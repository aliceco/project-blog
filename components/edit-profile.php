<div id="edit-profile" class="fixed inset-0 z-50 hidden">
  <div id="edit-profile-backdrop" class="absolute inset-0 bg-foreground/30"></div>

  <div class="relative min-h-full flex items-start justify-center overflow-y-auto py-10 px-4">
    <div class="w-full max-w-2xl bg-card border border-border rounded-sm shadow-xl">
      <div class="flex items-center justify-between px-6 py-4 border-b border-border">
        <h2 class="text-lg text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
          Edit profile
        </h2>
        <button id="close-edit-profile" type="button"
          class="text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
          x
        </button>
      </div>

      <form method="POST" class="p-6 space-y-5" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit_profile">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="307200"> <!-- 300 KB -->


        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5 w-full"
              style="font-family: 'DM Sans', sans-serif;">First name</label>
            <input type="text" name="firstname" value="<?= $firstname ?>"
              class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
              <?php if (!empty($errors['firstname'])): ?>
                <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($errors['firstname']) ?></p>
              <?php endif; ?>
          </div>
          <div>
            <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
              style="font-family: 'DM Sans', sans-serif;">Last name</label>
            <input type="text" name="lastname" value="<?= $lastname ?>"
              class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
              <?php if (!empty($errors['lastname'])): ?>
                <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($errors['lastname']) ?></p>
              <?php endif; ?>
          </div>
        </div>
        <div>
          <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
            style="font-family: 'DM Sans', sans-serif;">Username</label>
          <input type="text" name="username" value="<?= $username ?>"
            class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
          <?php if (!empty($errors['username'])): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($errors['username']) ?></p>
          <?php endif; ?>
        </div>

        <div>
          <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
            style="font-family: 'DM Sans', sans-serif;">Title</label>
          <input type="text" name="title" value="<?= $title ?>"
            class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
        </div>

        <div>
          <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
            style="font-family: 'DM Sans', sans-serif;">Bio</label>
          <textarea name="bio" rows="5"
            class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all resize-y"
            style="line-height: 1.7"><?= $bio ?></textarea>
        </div>

        <div>
          <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
            style="font-family: 'DM Sans', sans-serif;">Profile image</label>
          <input type="file" name="profile_image" accept="image/*"
            class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
          <?php if (!empty($errors['profile_image'])): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($errors['profile_image']) ?></p>
          <?php endif; ?>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2 border-t border-border">
          <?php if (!empty($errors['general'])): ?>
            <p class="text-sm text-red-600 mr-auto"><?= htmlspecialchars($errors['general']) ?></p>
          <?php endif; ?>
          <button id="cancel-edit-profile" type="button"
            class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
            Cancel
          </button>
          <button type="submit"
            class="px-5 py-2 text-sm bg-accent text-accent-foreground rounded-sm hover:opacity-90 transition-opacity cursor-pointer">
            Save changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
