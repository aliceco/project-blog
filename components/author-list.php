<section id="writers" class="py-12 border-b border-border">
  <div class="flex items-baseline justify-between mb-8">
    <h2 class="text-md text-foreground" style="font-family: 'DM Sans', sans-serif; font-weight: 600;">
      Our writers
    </h2>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    <?php foreach ($authors as $author): ?>
      <?php
      $firstname = htmlspecialchars($author['firstname'] ?? '');
      $lastname = htmlspecialchars($author['lastname'] ?? '');
      $username = htmlspecialchars($author['username'] ?? '');
      $title = htmlspecialchars(strtoupper($author['title'] ?? ''));
      $bio = htmlspecialchars(substr(($author['bio'] ?? ''), 0, 50) . '...');
      $profileImage = htmlspecialchars($author['profile_image'] ?? '');
      $avatarUrl = $profileImage !== '' ? $profileImage : BASE_URL . 'images/default-avatar.jpg';
      $url = BASE_URL . 'pages/blog.php?author=' . urlencode($author['username'] ?? '');
      ?>
      <a href="<?= $url ?>"
        class="block text-left bg-card border border-border rounded-sm p-5 hover:border-accent hover:shadow-md transition-all group">

        <img src="<?= $avatarUrl ?>" alt="<?= $username ?>"
          class="w-12 h-12 rounded-full object-cover mb-3 grayscale group-hover:grayscale-0 transition-all">

        <div class="flex flex-row gap-1 items-center mb-1">
          <p class="text-base text-foreground mb-1 group-hover:text-accent transition-colors">
            <?= $firstname ?>
          </p>
          <p class="text-base text-foreground mb-1 group-hover:text-accent transition-colors">
            <?= $lastname ?>
          </p>
        </div>

        <?php if (!empty($title)): ?>
          <p class="text-xs text-muted-foreground mb-1" style="font-family: 'DM Sans', sans-serif;"><?= $title ?></p>
        <?php endif; ?>

        <?php if (!empty($author['bio'])): ?>
          <p class="text-xs text-muted-foreground leading-relaxed">
            <?= $bio ?>
          </p>
        <?php endif; ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>