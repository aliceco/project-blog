<?php
$head = 'Home';
require_once __DIR__ . '/admin/session.php';
require_once __DIR__ . '/admin/db.php';
require_once __DIR__ . '/admin/utils.php';

echo 'Session ID: ' . session_id();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';


require_once __DIR__ . '/includes/document-head.php';
require_once __DIR__ . '/components/navbar.php';

$posts = getPostsSorted();
$featuredPost = $posts[0]; // Get the most recent post as the featured post
$featuredPuthor = getUserById($featuredPost['user_id']);
$featuredPostDate = readableDate($featuredPost['created_at']);
$featuredUrl = '/project-blog/pages/blog.php?author=' . urlencode($featuredPuthor['username']) . '&post=' . $featuredPost['id'];
$authors = getUsers();


?>

<main class="max-w-6xl mx-auto px-6 py-8">
  <!-- Latest page -->
  <section class="mt-8 flex flex-row gap-8 min-h-[400px] border-b border-border">


    <div class="w-2/3 flex flex-col my-auto">
      <h1 class="text-md" style="font-family: 'DM Sans', sans-serif; font-weight: 600;" id="featured">Featured</h1>
      <h2 class="text-5xl"><a href="<?= htmlspecialchars($featuredUrl) ?>"
          class="text-foreground hover:text-accent transition-colors"><?= htmlspecialchars($featuredPost['title']) ?></a>
      </h2>
      <p class="text-lg text-muted-foreground">
        <?= htmlspecialchars(substr($featuredPost['content'], 0, 100) . '...'); ?>
      </p>
      <div class="flex items-center gap-4 my-4">
        <p class="text-sm font-medium"><?= htmlspecialchars($featuredPuthor['username']) ?></p>
        <p class="text-xs text-muted-foreground"><?= htmlspecialchars($featuredPostDate) ?></p>
      </div>
      <a href="<?= htmlspecialchars($featuredUrl) ?>"
        class="text-accent hover:text-primary transition-colors">Read more -></a>
    </div>
    <div class="w-1/3">
      <img src="/project-blog/images/default-avatar.jpg" alt="Blog post image" class="w-full h-auto rounded-lg my-4">
    </div>
  </section>

  <section id="g" class="py-12 border-b border-border">
    <h1 class="text-md text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
      Latest writing
    </h1>
    <div class="divide-y divide-border">
      <?php foreach ($posts as $post): ?>
        <?php include __DIR__ . '/components/post-preview.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>

  <?php
  include __DIR__ . '/components/author-list.php';
  ?>
</main>






</body>

</html>
