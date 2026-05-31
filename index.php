<?php
$title = 'Home';
require_once __DIR__ . '/admin/session.php';
require_once __DIR__ . '/admin/db.php';

$username = $_SESSION['username'] ?? null;

echo 'Session ID: ' . session_id();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';


require_once __DIR__ . '/includes/document-head.php';
require_once __DIR__ . '/components/navbar.php';

?>



<main class="max-w-6xl mx-auto px-6 py-8">
  <!-- Latest page -->
  <section class="mt-8 flex flex-row gap-8 min-h-[400px] border-b border-border">
    <div class="w-2/3 flex flex-col my-auto">
      <h1 class="text-md " id="latest">Latest Articles</h1>
      <h2 class="text-5xl"><a href="" class="text-foreground hover:text-accent transition-colors">Blog post title</a>
      </h2>
      <p class="text-lg text-muted-foreground">
        A brief description of the blog post goes here. It should be concise
        and enticing to encourage readers to click through.
      </p>
      <div class="flex items-center gap-4 my-4">
        <p class="text-sm font-medium">John Doe</p>
        <p class="text-xs text-muted-foreground">Published on Jan 1, 2024</p>
      </div>
      <a href="" class="text-accent hover:text-accent-foreground transition-colors">Read post</a>
    </div>

    <div class="w-1/3">
      <img src="https://via.placeholder.com/800x400" alt="Blog post image" class="w-full h-auto rounded-lg my-4">
    </div>
  </section>

  <section id="latest" class="py-12 border-b border-border">
    <?php
    $posts = [
      [
        'blogger_name' => 'Alice',
        'title' => 'Why slow mornings make better writing',
        'excerpt' => 'A short reflection on pace, attention, and how quieter starts can unlock better long-form thinking.',
        'date' => 'May 30, 2026',
        'url' => '/project-blog/pages/blog.php',
      ],
      [
        'blogger_name' => 'Noah',
        'title' => 'The newsletter renaissance is just beginning',
        'excerpt' => 'Independent voices are building audiences again. Here are the patterns that actually hold attention.',
        'date' => 'May 28, 2026',
        'url' => '/project-blog/pages/blog.php',
      ],
    ];
    ?>

    <div class="flex items-baseline justify-between mb-8">
      <h2 class="text-2xl text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
        Latest writing
      </h2>
      <span class="text-xs text-muted-foreground uppercase tracking-widest">Recent posts</span>
    </div>

    <div class="divide-y divide-border">
      <?php foreach ($posts as $post): ?>
        <?php include __DIR__ . '/components/post-preview.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>

  <?php
  $users = get_users();
  include __DIR__ . '/components/author-list.php';
  ?>
</main>





</body>

</html>