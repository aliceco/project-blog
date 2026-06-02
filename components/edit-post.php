<div id="edit-post" class="fixed inset-0 z-50 hidden">
  <div id="edit-post-backdrop" class="absolute inset-0 bg-foreground/30"></div>

  <div class="relative min-h-full flex items-start justify-center overflow-y-auto py-10 px-4">
    <div class="w-full max-w-2xl bg-card border border-border rounded-sm shadow-xl">
      <div class="flex items-center justify-between px-6 py-4 border-b border-border">
        <h2 class="text-lg text-foreground" style="font-family: 'Playfair Display', serif; font-weight: 600;">
          Edit post
        </h2>
        <button id="close-edit-post" type="button"
          class="text-md text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
          x
        </button>
      </div>

      <form method="POST" class="p-6 space-y-5" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit_post">
        <input type="hidden" name="post_id" value="<?= htmlspecialchars($selectedPostId) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="512000"> <!-- 500 KB -->


        <div>
          <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
            style="font-family: 'DM Sans', sans-serif;">Title</label>
          <input type="text" name="title" value="<?= htmlspecialchars($editPostOld['title'] ?? ($selectedPost['title'])) ?>"
            class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
          <?php if (!empty($editPostErrors['title'])): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($editPostErrors['title']) ?></p>
          <?php endif; ?>
        </div>

        <div>
          <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
            style="font-family: 'DM Sans', sans-serif;">Content</label>
          <textarea name="content" rows="5"
            class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all resize-y"
            style="line-height: 1.7"><?= htmlspecialchars($editPostOld['content'] ?? ($selectedPost['content'])) ?></textarea>
          <?php if (!empty($editPostErrors['content'])): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($editPostErrors['content']) ?></p>
          <?php endif; ?>
        </div>

        <div>
          <label class="block text-xs uppercase tracking-widest text-muted-foreground "
            style="font-family: 'DM Sans', sans-serif;">Upload New Image</label>
          <p class="text-xs tracking-widest italic text-foreground mb-1.5">Note: Uploading a new image will replace your
            previous image</p>
          <input type="file" name="post-image" accept="image/*"
            class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
          <?php if (!empty($editPostErrors['post-image'])): ?>
            <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($editPostErrors['post-image']) ?></p>
          <?php endif; ?>
        </div>

        <div class="flex items-center justify-between gap-3 pt-2 border-t border-border">
          <div>
            <?php if (!empty($editPostErrors['csrf']) || !empty($editPostErrors['general'])): ?>
              <div class="text-sm text-red-600">
                <?= htmlspecialchars($editPostErrors['csrf'] ?? $editPostErrors['general']) ?>
              </div>
            <?php endif; ?>
          </div>
          <div>
            <button id="cancel-edit-post" type="button"
              class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
              Cancel
            </button>
            <button type="submit"
              class="px-5 py-2 text-sm bg-accent text-accent-foreground rounded-sm hover:opacity-90 transition-opacity cursor-pointer">
              Save changes
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
