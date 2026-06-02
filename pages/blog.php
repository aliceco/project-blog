<?php
$head = 'Blog';
require_once __DIR__ . '/../admin/session.php';
require_once __DIR__ . '/../admin/db.php';
require_once __DIR__ . '/../admin/utils.php';

$csrfToken = createCsrfToken();

$editProfileErrors = $_SESSION['editProfileErr'] ?? [];
$createPostErrors = $_SESSION['createPostErr'] ?? [];
$editPostErrors = $_SESSION['editPostErr'] ?? [];
$createPostOld = $_SESSION['createPostOld'] ?? [];
$editPostOld = $_SESSION['editPostOld'] ?? [];
$openModal = $_SESSION['openModal'] ?? '';

unset($_SESSION['editProfileErr'], $_SESSION['createPostErr'], $_SESSION['editPostErr'], $_SESSION['createPostOld'], $_SESSION['editPostOld'], $_SESSION['openModal']); // Resets temporary form state after load

$showEditProfileModal = $openModal === 'edit_profile';
$showCreatePostModal = $openModal === 'create_post';
$showEditPostModal = $openModal === 'edit_post';

$authorQuery = trim($_GET['author']);
$author = getUser($authorQuery);
if (!$author) { // check if auhtor page exists, if not redirect to home page
    header('Location: /project-blog/index.php');
    exit('User not found');
}

$sessionUserId = $_SESSION['user_id'];
$sessionUser = getUserById($sessionUserId);

$can_edit = isLoggedIn() && ($sessionUserId) === $author['id']; // Checks if sessionUser is owner of the profile

$uploadDir = __DIR__ . '/../uploads/';


// Edit profile form 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'edit_profile')) {
    //Authorizatison in PHP
    if (!$can_edit) {
        http_response_code(403);
        exit('Forbidden');
    }

    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $editProfileErrors['csrf'] = 'Invalid CSRF token.';
    } else {
        $input = [
            'firstname' => trim($_POST['firstname']),
            'lastname' => trim($_POST['lastname']),
            'username' => trim($_POST['username']),
            'title' => trim($_POST['title']),
            'bio' => trim($_POST['bio']),
            'profile_image' => $_FILES['profile_image'] ?? null
        ];

        $sessionFirstname = ($sessionUser['firstname']);
        $sessionLastname = ($sessionUser['lastname']);
        $sessionUsername = ($sessionUser['username']);
        $sessionTitle = ($sessionUser['title']);
        $sessionBio = ($sessionUser['bio']);
        $sessionImagePath = $sessionUser['profile_image'] ?? null;
        $profileImagePath = $sessionImagePath;

        $uploadedFile = validateOptionalImageUpload($input['profile_image'], 307200);

        $fileUploadSuccess = $uploadedFile['uploaded'];

        $changedFirstname = $input['firstname'] !== $sessionFirstname;
        $changedLastname = $input['lastname'] !== $sessionLastname;
        $changedUsername = $input['username'] !== $sessionUsername;
        $changedTitle = $input['title'] !== $sessionTitle;
        $changedBio = $input['bio'] !== $sessionBio;
        $hasChanges = $changedFirstname || $changedLastname || $changedUsername || $changedTitle || $changedBio || $fileUploadSuccess;

        if (!$uploadedFile['ok']) {
            $editProfileErrors['profile_image'] = $uploadedFile['error'];
        }

        if ($fileUploadSuccess && empty($editProfileErrors['profile_image'])) {
            $newFilename = 'profile_' . $sessionUserId . '_' . bin2hex(random_bytes(4)) . '.' . $uploadedFile['extension'];
            $profileImageDir = $uploadDir . 'profile_images/';
            if (!is_dir($profileImageDir) && !mkdir($profileImageDir, 0755, true)) {
                $editProfileErrors['profile_image'] = 'Upload folder could not be created.';
            } elseif (move_uploaded_file($input['profile_image']['tmp_name'], $profileImageDir . $newFilename)) {
                $profileImagePath = '/project-blog/uploads/profile_images/' . $newFilename;

            } else {
                $editProfileErrors['profile_image'] = 'Could not save uploaded file.';
            }
        }

        if ($input['firstname'] === '') {
            $editProfileErrors['firstname'] = 'First name is required.';
        } elseif ($input['lastname'] === '') {
            $editProfileErrors['lastname'] = 'Last name is required.';
        } elseif ($input['username'] === '') {
            $editProfileErrors['username'] = 'Username is required.';
        } elseif ($changedUsername && usernameExists($input['username'])) {
            $editProfileErrors['username'] = 'Username already exists.';
        } elseif (!$hasChanges) {
            $editProfileErrors['general'] = 'No changes made.';
        } else {
            $updatedUser = updateUserProfile($_SESSION['user_id'], $input['firstname'], $input['lastname'], $input['username'], $input['title'], $input['bio'], $profileImagePath);

            // Reload profile page after successful update
            if ($updatedUser > 0) {
                newCSRFToken();
                $_SESSION['username'] = $input['username'];
                $redirect = '/project-blog/pages/blog.php?author=' . urlencode($input['username']);
                $sessionPost = $_GET['post'] ?? 0;

                if ($sessionPost > 0) {
                    $redirect .= '&post=' . $sessionPost;
                }
                header('Location: ' . $redirect);

                exit();
            }

            $editProfileErrors['general'] = $updatedUser === 0
                ? 'No changes were saved.'
                : 'Could not save changes. Please try again.';
        }
    }

    if (!empty($editProfileErrors)) {
        $_SESSION['editProfileErr'] = $editProfileErrors;
        $_SESSION['openModal'] = 'edit_profile';

        $redirect = '/project-blog/pages/blog.php?author=' . urlencode($author['username']);
        $sessionPost = $_GET['post'] ?? 0;
        if ($sessionPost > 0) {
            $redirect .= '&post=' . urlencode((string) $sessionPost);
        }

        header('Location: ' . $redirect);
        exit();
    }
}

