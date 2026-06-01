<header class="border-b border-border sticky top-0 bg-background/95 backdrop-blur-sm z-10">
    <nav class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl"><a href="/project-blog/index.php" class="hover:text-accent transition-colors">
                The Square
            </a>
        </h1>
        <?php if (isLoggedIn()): ?>
            <?php $sessionUser = (string)($_SESSION['username'] ?? '');  $logoutCsrfToken = createCsrfToken(); ?>
            
            <div class="relative">
                <div id="user-menu"
                    class="w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center text-sm font-bold cursor-pointer hover:bg-purple-600 transition-colors">
                    <?= htmlspecialchars(strtoupper(substr($sessionUser, 0, 1))) ?>
                </div>
                <div id="dropdown-menu"
                    class="absolute right-0 top-full mt-2 w-48 bg-card border border-border rounded-md shadow-lg hidden z-20">
                    <a href="/project-blog/pages/blog.php?author=<?= htmlspecialchars(urlencode($sessionUser)) ?>"
                        class="block px-4 py-2 hover:bg-accent hover:text-accent-foreground">
                        My blog
                    </a>
                    <form method="POST" action="/project-blog/admin/logout.php">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($logoutCsrfToken) ?>">
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 hover:bg-accent rounded-b-md cursor-pointer hover:text-accent-foreground">
                            Log Out
                        </button>
                    </form>
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
        const userMenu = document.getElementById('user-menu');
        const dropdownMenu = document.getElementById('dropdown-menu');

        if (userMenu && dropdownMenu) {
            // Make dropdown visible when clicking on user menu
            userMenu.addEventListener('click', () => {
                dropdownMenu.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside of it
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#user-menu') && !e.target.closest('#dropdown-menu')) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }
    });
</script>
