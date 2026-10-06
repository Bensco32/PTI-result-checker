<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin();

$id = ctype_digit((string) ($_GET['id'] ?? $_POST['id'] ?? '')) ? (int) ($_GET['id'] ?? $_POST['id']) : 0;
$stmt = db()->prepare('SELECT id, full_name, matric_number FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) {
    set_flash('error', 'Student not found.');
    redirect('students.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $new = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');
    if (strlen($new) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($new !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $upd = db()->prepare('UPDATE students SET password_hash = ?, updated_at = now() WHERE id = ?');
        $upd->execute([password_hash($new, PASSWORD_DEFAULT), $id]);
        set_flash('success', 'Password reset for ' . $student['full_name'] . '.');
        redirect('students.php');
    }
}

$context = 'admin';
$active = 'students';
$title = 'PTI | Reset Student Password';
require __DIR__ . '/../includes/header.php';
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">Reset Password</h1>
  <p>Student: <b><?= e($student['full_name']) ?></b> (<?= e($student['matric_number']) ?>)</p>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <section class="table-panel wide">
    <form method="post" action="reset-password.php" data-loading>
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
      <label for="new_password">New Password</label>
      <input id="new_password" name="new_password" type="password" required>
      <label for="confirm_password">Confirm New Password</label>
      <input id="confirm_password" name="confirm_password" type="password" required>
      <button class="button button-primary" type="submit" style="margin-top:20px">Reset Password</button>
      <a class="button button-outline" href="students.php">Cancel</a>
    </form>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