// Create new post form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action']) === 'create_post')) {
    // Authorizatison in PHP
    if (!$can_edit) {
        http_response_code(403);
        exit('Forbidden');
    }

    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $createPostErrors['csrf'] = 'Invalid CSRF token.';
    } else {
        $postTitle = trim($_POST['title']);
        $postContent = trim($_POST['content']);
        $postImage = $_FILES['post-image'] ?? null;

        // First validate image
        $uploadedFile = validateOptionalImageUpload($postImage, 409600);
        // save whether uploaded or not
        $fileUploadSuccess = $uploadedFile['uploaded'];
        $postImagePath = null;

        if (!$uploadedFile['ok']) {
            $createPostErrors['post-image'] = $uploadedFile['error'];
        }

        if ($fileUploadSuccess && empty($createPostErrors['post-image'])) {
            $newFilename = 'post' . $sessionUserId . '_' . bin2hex(random_bytes(4)) . '.' . $uploadedFile['extension'];
            $postImageDir = $uploadDir . 'post_images/';

            if (!is_dir($postImageDir) && !mkdir($postImageDir, 0755, true)) {
                $createPostErrors['post-image'] = 'Upload folder could not be created.';
            } elseif (move_uploaded_file($postImage['tmp_name'], $postImageDir . $newFilename)) {
                $postImagePath = '/project-blog/uploads/post_images/' . $newFilename;

            } else {
                $createPostErrors['post-image'] = 'Could not save uploaded file.';
            }
        }


        if ($postTitle === '') {
            $createPostErrors['title'] = 'Title is required.';
        } elseif ($postContent === '') {
            $createPostErrors['content'] = 'Content is required.';
        } else {
            $createdPostID = addPost($_SESSION['user_id'], $postTitle, $postContent, $postImagePath);

            // Reload and redirect to updated profile page with the new created post selected
            if ($createdPostID) {
                newCSRFToken();
                $redirect = '/project-blog/pages/blog.php?author=' . urlencode($sessionUser['username']) . '&post=' . $createdPostID;
                header('Location: ' . $redirect);
                exit();
            }
            $createPostErrors['general'] = 'Could not create post. Please try again.';
        }
    }

    if (!empty($createPostErrors)) {
        $_SESSION['createPostErr'] = $createPostErrors;
        $_SESSION['createPostOld'] = [
            'title' => trim($_POST['title'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
        ];
        $_SESSION['openModal'] = 'create_post';

        $redirect = '/project-blog/pages/blog.php?author=' . urlencode($author['username']);
        $sessionPost = $_GET['post'] ?? 0;
        if ($sessionPost > 0) {
            $redirect .= '&post=' . urlencode((string) $sessionPost);
        }

        header('Location: ' . $redirect);
        exit();
    }
}


// Edit post form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'edit_post')) {
    // Authorizatison in PHP
    if (!$can_edit) {
        http_response_code(403);
        exit('Forbidden');
    }

    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $editPostErrors['csrf'] = 'Invalid CSRF token.';
    } else {
        $postImagePath = NULL;
        $postId = ($_POST['post_id'] ?? 0);
        $titleInput = trim($_POST['title'] ?? '');
        $contentInput = trim($_POST['content'] ?? '');
        $imageInput = $_FILES['post-image'] ?? null;

        $uploadedFile = validateOptionalImageUpload($imageInput, 409600);

        $fileUploadSuccess = $uploadedFile['uploaded'];

        if (!$uploadedFile['ok']) {
            $editPostErrors['post-image'] = $uploadedFile['error'];
        }
        if ($fileUploadSuccess && empty($editPostErrors['post-image'])) {
            $newFilename = 'post' . $sessionUserId . '_' . bin2hex(random_bytes(4)) . '.' . $uploadedFile['extension'];
            $postImageDir = $uploadDir . 'post_images/';

            if (!is_dir($postImageDir) && !mkdir($postImageDir, 0755, true)) {
                $editPostErrors['post-image'] = 'Upload folder could not be created.';
            } elseif (move_uploaded_file($imageInput['tmp_name'], $postImageDir . $newFilename)) {
                $postImagePath = '/project-blog/uploads/post_images/' . $newFilename;

            } else {

                $editPostErrors['post-image'] = 'Could not save uploaded file.';
            }
        }


        if ($titleInput === '') {
            $editPostErrors['title'] = 'Title is required.';
        } elseif ($contentInput === '') {
            $editPostErrors['content'] = 'Content is required.';
        } else {
            $updatedPost = updatePost($postId, $_SESSION['user_id'], $titleInput, $contentInput, $postImagePath);

            // Reload and redirect to updated profile page with the updated post selected
            if ($updatedPost > 0) {
                newCSRFToken();
                $redirect = '/project-blog/pages/blog.php?author=' . urlencode($sessionUser['username']) . '&post=' . $postId;
                header('Location: ' . $redirect);
                exit();
            }

            if ($updatedPost === 0) {
                $editPostErrors['general'] = 'No changes made.';
            } else {
                $editPostErrors['general'] = 'Could not update post. Please try again.';
            }
        }
    }

    if (!empty($editPostErrors)) {
        $_SESSION['editPostErr'] = $editPostErrors;
        $_SESSION['editPostOld'] = [
            'post_id' => (int) ($_POST['post_id'] ?? 0),
            'title' => trim($_POST['title'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
        ];
        $_SESSION['openModal'] = 'edit_post';

        $redirect = '/project-blog/pages/blog.php?author=' . urlencode($author['username']);
        $sessionPost = (int) ($_POST['post_id'] ?? 0);
        if ($sessionPost > 0) {
            $redirect .= '&post=' . urlencode((string) $sessionPost);
        }

        header('Location: ' . $redirect);
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'deleted_post')) {
    // Authorizatison in PHP
    if (!$can_edit) {
        http_response_code(403);
        exit('Forbidden');
    }

    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $errors['csrf'] = 'Invalid CSRF token.';
    } else {
        $postId = $_POST['post_id'];

        if ($postId <= 0) {
            $errors['general'] = 'Invalid post ID.';
        } else {
            $deleted = deletePost($postId, $_SESSION['user_id']);

            if ($deleted) {

                $redirect = '/project-blog/pages/blog.php?author=' . urlencode($sessionUser['username']) . '&updated=1';
                header('Location: ' . $redirect);
                newCSRFToken();
                exit();
            }

            $errors['general'] = 'Could not delete post. Please try again.';
        }
    }
}

