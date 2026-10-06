<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
$student = require_student();

$message = null;
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $current = (string) ($_POST['current_password'] ?? '');
    $new     = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if (!password_verify($current, $student['password_hash'])) {
        $message = 'Current password is incorrect.';
        $messageType = 'error';
    } elseif (strlen($new) < 8) {
        $message = 'New password must be at least 8 characters.';
        $messageType = 'error';
    } elseif ($new !== $confirm) {
        $message = 'New passwords do not match.';
        $messageType = 'error';
    } else {
        $stmt = db()->prepare('UPDATE students SET password_hash = ?, updated_at = now() WHERE id = ?');
        $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $student['id']]);
        $message = 'Password updated successfully.';
        $messageType = 'success';
    }
}

$context = 'student';
$active = 'profile';
$title = 'PTI | My Profile';
require __DIR__ . '/../includes/header.php';
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">My Profile</h1>
  <?php if ($message): ?><div class="alert alert-<?= $messageType === 'success' ? 'success' : 'error' ?>"><?= e($message) ?></div><?php endif; ?>
  <section class="table-panel">
    <img class="profile-photo" src="<?= e(student_photo_url($student['student_photo'] ?? null)) ?>" alt="Profile photo">
    <p><b>Matric Number:</b> <?= e($student['matric_number']) ?></p>
    <p><b>Full Name:</b> <?= e($student['full_name']) ?></p>
    <p><b>Email:</b> <?= e($student['email']) ?></p>
    <p><b>Department:</b> <?= e($student['department']) ?></p>
    <p><b>Programme:</b> <?= e($student['programme']) ?></p>
    <p><b>Level:</b> <?= e($student['level']) ?></p>
  </section>
  <section class="table-panel" style="margin-top:20px">
    <h2 class="table-title">Change Password</h2>
    <form method="post" action="profile.php" data-loading>
      <?= csrf_field() ?>
      <label for="current_password">Current Password</label>
      <input id="current_password" name="current_password" type="password" required>
      <label for="new_password">New Password</label>
      <input id="new_password" name="new_password" type="password" required>
      <label for="confirm_password">Confirm New Password</label>
      <input id="confirm_password" name="confirm_password" type="password" required>
      <button class="button button-primary" type="submit" style="margin-top:16px">Update Password</button>
    </form>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
