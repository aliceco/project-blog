<?php
$title = 'Login';
require_once __DIR__ . '/../admin/session.php';
require_once __DIR__ . '/../admin/db.php';
require_once __DIR__ . '/../admin/auth.php';

$csrfToken = create_csrf_token();

function validate_login_input($username, $password)
{
  $errors = [];

  if (trim($username) === '') {
    $errors['username'] = 'Username is required';
  }

  if ($password === '') {
    $errors['password'] = 'Password is required';
  }

  return $errors;
}

function validate_password_input($username, $password, $email, $gdpr)
{
  $errors = [];

  if (trim($username) === '') {
    $errors['username'] = 'Username is required';
  }

  if (strlen($password) < 6) {
    $errors['password'] = 'Password must be at least 6 characters long.';
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Valid email is required.';
  }

  if (!$gdpr) {
    $errors['gdpr'] = 'You must agree to the processing of your personal data in accordance with the Privacy Policy.';
  }

  return $errors;
}

$errors = [];
$activeTab = 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    $errors['csrf'] = "Ogiltig CSRF-token.";
  } else {

    $action = $_POST['action'] ?? 'login';

    if ($action === 'login') {
      $username = trim($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';

      $errors = validate_login_input($username, $password);
      if (empty($errors)) {
        $user = login_user($username, $password);
        if ($user) {
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $user['username'];
          header('Location: /project-blog/index.php');
          exit();
        }
        $errors['general'] = 'Invalid username or password.';
      }

      $activeTab = 'login';
    }

    if ($action === 'register') {
      $username = trim($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';
      $email = trim($_POST['email'] ?? '');
      $gdpr = isset($_POST['gdpr']);

      $errors = validate_password_input($username, $password, $email, $gdpr);
      if (empty($errors)) {
        $newUserId = register_user($username, $password, $email);
        if ($newUserId !== false) {
          $_SESSION['user_id'] = $newUserId;
          $_SESSION['username'] = $username;
          header('Location: /project-blog/index.php');
          exit();
        }
        $errors['general'] = 'User already exists.';
      }

      $activeTab = 'register';
    }
  }
}

require_once __DIR__ . '/../includes/document-head.php';
?>

<header class="border-b border-border max-w-6xl mx-auto px-6 py-4 flex justify-between items-center ">
  
    <h1 class="text-2xl text-foreground">
      <a href="/project-blog/index.php" class="hover:text-accent transition-colors">
        The Square
      </a>
    </h1>
  
  </div>
</header>

<main class="max-w-xl mx-auto px-6 py-8">
  <div class="text-center">
    <h1 class="text-2xl font-display font-medium text-foreground" id="login-message">
      Welcome back.
    </h1>
    <h1 class="text-2xl font-display font-medium text-foreground" id="register-message">
      Register a new account.
    </h1>
  </div>
  <div class="mt-8  ">
    <div class="mx-auto flex justify-center">
      <button id="tabLogin"
        class="px-3 py-2 w-32 rounded-l-md bg-primary text-primary-foreground cursor-pointer">Login</button>
      <button id="tabRegister"
        class="px-3 py-2 w-32 rounded-md bg-secondary text-secondary-foreground cursor-pointer">Register</button>
    </div>
    <div>
      <form id="panelLogin" class="mt-8 flex flex-col gap-2" method="POST">
        <input type="hidden" name="action" value="login">
        <!-- Hidden field to indicate which form is being sent -->
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <!-- send csrf token-->

        <label for="login-username" class="text-sm font-medium text-foreground">Username</label>
        <input id="login-username" name="username" type="text" placeholder="Your Username"
          class="px-4 py-2 border border-border rounded-r-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['username'])): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['username']) ?></p>
        <?php endif; ?>

        <label for="login-password" class="text-sm font-medium text-foreground">Password</label>
        <input id="login-password" name="password" type="password" placeholder="••••••••"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['password'])): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['password']) ?></p>
        <?php endif; ?>

        <button type="submit"
          class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:opacity-90 transition-opacity">Login</button>
        <?php if (!empty($errors['general']) && $activeTab === 'login'): ?>
          <p class="text-sm text-red-600">
            <?= htmlspecialchars($errors['general']) ?>
          </p>
        <?php endif; ?>

      </form>

      <form id="panelRegister" class="mt-8 flex flex-col gap-2 hidden" method="POST">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"> <!-- send csrf token-->


        <label for="register-username" class="text-sm font-medium text-foreground">Username</label>
        <input id="register-username" name="username" type="text" placeholder="janedoe"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['username']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['username']) ?></p>
        <?php endif; ?>

        <label for="register-email" class="text-sm font-medium text-foreground">Email</label>
        <input id="register-email" name="email" type="email" placeholder="you@example.com"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['email'])): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['email']) ?></p>
        <?php endif; ?>

        <label for="register-password" class="text-sm font-medium text-foreground">Password</label>
        <input id="register-password" name="password" type="password" placeholder="••••••••"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['password']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['password']) ?></p>
        <?php endif; ?>
        <div class="text-sm text-muted-foreground align-items-center gap-2 flex">
          <input id="gdpr" name="gdpr" type="checkbox" required>
          <label for="gdpr" class="text-xs">
            I agree to the processing of my personal data in accordance with the
            <a href="/privacy-policy">Privacy Policy</a>.
            You can withdraw your consent at any time.
          </label>
        </div>
        <?php if (!empty($errors['gdpr'])): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['gdpr']) ?></p>
        <?php endif; ?>
        <?php if (!empty($errors['general']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['general']) ?></p>
        <?php endif; ?>

        <button type="submit"
          class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:opacity-90 transition-opacity">Register</button>
        <?php if (!empty($errors['general'])): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['general']) ?></p>
        <?php endif; ?>

      </form>
    </div>

  </div>

</main>

<script>
  const loginTab = document.getElementById('tabLogin');
  const registerTab = document.getElementById('tabRegister');
  const loginMessage = document.getElementById('loginMessage');
  const registerMessage = document.getElementById('registerMessage');
  const loginPanel = document.getElementById('panelLogin');
  const registerPanel = document.getElementById('panelRegister');

  function show(which) {
    const isLogin = which === 'login';

    loginPanel.classList.toggle('hidden', !isLogin);
    registerPanel.classList.toggle('hidden', isLogin);

    loginMessage.classList.toggle('hidden', !isLogin);
    registerMessage.classList.toggle('hidden', isLogin);

    loginTab.className = isLogin ? 'px-3 py-2 w-32 rounded-l-md bg-primary text-primary-foreground' : 'px-3 py-2 w-32 rounded-l-md bg-secondary text-secondary-foreground';
    registerTab.className = !isLogin ? 'px-3 py-2 w-32 rounded-r-md bg-primary text-primary-foreground' : 'px-3 py-2 w-32 rounded-r-md bg-secondary text-secondary-foreground';
  }

  loginTab.addEventListener('click', () => show('login'));
  registerTab.addEventListener('click', () => show('register'));

  show('<?= $activeTab ?>');

</script>


</body>

</html>