$posts = getPostsByUser($author['id']);

$selectedPostId = $_GET['post'] ?? 0; // gets the selected post ID from query parameter
$selectedPost = null;

if ($selectedPostId > 0) {
    // Finds the post that the user clicked on
    foreach ($posts as $post) {
        if (($post['id'] ?? 0) == $selectedPostId) {
            $selectedPost = $post;
            break;
        }
    }
}

// Sets the first post as selected if no postID is provided in query
if ($selectedPost === null && !empty($posts)) {
    $selectedPost = $posts[0];
    $selectedPostId = $selectedPost['id'] ?? 0;
}

// Variables for for user data
$firstname = htmlspecialchars($author['firstname']);
$lastname = htmlspecialchars($author['lastname']);
$username = htmlspecialchars($author['username']);
$title = htmlspecialchars($author['title']);
$bio = htmlspecialchars($author['bio']);
$profileImage = htmlspecialchars($author['profile_image'] ?? '/project-blog/images/default-avatar.jpg');

// Variables for for post data
$selectedTitle = htmlspecialchars($selectedPost['title'] ?? '');
$selectedCreatedAt = $selectedPost['created_at'] ?? '';
$selectedFilename = htmlspecialchars($selectedPost['filename'] ?? '');
$selectedContent = nl2br(htmlspecialchars($selectedPost['content'] ?? ''));
$selectedPostImagePath = $selectedPost['image_path'] ?? null;

