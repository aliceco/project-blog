<?php
$title = 'Home';
require_once __DIR__ . '/../admin/session.php';
require_once __DIR__ . '/../admin/db.php';
require_once __DIR__ . '/../admin/utils.php';

$errors = [];
$success = '';
$showEditModal = false;
$showCreatePostModal = false;

$csrfToken = create_csrf_token();

$username = trim($_GET['user'] ?? '');
$user = get_user($username);

if (!$user) {
    header('Location: /project-blog/index.php');
    exit('User not found');
}

$can_edit = is_logged_in() && (int) ($_SESSION['user_id'] ?? 0) === (int) $user['id'];

// Edit profile form handlning
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'edit_profile')) {
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $errors['csrf'] = 'Invalid CSRF token.';
    } else {
        $usernameInput = trim($_POST['username']);
        $titleInput = trim($_POST['title']);
        $presentationInput = trim($_POST['presentation']);

        $currentUsername = (string) ($user['username']);
        $currentTitle = (string) ($user['title']);
        $currentPresentation = (string) ($user['presentation']);

        $changedUsername = $usernameInput !== $currentUsername;
        $changedTitle = $titleInput !== $currentTitle;
        $changedPresentation = $presentationInput !== $currentPresentation;
        $hasChanges = $changedUsername || $changedTitle || $changedPresentation;

        if ($usernameInput === '') {
            $errors['username'] = 'Username is required.';
        } elseif ($changedUsername && username_exists($usernameInput)) {
            $errors['username'] = 'Username already exists.';
        } else {
            $updatedUsers = update_user_profile((int) $_SESSION['user_id'], $usernameInput, $titleInput, $presentationInput);

            // Redirect to updated profile page after successful update
            if ($updatedUsers) {
                $_SESSION['username'] = $usernameInput;
                $redirect = '/project-blog/pages/blog.php?user=' . urlencode($usernameInput) . '&updated=1';
                $currentPost = (int) ($_GET['post'] ?? 0);

                if ($currentPost > 0) {
                    $redirect .= '&post=' . $currentPost;
                }
                header('Location: ' . $redirect);
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'create_post')) {
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
            $createdPost = add_post((int) $_SESSION['user_id'], $postTitle, $postContent);

            // Redirect to updated profile page with the new created post selected

            if ($createdPost) {
                $redirect = '/project-blog/pages/blog.php?user=' . urlencode((string) $user['username']) . '&post=' . (int) $createdPost;
                header('Location: ' . $redirect);
                exit();
            }
            $errors['general'] = 'Could not create post. Please try again.';
        }
    }

    if (!empty($errors)) {
        $showCreatePostModal = true;
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'deleted_post')) {
    if (!$can_edit) {
        $errors['general'] = 'You are not allowed to delete this post.';
    } elseif (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $errors['csrf'] = 'Invalid CSRF token.';
    } else {
        $postId = (int) ($_POST['post_id'] ?? 0);

        if ($postId <= 0) {
            $errors['general'] = 'Invalid post ID.';
        } else {
            $deleted = delete_post($postId, (int) $_SESSION['user_id']);

            if ($deleted) {
                $redirect = '/project-blog/pages/blog.php?user=' . urlencode((string) $user['username']) . '&updated=1';
                header('Location: ' . $redirect);
                exit();
            }

            $errors['general'] = 'Could not delete post. Please try again.';
        }
    }
}


$username = trim($_GET['user'] ?? '');
$user = get_user($username);

if (!$user) {
    header('Location: /project-blog/index.php');
    exit('User not found');
}

$can_edit = is_logged_in() && (int) ($_SESSION['user_id'] ?? 0) === (int) $user['id'];

$posts = get_posts_by_user((int) $user['id']);

$selectedPostId = (int) ($_GET['post'] ?? 0); // gets the selected post ID from query parameter
$selectedPost = null;

if ($selectedPostId > 0) {
    // Finds the post that the user clicked on
    foreach ($posts as $post) {
        if ((int) ($post['id'] ?? 0) === $selectedPostId) {
            $selectedPost = $post;
            break;
        }
    }
}

// Sets the first post as selected if no postID is provided in query
if ($selectedPost === null && !empty($posts)) {
    $selectedPost = $posts[0];
    $selectedPostId = (int) ($selectedPost['id'] ?? 0);
}

// Variables for for user data
$username = htmlspecialchars($user['username'] ?? '');
$title = htmlspecialchars($user['title'] ?? '');
$presentation = htmlspecialchars($user['presentation'] ?? '');
$profileImage = htmlspecialchars($user['profile_image'] ?? '/project-blog/images/default-avatar.jpg');

// Variables for for post data
$selectedTitle = htmlspecialchars($selectedPost['title'] ?? '');
$selectedCreatedAt = $selectedPost['created_at'] ?? '';
$selectedFilename = htmlspecialchars($selectedPost['filename'] ?? '');
$selectedContent = nl2br(htmlspecialchars($selectedPost['content'] ?? ''));
$profileUpdated = (($_GET['updated'] ?? '') === '1');

require_once __DIR__ . '/../includes/document_head.php';
require_once __DIR__ . '/../components/navbar.php';
?>

