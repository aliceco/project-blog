<header class="border-b border-border sticky top-0 bg-background/95 backdrop-blur-sm z-10">
    <nav class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">


        <p class="text-2xl text-foreground">
            <a href="/project-blog/index.php" class="hover:text-accent transition-colors">
                The Square
            </a>
        </p>

        <?php if (is_logged_in()): ?>
            <div class="relative">
                <div id="userMenu"
                    class="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center text-sm font-bold cursor-pointer hover:bg-purple-600 transition-colors">
                    <?= strtoupper(substr($username, 0, 1)) ?> <!-- Show first letter of username in capital-->
                </div>
                <div id="dropdownMenu"
                    class="absolute right-0 top-full mt-2 w-48 bg-card border border-border rounded-md shadow-lg hidden z-20">
                    <a href="/project-blog/pages/blog.php?user=<?= urlencode($username) ?>"
                        class="block px-4 py-2 hover:bg-accent hover:text-accent-foreground">
                        My blog
                    </a>
                    <a href="/project-blog/admin/logout.php" class="block px-4 py-2 hover:bg-accent rounded-b-md cursor-pointer hover:text-accent-foreground">Logout</a>
                </div>
            </div>



            </div>
        <?php else: ?>
            <button onclick=" window.location.href = '/project-blog/pages/login.php';"
                class="justify-self-end text-sm px-4 py-2 bg-primary text-primary-foreground rounded-md hover:opacity-90 transition-opacity cursor-pointer">
                Sign in
            </button>
        <?php endif; ?>

    </nav>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const userMenu = document.getElementById('userMenu');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (userMenu && dropdownMenu) {
            // Make dropdown visible when clicking on user menu
            userMenu.addEventListener('click', () => {
                dropdownMenu.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside of it
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#userMenu') && !e.target.closest('#dropdownMenu')) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }
    });
</script>