require_once __DIR__ . '/../includes/document-head.php';
require_once __DIR__ . '/../components/navbar.php';
?>

<main class="max-w-6xl mx-auto px-6 py-8">
    <section class="mt-10 border-b border-border pb-10">
        <div class="flex items-center space-x-4 mb-6">
            <img src="<?= $profileImage ?>" alt="" class="w-40 h-40 rounded-full object-cover border">
            <div>
                <div class="flex flex-row gap-2">
                    <h1 class="text-3xl text-foreground"
                        style="font-family: 'Playfair Display', serif; font-weight: 600;">
                        <?= $firstname ?>
                    </h1>
                    <h1 class="text-3xl text-foreground"
                        style="font-family: 'Playfair Display', serif; font-weight: 600;">
                        <?= $lastname ?>
                    </h1>
                </div>
                <h2>
                    <?= $username ?>
                </h2>

                <?php if (!empty($title)): ?>
                    <p class="text-sm text-muted-foreground" style="font-family: 'DM Sans', sans-serif;">
                        <?= $title ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($bio)): ?>
                    <p class="text-base text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                        <?= $bio ?>
                    </p>
                <?php endif; ?>
                <?php if ($can_edit): ?>
                    <button id="open-edit-profile" type="button"
                        class="text-xs mt-4 px-3 py-2 bg-secondary text-secondary-foreground rounded-md hover:opacity-90 transition-opacity cursor-pointer">
                        Edit profile
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if ($can_edit) {
        require_once __DIR__ . '/../components/edit-profile.php';
    }
    ?>

    <section class="grid grid-cols-1 grid-cols-[280px_1fr] gap-10 pb-16 mt-10">
        <!-- All posts -->
        <aside>
            <div class="flex flex-row items-baseline justify-between mb-2">
                <h1 class="text-lg text-foreground" style="font-family: 'DM Sans', sans-serif;">
                    All posts</h1>
                <?php if ($can_edit): ?>
                    <button class="text-sm text-accent hover:opacity-80 cursor-pointer" id="open-create-post">+ New</button>
                <?php endif; ?>
            </div>
            <?php foreach ($posts as $post): ?>
                <?php
                $postId = $post['id'] ?? 0;
                $postTitle = htmlspecialchars($post['title'] ?? 'Untitled');
                $isActive = ((string) $postId) === ((string) $selectedPostId);
                $postCreatedAt = $post['created_at'] ?? '';
                $postUrl = '/project-blog/pages/blog.php?author=' . urlencode($author['username']) . '&post=' . $postId;
                $postWrapperClass = $isActive
                    ? 'group relative rounded-md border border-accent bg-accent/10 transition-all mb-2'
                    : 'group relative rounded-md border border-transparent hover:border-border hover:bg-card transition-all mb-2';
                $postTitleClass = $isActive
                    ? 'text-sm mb-1 transition-colors text-accent'
                    : 'text-sm mb-1 transition-colors text-foreground group-hover:text-accent';
                ?>
                <div class="<?= $postWrapperClass ?>">
                    <a href="<?= $postUrl ?>" class="block w-full text-left px-3 py-2">
                        <h1 class="<?= $postTitleClass ?>"
                            style="font-family: 'Playfair Display', serif; font-weight: 600;"><?= $postTitle ?></h1>
                        <?php if (!empty($postCreatedAt)): ?>
                            <h2 class="text-xs text-muted-foreground" style="font-family: 'DM Sans', sans-serif;">
                                <?= htmlspecialchars(readableDate($postCreatedAt)) ?>
                            </h2>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endforeach; ?>

        </aside>

        <?php if ($can_edit) {
            require_once __DIR__ . '/../components/create-post-modal.php';
        }
        ?>

        <section class="border-b border-border pb-10">
            <?php if ($can_edit): ?>
                <div class="flex justify-end">
                    <button id="open-create-post-secondary"
                        class="text-sm mb-4 px-3 py-2 bg-accent text-primary-foreground rounded-md hover:opacity-90 transition-opacity cursor-pointer">
                        + Create new post
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($selectedPost): ?>
                <article>
                    <?php if (!empty($selectedTitle)): ?>
                        <h1 class="text-4xl text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
                            <?= $selectedTitle ?>
                        </h1>
                    <?php endif; ?>
                    <?php if (!empty($selectedCreatedAt)): ?>
                        <h2 class="text-xs text-foreground" style="font-family: 'DM Sans', sans-serif;">
                            <?= htmlspecialchars(readableDate($selectedCreatedAt)) ?>
                        </h2>
                    <?php endif; ?>
                    <?php if (!empty($selectedFilename)): ?>
                        <img src="/project-blog/uploads/<?= $selectedFilename ?>" alt="<?= $selectedTitle ?: 'Post image' ?>"
                            class="w-full h-auto rounded-lg my-4">
                    <?php endif; ?>
                    <?php if (!empty($selectedPostImagePath)): ?>
                        <img src="<?= $selectedPostImagePath ?>" alt="<?= $selectedTitle ?: 'Post image' ?>"
                            class="w-100 h-auto rounded-lg my-4">
                    <?php endif; ?>
                    <?php if (!empty($selectedContent)): ?>
                        <p class="text-base text-foreground leading-relaxed mt-6" style="font-family: 'DM Sans', sans-serif;">
                            <?= $selectedContent ?>
                        </p>
                    <?php endif; ?>
                </article>
            <?php else: ?>
                <p class="text-muted-foreground">No posts yet.</p>
            <?php endif; ?>

            <?php if ($can_edit && $selectedPost): ?>
                <div class="flex justify-end mt-4 gap-2">
                    <button id="open-edit-post"
                        class="text-xs px-2 py-1 border border-primary text-secondary-foreground rounded-md hover:bg-primary/10 transition-colors cursor-pointer">
                        Edit post
                    </button>
                    <button id="open-delete-modal"
                        class="text-xs px-2 py-1 border border-destructive text-destructive rounded-md hover:bg-destructive/10 transition-colors cursor-pointer">
                        Delete
                    </button>

                </div>
            <?php endif; ?>

            <?php if ($can_edit) {
                require_once __DIR__ . '/../components/edit-post.php';
            }
            ?>
            <?php if ($can_edit) {
                require_once __DIR__ . '/../components/delete-modal.php';
            }
            ?>
        </section>
    </section>