<main class="max-w-6xl mx-auto px-6 py-8">
    <section class="mt-10 border-b border-border pb-10">
        <div class="flex items-center space-x-4 mb-6">
            <img src="<?php echo $profileImage; ?>" alt="" class="w-40 h-40 rounded-full object-cover border">
            <div>
                <h1 class="text-3xl text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
                    <?php echo $username; ?>
                </h1>

                <?php if (!empty($title)): ?>
                    <p class="text-sm text-muted-foreground" style="font-family: 'DM Sans', sans-serif;">
                        <?php echo $title; ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($presentation)): ?>
                    <p class="text-base text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                        <?php echo $presentation; ?>
                    </p>
                <?php endif; ?>
                <?php if ($can_edit): ?>
                    <button id="openEditProfileModal" type="button"
                        class="text-xs mt-4 px-3 py-2 bg-secondary text-secondary-foreground rounded-md hover:opacity-90 transition-opacity cursor-pointer">
                        Edit profile
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if ($can_edit) {
        require_once __DIR__ . '/../components/edit_profile.php';
    }
    ?>

    <section class="grid grid-cols-1 grid-cols-[280px_1fr] gap-10 pb-16 mt-10">
        <!-- All posts -->
        <aside>
            <div class="flex flex-row items-baseline justify-between mb-2">
                <h2 class="text-lg text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
                    All posts</h2>
                <?php if($can_edit): ?>
                    <button class="text-sm text-accent hover:opacity-80 cursor-pointer" id="openCreatePostModal">+ New</button>
                <?php endif; ?>
            </div>
            <?php foreach ($posts as $post): ?>
                <?php
                $postId = (int) ($post['id'] ?? 0);
                $postTitle = htmlspecialchars($post['title'] ?? 'Untitled');
                $isActive = $postId === $selectedPostId;
                $postCreatedAt = $post['created_at'] ?? '';
                $postUrl = '/project-blog/pages/blog.php?user=' . urlencode($user['username']) . '&post=' . $postId;
                $postLinkClass = $isActive
                    ? 'block text-sm mb-2 px-3 py-2 rounded-md border border-accent text-foreground bg-card'
                    : 'block text-sm mb-2 px-3 py-2 rounded-md border border-border text-muted-foreground hover:text-foreground';
                ?>
                <a href="<?php echo $postUrl; ?>" class="<?php echo $postLinkClass; ?>">
                    <span class="block"><?php echo $postTitle; ?></span>
                    <?php if (!empty($postCreatedAt)): ?>
                        <h2 class="text-xs text-foreground" style="font-family: 'DM Sans', sans-serif;">
                            <?php echo htmlspecialchars(readable_date($postCreatedAt)); ?>
                        </h2>
                    <?php endif; ?>

                </a>

            <?php endforeach; ?>

        </aside>

        <?php if ($can_edit) {
            require_once __DIR__ . '/../components/create_post_modal.php';
        }
        ?>

        <section class="border-b border-border pb-10">
            <?php if ($can_edit): ?>
                <div class="flex justify-end">
                    <button
                        id="openCreatePostModalSecondary" class="text-sm mb-4 px-3 py-2 bg-accent text-primary-foreground rounded-md hover:opacity-90 transition-opacity cursor-pointer">
                        Create new post
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($selectedPost): ?>
                <article>
                    <?php if (!empty($selectedTitle)): ?>
                        <h1 class="text-4xl text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
                            <?php echo $selectedTitle; ?>
                        </h1>
                    <?php endif; ?>
                    <?php if (!empty($selectedCreatedAt)): ?>
                        <h2 class="text-xs text-foreground" style="font-family: 'DM Sans', sans-serif;">
                            <?php echo htmlspecialchars(readable_date($selectedCreatedAt)); ?>
                        </h2>
                    <?php endif; ?>
                    <?php if (!empty($selectedFilename)): ?>
                        <img src="/project-blog/uploads/<?php echo $selectedFilename; ?>"
                            alt="<?php echo $selectedTitle ?: 'Post image'; ?>" class="w-full h-auto rounded-lg my-4">
                    <?php endif; ?>
                    <?php if (!empty($selectedContent)): ?>
                        <p class="text-base text-foreground leading-relaxed mt-6" style="font-family: 'DM Sans', sans-serif;">
                            <?php echo $selectedContent; ?>
                        </p>
                    <?php endif; ?>
                </article>
            <?php else: ?>
                <p class="text-muted-foreground">No posts yet.</p>
            <?php endif; ?>
            <?php if ($can_edit): ?>
                <div class="flex justify-end mt-4 gap-2">
                    <button
                        class="text-xs px-2 py-1 border border-primary text-secondary-foreground rounded-md hover:bg-primary/10 transition-colors cursor-pointer">
                        Edit post
                    </button>
                    <form method="POST">
                        <input type="hidden" name="post_id" value="<?php echo (int) $selectedPostId; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
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
        const editProfileModal = document.getElementById('editProfileModal');
        const openEditProfileModal = document.getElementById('openEditProfileModal');
        const closeEditProfileModal = document.getElementById('closeEditProfileModal');
        const cancelEditProfileModal = document.getElementById('cancelEditProfileModal');
        const editProfileBackdrop = document.getElementById('editProfileBackdrop');

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

        const createPostModal = document.getElementById('createPostModal');
        const openCreatePostModal = document.getElementById('openCreatePostModal');
        const openCreatePostModalSecondary = document.getElementById('openCreatePostModalSecondary');
        const closeCreatePostModal = document.getElementById('closeCreatePostModal');
        const cancelCreatePostModal = document.getElementById('cancelCreatePostModal');
        const createPostBackdrop = document.getElementById('createPostBackdrop');

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
