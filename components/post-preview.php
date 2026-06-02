<?php
$title = htmlspecialchars($post['title'] ?? 'Untitled');
$author = htmlspecialchars(getUserById($post['user_id'])['username'] ?? '');
$excerpt = htmlspecialchars(substr($post['content'] ?? '', 0, 180) . '...');
$date = readableDate($post['created_at'] ?? '');
$postUrl = BASE_URL . 'pages/blog.php?author=' . urlencode($author) . '&post=' . ($post['id'] ?? '');
?>

<article class="py-6 grid grid-cols-1 md:grid-cols-[1fr_auto] gap-4 items-start group">
  <div>
    <div class="flex items-center gap-3 mb-2">
      <?php if ($author): ?>
        <span class="text-xs text-muted-foreground"><?= $author ?></span>
      <?php endif; ?>
    </div>

    <h3 class="text-xl text-foreground group-hover:text-accent transition-colors mb-1">
      <a href="<?= $postUrl ?>"
        class="cursor-pointer"><?= $title ?></a>
    </h3>

    <p class="text-sm text-muted-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
      <?= $excerpt ?>
    </p>
  </div>

  <div class="flex md:flex-col items-center md:items-end gap-2 text-xs text-muted-foreground shrink-0">
    <?php if ($date): ?>
      <span><?= $date ?></span>
    <?php endif; ?>
  </div>
</article>
