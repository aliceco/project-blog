<?php
$title = htmlspecialchars($post['title'] ?? 'Untitled');
$excerpt = htmlspecialchars(mb_strimwidth((string) $post['content'], 0, 180, '...'));
$createdAt = $post['created_at'] ?? ($post['date'] ?? '');
$date = '';

// Parse created at timestamp to a readable date format using strtotime and date functions, with error handling for invalid timestamps
if (!empty($createdAt)) {
  $timestamp = strtotime((string) $createdAt);
  $date = $timestamp ? date('M j, Y', $timestamp) : (string) $createdAt;
}

$author = htmlspecialchars($post['blogger_name'] ?? ($post['author'] ?? ($user['username'] ?? '')));
$url = htmlspecialchars($post['url'] ?? ('/project-blog/pages/post.php?id=' . urlencode((string) ($post['id'] ?? ''))));

?>

<article class="py-6 grid grid-cols-1 md:grid-cols-[1fr_auto] gap-4 items-start group">
  <div>
    <div class="flex items-center gap-3 mb-2">
      <?php if ($author): ?>
        <span class="text-xs text-muted-foreground"><?= $author ?></span>
      <?php endif; ?>
    </div>

    <h3 class="text-xl text-foreground group-hover:text-accent transition-colors mb-1" style="font-family: 'Playfair Display', serif; font-weight: 600;">
      <a href="<?= $url ?>" class="cursor-pointer"><?= $title ?></a>
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
