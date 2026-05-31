<!-- EDIT PROFILE MODAL -->
        <div id="editProfileModal" class="fixed inset-0 z-50 hidden">
            <div id="editProfileBackdrop" class="absolute inset-0 bg-foreground/30"></div>

            <div class="relative min-h-full flex items-start justify-center overflow-y-auto py-10 px-4">
                <div class="w-full max-w-2xl bg-card border border-border rounded-sm shadow-xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                        <h2 class="text-lg text-foreground"
                            style="font-family: 'Playfair Display', serif; font-weight: 600;">
                            Edit profile
                        </h2>
                        <button id="closeEditProfileModal" type="button"
                            class="text-muted-foreground hover:text-foreground transition-colors">
                            x
                        </button>
                    </div>

                    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                        <input type="hidden" name="action" value="edit_profile">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

                        <div>
                            <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
                                style="font-family: 'DM Sans', sans-serif;">Username</label>
                            <input type="text" name="username" value="<?php echo $usernameSafe; ?>"
                                class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
                                style="font-family: 'DM Sans', sans-serif;">Title</label>
                            <input type="text" name="title" value="<?php echo $titleSafe; ?>"
                                class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
                                style="font-family: 'DM Sans', sans-serif;">Presentation</label>
                            <textarea name="presentation" rows="5"
                                class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all resize-y"
                                style="line-height: 1.7"><?php echo $presentationSafe; ?></textarea>
                        </div>

                        <div>
                            <label class="block text-xs uppercase tracking-widest text-muted-foreground mb-1.5"
                                style="font-family: 'DM Sans', sans-serif;">Profile image</label>
                            <input type="file" name="profile_image"
                                class="w-full px-3 py-2 bg-background border border-border rounded-sm text-sm text-foreground focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 transition-all">
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-border">
                            <button id="cancelEditProfileModal" type="button"
                                class="px-4 py-2 text-sm text-muted-foreground hover:text-foreground transition-colors"
                                style="font-family: 'DM Sans', sans-serif;">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-5 py-2 text-sm bg-accent text-accent-foreground rounded-sm hover:opacity-90 transition-opacity"
                                style="font-family: 'DM Sans', sans-serif;">
                                Save changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>