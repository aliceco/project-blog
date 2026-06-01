<?php
$users = $users ?? [];
$contributorCount = count($users);
?>

<section id="writers" class="py-12 border-b border-border">
  <div class="flex items-baseline justify-between mb-8">
    <h2 class="text-2xl text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
      Our writers
    </h2>
    <span class="text-xs text-muted-foreground uppercase tracking-widest"><?= $contributorCount ?> contributors</span>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <?php foreach ($users as $user): ?>
      <?php
      $username = htmlspecialchars($user['username'] ?? 'writer');
      $title = htmlspecialchars($user['title'] ?? '');
      $presentation = htmlspecialchars($user['presentation'] ?? '');
      $profileImage = htmlspecialchars($user['profile_image'] ?? '');
      $avatarUrl = $profileImage !== '' ? $profileImage : '/project-blog/images/default-avatar.jpg';
      $url = '/project-blog/pages/blog.php?user=' . urlencode($user['username'] ?? '');
      ?>

      <a href="<?php echo $url; ?>"
        class="block text-left bg-card border border-border rounded-sm p-5 hover:border-accent hover:shadow-md transition-all group">
        <img src="<?php echo $avatarUrl; ?>" alt="<?php echo $username; ?>"
          class="w-12 h-12 rounded-full object-cover mb-3 grayscale group-hover:grayscale-0 transition-all">

        <p class="text-base text-foreground mb-1 group-hover:text-accent transition-colors"
          style="font-family: 'Playfair Display', serif; font-weight: 600;">
          <?php echo $username; ?>
        </p>

        <?php
        if (!empty($title)) {
          echo '<p class="text-xs text-muted-foreground mb-1" style="font-family: \'DM Sans\', sans-serif;">' . $title . '</p>';
        }
        ?>

        <?php if (!empty($presentation)): ?>
          <p class="text-xs text-muted-foreground leading-relaxed" style="font-family: 'DM Sans', sans-serif;">
            <?php echo $presentation; ?>
          </p>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>
