<div id="delete-modal" class="fixed inset-0 z-50 hidden ">
    <div id="delete-backdrop" class="absolute inset-0 bg-foreground/30"></div>

    <div class="relative min-h-full flex items-center justify-center overflow-y-auto py-10 px-4">
        <div class="w-full max-w-xl bg-card border border-border rounded-sm shadow-xl">
            <div class="relative flex items-center justify-center px-6 py-4">
                <h1 class="text-lg text-foreground text-center"
                    style="font-family: 'Playfair Display', serif; font-weight: 600;">
                    Delete
                </h1>
                <button id="close-delete-post" type="button"
                    class="absolute right-6 text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
                    x
                </button>
            </div>
            <h2 class="px-6 py-4 text-center">Are you sure you want to delete this post? </h2>
            <form method="POST" class="p-6 space-y-5">
                <input type="hidden" name="action" value="deleted_post">
                <input type="hidden" name="post_id" value="<?= htmlspecialchars($selectedPostId) ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div class="flex flex-col items-center gap-3 pt-2">
                    <div class="flex items-center justify-center gap-3">
                        <button id="cancel-delete-post" type="button"
                            class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm bg-destructive text-accent-foreground rounded-sm hover:opacity-90 transition-opacity cursor-pointer">
                            Delete Post
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>