<?php
$title = 'Home';
require_once __DIR__ . '/../admin/session.php';
require_once __DIR__ . '/../admin/db.php';
require_once __DIR__ . '/../admin/utils.php';

$csrfToken = createCsrfToken();

$errors = [];
$showEditModal = false;
$showCreatePostModal = false;


$authorQuery = trim($_GET['author']);
$author = getUser($authorQuery);
if (!$author) { // check if auhtor page exists, if not redirect to home page
    header('Location: /project-blog/index.php');
    exit('User not found');
}

$sessionUserId = $_SESSION['user_id'];
$sessionUser = getUserById($sessionUserId);

$can_edit = isLoggedIn() && ($sessionUserId) === $author['id']; // Checks if sessionUser is owner of the profile

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
        $errors['csrf'] = 'Invalid CSRF token.';
    } else {
        $input = [
            'firstname' => trim($_POST['firstname']),
            'lastname' => trim($_POST['lastname']),
            'username' => trim($_POST['username']),
            'title' => trim($_POST['title']),
            'bio' => trim($_POST['bio']),
        ];

        $sessionFirstname = ($sessionUser['firstname']);
        $sessionLastname = ($sessionUser['lastname']);
        $sessionUsername = ($sessionUser['username']);
        $sessionTitle = ($sessionUser['title']);
        $sessionBio = ($sessionUser['bio']);

        $changedFirstname = $input['firstname'] !== $sessionFirstname;
        $changedLastname = $input['lastname'] !== $sessionLastname;
        $changedUsername = $input['username'] !== $sessionUsername;
        $changedTitle = $input['title'] !== $sessionTitle;
        $changedBio = $input['bio'] !== $sessionBio;
        $hasChanges = $changedFirstname || $changedLastname || $changedUsername || $changedTitle || $changedBio;



        if ($input['firstname'] === '') {
            $errors['firstname'] = 'First name is required.';
        } elseif ($input['lastname'] === '') {
            $errors['lastname'] = 'Last name is required.';
        } elseif ($input['username'] === '') {
            $errors['username'] = 'Username is required.';
        } elseif ($changedUsername && usernameExists($input['username'])) {
            $errors['username'] = 'Username already exists.';
        } else {
            $updatedUser = updateUserProfile($_SESSION['user_id'], $input['firstname'], $input['lastname'], $input['username'], $input['title'], $input['bio']);

            // Reload profile page after successful update
            if ($updatedUser !== false) {
                $_SESSION['username'] = $input['username'];
                $redirect = '/project-blog/pages/blog.php?author=' . urlencode($input['username']);
                $sessionPost = $_GET['post'] ?? 0;

                if ($sessionPost > 0) {
                    $redirect .= '&post=' . $sessionPost;
                }
                header('Location: ' . $redirect);
                newCSRFToken();

                exit();
            }

            $errors['general'] = 'Could not save changes. Please try again.';
        }
    }

    if (!empty($errors)) {
        $showEditModal = true;
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
        $errors['csrf'] = 'Invalid CSRF token.';
    } else {
        $postTitle = trim($_POST['title']);
        $postContent = trim($_POST['content']);

        if ($postTitle === '') {
            $errors['title'] = 'Title is required.';
        } elseif ($postContent === '') {
            $errors['content'] = 'Content is required.';
        } else {
            $createdPost = addPost($_SESSION['user_id'], $postTitle, $postContent);

            // Reload and redirect to updated profile page with the new created post selected
            if ($createdPost) {
                $redirect = '/project-blog/pages/blog.php?author=' . urlencode($sessionUser['username']) . '&post=' . $createdPost;
                header('Location: ' . $redirect);
                newCSRFToken();
                exit();
            }
            $errors['general'] = 'Could not create post. Please try again.';
        }
    }

    if (!empty($errors)) {
        $showCreatePostModal = true;
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
$firstname = htmlspecialchars($author['firstname'] ?? '');
$lastname = htmlspecialchars($author['lastname'] ?? '');
$username = htmlspecialchars($author['username'] ?? '');
$title = htmlspecialchars($author['title'] ?? '');
$bio = htmlspecialchars($author['bio'] ?? '');
$profileImage = htmlspecialchars($author['profile_image'] ?? '/project-blog/images/default-avatar.jpg');

// Variables for for post data
$selectedTitle = htmlspecialchars($selectedPost['title'] ?? '');
$selectedCreatedAt = $selectedPost['created_at'] ?? '';
$selectedFilename = htmlspecialchars($selectedPost['filename'] ?? '');
$selectedContent = nl2br(htmlspecialchars($selectedPost['content'] ?? ''));

require_once __DIR__ . '/../includes/document-head.php';
require_once __DIR__ . '/../components/navbar.php';
?>

<main class="max-w-6xl mx-auto px-6 py-8">
    <section class="mt-10 border-b border-border pb-10">
        <div class="flex items-center space-x-4 mb-6">
            <img src="<?= $profileImage ?>" alt="" class="w-40 h-40 rounded-full object-cover border">
            <div>
                <h1 class="text-3xl text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
                    <?= $username ?>
                </h1>

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
        <aside >
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
                        <h1 class="<?= $postTitleClass ?>" style="font-family: 'Playfair Display', serif; font-weight: 600;"><?= $postTitle ?></h1>
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
                    <button
                        class="text-xs px-2 py-1 border border-primary text-secondary-foreground rounded-md hover:bg-primary/10 transition-colors cursor-pointer">
                        Edit post
                    </button>
                    <form method="POST">
                        <input type="hidden" name="post_id" value="<?= htmlspecialchars($selectedPostId) ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <button type="submit" name="action" value="deleted_post"
                            class="text-xs px-2 py-1 border border-destructive text-destructive rounded-md hover:bg-destructive/10 transition-colors cursor-pointer">
                            Delete
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </section>
    </section>

</main>

<?php if ($can_edit): ?>
    <script>
        const editProfileModal = document.getElementById('edit-profile');
        const openEditProfileModal = document.getElementById('open-edit-profile');
        const closeEditProfileModal = document.getElementById('close-edit-profile');
        const cancelEditProfileModal = document.getElementById('cancel-edit-profile');
        const editProfileBackdrop = document.getElementById('edit-profile-backdrop');

        function openProfileModal() {
            if (!editProfileModal) return;
            editProfileModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeProfileModal() {
            if (!editProfileModal) return;
            editProfileModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        if (openEditProfileModal) openEditProfileModal.addEventListener('click', openProfileModal);
        if (closeEditProfileModal) closeEditProfileModal.addEventListener('click', closeProfileModal);
        if (cancelEditProfileModal) cancelEditProfileModal.addEventListener('click', closeProfileModal);
        if (editProfileBackdrop) editProfileBackdrop.addEventListener('click', closeProfileModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeProfileModal();
        });

        <?php if ($showEditModal): ?>
            openProfileModal();
        <?php endif; ?>

        const createPostModal = document.getElementById('create-post');
        const openCreatePostModal = document.getElementById('open-create-post');
        const openCreatePostModalSecondary = document.getElementById('open-create-post-secondary');
        const closeCreatePostModal = document.getElementById('close-create-post');
        const cancelCreatePostModal = document.getElementById('cancel-create-post');
        const createPostBackdrop = document.getElementById('create-post-backdrop');

        function openPostModal() {
            createPostModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closePostModal() {
            createPostModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        if (openCreatePostModal) openCreatePostModal.addEventListener('click', openPostModal);
        if (openCreatePostModalSecondary) openCreatePostModalSecondary.addEventListener('click', openPostModal);
        if (closeCreatePostModal) closeCreatePostModal.addEventListener('click', closePostModal);
        if (cancelCreatePostModal) cancelCreatePostModal.addEventListener('click', closePostModal);
        if (createPostBackdrop) createPostBackdrop.addEventListener('click', closePostModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closePostModal();
        });

        <?php if ($showCreatePostModal): ?>
            openPostModal();
        <?php endif; ?>

    </script>
<?php endif; ?>

</body>

</html>
