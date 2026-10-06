<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
$context = 'public';
$active = '';
$title = 'PTI | Admin Login';

if (!empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $identifier = trim((string) ($_POST['identifier'] ?? ''));
    $password   = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM admins WHERE username = ? OR email = ?');
    $stmt->execute([$identifier, $identifier]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $admin['id'];
        redirect('dashboard.php');
    }
    $error = 'Invalid username or password.';
}

require __DIR__ . '/../includes/header.php';
?>
<main class="form-page">
  <section class="checker-card">
    <div class="user-icon">⚙</div>
    <h1>Admin Login</h1>
    <p class="muted">Sign in to the PTI result management portal.</p>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="login.php" data-loading>
      <?= csrf_field() ?>
      <label for="identifier">Username / Email</label>
      <input id="identifier" name="identifier" type="text" required>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
      <button class="button button-primary full-width" type="submit" style="margin-top:20px">Sign In</button>
    </form>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
