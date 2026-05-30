<?php
$title = 'Home';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../admin/session.php';
require_once __DIR__ . '/../admin/db.php';
require_once __DIR__ . '/../admin/utils.php';


$username = trim($_GET['user'] ?? '');
$user = get_user($username);

if (!$user) {
    header('Location: /project-blog/index.php');
    exit('User not found');
}

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

$usernameSafe = htmlspecialchars($user['username'] ?? '');
$titleSafe = htmlspecialchars($user['title'] ?? '');
$presentationSafe = htmlspecialchars($user['presentation'] ?? '');
$profileImageSafe = htmlspecialchars($user['profile_image'] ?? 'https://via.placeholder.com/96');

$selectedTitle = htmlspecialchars($selectedPost['title'] ?? '');
$selectedCreatedAt = $selectedPost['created_at'] ?? '';
$selectedFilename = htmlspecialchars($selectedPost['filename'] ?? '');
$selectedContent = nl2br(htmlspecialchars($selectedPost['content'] ?? ''));

?>

<header class="border-b border-border">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <button type="button" onclick=" window.location.href = '/project-blog/index.php';"
            class="flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
            Back
        </button>
        <button class="flex items-center gap-2">
            <span style="font-family: 'Playfair Display', serif; font-weight: 700;" class="text-lg text-foreground">
                The Square
            </span>
        </button>
    </div>
</header>

<main class="max-w-6xl mx-auto px-6 py-8">
    <section class="mt-10 border-b border-border pb-10">
        <div class="flex items-center space-x-4 mb-6">
            <img src="<?php echo $profileImageSafe; ?>" alt="" class="w-40 h-40 rounded-full object-cover border">

            <div>
                <h1 class="text-3xl text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
                    <?php echo $usernameSafe; ?>
                </h1>

                <?php if (!empty($titleSafe)): ?>
                    <p class="text-sm text-muted-foreground" style="font-family: 'DM Sans', sans-serif;">
                        <?php echo $titleSafe; ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($presentationSafe)): ?>
                    <p class="text-base text-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
                        <?php echo $presentationSafe; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 grid-cols-[280px_1fr] gap-10 pb-16 mt-10">
        <!-- All posts -->
        <aside>
            <h2 class="text-lg text-foreground mb-4" style="font-family: 'Playfair Display', serif; font-weight: 600;">
                All posts</h2>
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

        <section>
            <?php if ($selectedPost): ?>
                <article class="border-b border-border pb-10">
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
        </section>
    </section>

</main>

</body>

</html>