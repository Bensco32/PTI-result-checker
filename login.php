<?php
declare(strict_types=1);
$base = '';
require_once __DIR__ . '/includes/functions.php';
$context = 'public';
$active = 'login';
$title = 'PTI Result Checker | Student Login';

if (!empty($_SESSION['student_id'])) {
    redirect('student/dashboard.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $identifier = trim((string) ($_POST['identifier'] ?? ''));
    $password   = (string) ($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT * FROM students WHERE matric_number = ? OR email = ?');
    $stmt->execute([$identifier, $identifier]);
    $student = $stmt->fetch();

    if ($student && password_verify($password, $student['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['student_id'] = (int) $student['id'];
        redirect('student/dashboard.php');
    }
    $error = 'Invalid matriculation number or password.';
}

require __DIR__ . '/includes/header.php';
$flash = get_flash();
?>
<main class="form-page">
  <section class="checker-card">
    <div class="user-icon">♟</div>
    <h1>Student Login</h1>
    <p class="muted">Sign in with your matriculation number (or email) and password.</p>
    <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="login.php" data-loading>
      <?= csrf_field() ?>
      <label for="identifier">Matriculation Number / Email</label>
      <input id="identifier" name="identifier" type="text" required>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
      <button class="button button-primary full-width" type="submit" style="margin-top:20px">Sign In</button>
    </form>
    <p class="notice">✓ &nbsp; Contact the administrator if you do not have an account.</p>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
