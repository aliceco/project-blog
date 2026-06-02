<?php
$title = 'Login';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../admin/session.php';
require_once __DIR__ . '/../admin/db.php';
require_once __DIR__ . '/../admin/auth.php';
require_once __DIR__ . '/../admin/utils.php';

$csrfToken = createCsrfToken();

function validateLoginInput($username, $password)
{
  $errors = [];

  // Check if fields are empty
  if (checkIfEmpty($username) || checkIfEmpty($password)) { 
    $errors['general'] = 'All fields are required.';
    return $errors;
  } 

  return $errors;
}

function validateRegistrationInput($firstname, $lastname, $username, $email,  $password, $gdpr)
{
  // Validates registration input, sends appropriate error message if error
  $errors = [];

  if (checkIfEmpty($username)) {
    $errors['username'] = 'Username is required';
  }

  if (checkIfEmpty($firstname)) {
    $errors['firstname'] = 'First name is required';
  }

  if (checkIfEmpty($lastname)) {
    $errors['lastname'] = 'Last name is required';
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

// Stores information about session if error occurs so user don't have to re-enter all information
$errors = $_SESSION['form_errors'] ?? []; 
$activeTab = $_SESSION['active_tab'] ?? 'login';
$formData = $_SESSION['form_data'] ?? [];

unset($_SESSION['form_errors'], $_SESSION['active_tab'], $_SESSION['form_data']); // Resets temporary form state after load

// Gets old field values
function oldFieldValue($field, $errors, $formData)
{
  if (!empty($errors[$field])) {
    return '';
  }

  return htmlspecialchars($formData[$field] ?? '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? 'login'; // Check which form is being submitted
  $activeTab = $action === 'register' ? 'register' : 'login';

  if (
    empty($_POST['csrf_token']) ||
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    $errors['general'] = 'Invalid CSRF token. Please refresh and try again.';
  } else {
    // Handles login
    if ($action === 'login') {
      $username = trim($_POST['username']);
      $password = $_POST['password'];

      $errors = validateLoginInput($username, $password);

      if (empty($errors)) {
        $user = loginUser($username, $password);
        if ($user) {
          session_regenerate_id(true);
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $user['username'];
          header('Location: ' . BASE_URL . 'index.php');
          newCSRFToken();
          exit();
        }
        $errors['general'] = 'Invalid username or password.';
      }
    }

    // handles registration
    if ($action === 'register') {
      $firstname = trim($_POST['firstname']);
      $lastname = trim($_POST['lastname']);
      $username = trim($_POST['username']);
      $password = $_POST['password'];
      $email = trim($_POST['email']);
      $gdpr = isset($_POST['gdpr']);

      $errors = validateRegistrationInput($firstname, $lastname, $username, $email, $password, $gdpr);
      
      if (empty($errors)) { // If no errors, try to register user
        $newUserId = registerUser($firstname, $lastname, $username, $password, $email);
        if ($newUserId !== false) {
          // Redirect user to homepage if registrations was successful 
          session_regenerate_id(true);
          $_SESSION['user_id'] = $newUserId;
          $_SESSION['username'] = $username;
          header('Location: ' . BASE_URL . 'index.php');
          newCSRFToken();
          exit();
        }
        $errors['general'] = 'User already exists.';
      }
    }
  }

  // Avoids resubmitting form when refreshing page & saves errors in session to show them to user if error has occured
  if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['active_tab'] = $activeTab;
    $_SESSION['form_data'] = [
      'firstname' => trim($_POST['firstname'] ?? ''),
      'lastname' => trim($_POST['lastname'] ?? ''),
      'username' => trim($_POST['username'] ?? ''),
      'email' => trim($_POST['email'] ?? ''),
    ];
    header('Location: ' . BASE_URL . 'pages/login.php');
    exit();
  }
}

require_once __DIR__ . '/../includes/document-head.php';?>

<header class="border-b border-border max-w-6xl mx-auto px-6 py-4 flex justify-between items-center ">
    <h1 class="text-2xl text-foreground">
      <a href="<?= BASE_URL ?>index.php" class="hover:text-accent transition-colors">
        The Square
      </a>
    </h1>
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
      <button id="tab-login"
        class="px-3 py-2 w-32 rounded-l-md bg-primary text-primary-foreground cursor-pointer">Login</button>
      <button id="tab-register"
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
          value="<?= oldFieldValue('username', $errors, $formData) ?>"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['username']) && $activeTab === 'login'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['username']) ?></p>
        <?php endif; ?>

        <label for="login-password" class="text-sm font-medium text-foreground">Password</label>
        <input id="login-password" name="password" type="password" placeholder="••••••••"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['password']) && $activeTab === 'login'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['password']) ?></p>
        <?php endif; ?>

        <button type="submit"
          class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:opacity-90 transition-opacity cursor-pointer">Login</button>
        <?php if (!empty($errors['general']) && $activeTab === 'login'): ?>
          <p class="text-sm text-red-600">
            <?= htmlspecialchars($errors['general']) ?>
          </p>
        <?php endif; ?>

      </form>

      <form id="panelRegister" class="mt-8 flex flex-col gap-2 hidden" method="POST">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"> <!-- send csrf token-->

        <label for="register-firstname" class="text-sm font-medium text-foreground">First Name</label>
        <input id="register-firstname" name="firstname" type="text" placeholder="Jane"
          value="<?= oldFieldValue('firstname', $errors, $formData) ?>"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['firstname']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['firstname']) ?></p>
        <?php endif; ?>

        <label for="register-lastname" class="text-sm font-medium text-foreground">Last Name</label>
        <input id="register-lastname" name="lastname" type="text" placeholder="Doe"
          value="<?= oldFieldValue('lastname', $errors, $formData) ?>"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['lastname']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['lastname']) ?></p>
        <?php endif; ?>

        <label for="register-username" class="text-sm font-medium text-foreground">Username</label>
        <input id="register-username" name="username" type="text" placeholder="janedoe"
          value="<?= oldFieldValue('username', $errors, $formData) ?>"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['username']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['username']) ?></p>
        <?php endif; ?>

        <label for="register-email" class="text-sm font-medium text-foreground">Email</label>
        <input id="register-email" name="email" type="email" placeholder="you@example.com"
          value="<?= oldFieldValue('email', $errors, $formData) ?>"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['email']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['email']) ?></p>
        <?php endif; ?>

        <label for="register-password" class="text-sm font-medium text-foreground">Password</label>
        <input id="register-password" name="password" type="password" placeholder="••••••••"
          class="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
        <?php if (!empty($errors['password']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['password']) ?></p>
        <?php endif; ?>

        <div class="text-sm text-muted-foreground align-items-center gap-2 flex">
          <input id="gdpr" name="gdpr" type="checkbox" class="cursor-pointer">
          <label for="gdpr" class="text-xs ">
            I agree to the processing of my personal data in accordance with the
            <a href="<?= BASE_URL ?>pages/gdpr.php" class="hover:text-primary cursor-pointer underline">Privacy Policy</a>.
            You can withdraw your consent at any time.
          </label>
        </div>

        <?php if (!empty($errors['gdpr']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['gdpr']) ?></p>
        <?php endif; ?>
        <?php if (!empty($errors['general']) && $activeTab === 'register'): ?>
          <p class="text-sm text-red-600"><?= htmlspecialchars($errors['general']) ?></p>
        <?php endif; ?>

        <button type="submit"
          class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:opacity-90 transition-opacity cursor-pointer">Register</button>

      </form>
    </div>

  </div>

</main>

<script>
  const loginTab = document.getElementById('tab-login');
  const registerTab = document.getElementById('tab-register');
  const loginMessage = document.getElementById('login-message');
  const registerMessage = document.getElementById('register-message');
  const loginPanel = document.getElementById('panelLogin');
  const registerPanel = document.getElementById('panelRegister');

  function show(which) {
    const isLogin = which === 'login';

    loginPanel.classList.toggle('hidden', !isLogin);
    registerPanel.classList.toggle('hidden', isLogin);

    loginMessage.classList.toggle('hidden', !isLogin);
    registerMessage.classList.toggle('hidden', isLogin);

    loginTab.className = isLogin ? 'px-3 py-2 w-32 rounded-l-md bg-primary text-primary-foreground cursor-pointer' : 'px-3 py-2 w-32 rounded-l-md bg-secondary text-secondary-foreground cursor-pointer';
    registerTab.className = !isLogin ? 'px-3 py-2 w-32 rounded-r-md bg-primary text-primary-foreground cursor-pointer' : 'px-3 py-2 w-32 rounded-r-md bg-secondary text-secondary-foreground cursor-pointer';
  }

  loginTab.addEventListener('click', () => show('login'));
  registerTab.addEventListener('click', () => show('register'));

  show('<?= htmlspecialchars($activeTab) ?>');

</script>


<?= require_once __DIR__ . '/../includes/footer.php'; ?>

</body>

</html>