</main>

<?php if ($can_edit): ?>
    <script>
        function setupModal({ modal, openButtons = [], closeButtons = [], backdrop = null, autoOpen = false }) {
            if (!modal) return { open: () => { }, close: () => { } };

            const open = () => {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const close = () => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            openButtons.forEach((btn) => btn && btn.addEventListener('click', open));
            closeButtons.forEach((btn) => btn && btn.addEventListener('click', close));
            if (backdrop) backdrop.addEventListener('click', close);

            if (autoOpen) open();

            return { open, close };
        }

        const editProfileModal = setupModal({
            modal: document.getElementById('edit-profile'),
            openButtons: [document.getElementById('open-edit-profile')],
            closeButtons: [
                document.getElementById('close-edit-profile'),
                document.getElementById('cancel-edit-profile')
            ],
            backdrop: document.getElementById('edit-profile-backdrop'),
            autoOpen: <?= $showEditProfileModal ? 'true' : 'false' ?>
        });

        const createPostModal = setupModal({
            modal: document.getElementById('create-post'),
            openButtons: [
                document.getElementById('open-create-post'),
                document.getElementById('open-create-post-secondary')
            ],
            closeButtons: [
                document.getElementById('close-create-post'),
                document.getElementById('cancel-create-post')
            ],
            backdrop: document.getElementById('create-post-backdrop'),
            autoOpen: <?= $showCreatePostModal ? 'true' : 'false' ?>
        });

        const editPostModal = setupModal({
            modal: document.getElementById('edit-post'),
            openButtons: [
                document.getElementById('open-edit-post'),
                // or multiple triggers if each post has one
            ],
            closeButtons: [
                document.getElementById('close-edit-post'),
                document.getElementById('cancel-edit-post')
            ],
            backdrop: document.getElementById('edit-post-backdrop'),
            autoOpen: <?= $showEditPostModal ? 'true' : 'false' ?>
        });

        const deleteModal = setupModal({
            modal: document.getElementById('delete-modal'),
            openButtons: [document.getElementById('open-delete-modal')],
            closeButtons: [
                document.getElementById('close-delete-post'),
                document.getElementById('cancel-delete-post')
            ],
            backdrop: document.getElementById('delete-backdrop'),
        })

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                editProfileModal.close();
                createPostModal.close();
                editPostModal.close();
            }
        });
    </script>

<?php endif; ?>

</body>

</html>
