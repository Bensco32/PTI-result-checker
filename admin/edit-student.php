<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin();

$id = ctype_digit((string) ($_GET['id'] ?? $_POST['id'] ?? '')) ? (int) ($_GET['id'] ?? $_POST['id']) : 0;
$stmt = db()->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) {
    set_flash('error', 'Student not found.');
    redirect('students.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $matric     = trim((string) ($_POST['matric_number'] ?? ''));
    $name       = trim((string) ($_POST['full_name'] ?? ''));
    $email      = trim((string) ($_POST['email'] ?? ''));
    $department = trim((string) ($_POST['department'] ?? ''));
    $programme  = trim((string) ($_POST['programme'] ?? ''));
    $level      = trim((string) ($_POST['level'] ?? ''));

    if ($matric === '' || $name === '' || $email === '' || $department === '' || $programme === '' || $level === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $upd = db()->prepare(
                'UPDATE students SET matric_number = ?, full_name = ?, email = ?, department = ?, programme = ?, level = ?, updated_at = now() WHERE id = ?'
            );
            $upd->execute([$matric, $name, $email, $department, $programme, $level, $id]);
            set_flash('success', 'Student updated successfully.');
            redirect('students.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') {
                $error = 'A student with this matriculation number or email already exists.';
            } else {
                error_log($e->getMessage());
                $error = 'Something went wrong while processing your request. Please try again.';
            }
        }
    }
}

$context = 'admin';
$active = 'students';
$title = 'PTI | Edit Student';
require __DIR__ . '/../includes/header.php';
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">Edit Student</h1>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <section class="table-panel wide">
    <form method="post" action="edit-student.php" data-loading>
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $student['id'] ?>">
      <label for="matric_number">Matriculation Number *</label>
      <input id="matric_number" name="matric_number" type="text" value="<?= e($student['matric_number']) ?>" required>
      <label for="full_name">Full Name *</label>
      <input id="full_name" name="full_name" type="text" value="<?= e($student['full_name']) ?>" required>
      <label for="email">Email *</label>
      <input id="email" name="email" type="email" value="<?= e($student['email']) ?>" required>
      <label for="department">Department *</label>
      <input id="department" name="department" type="text" value="<?= e($student['department']) ?>" required>
      <label for="programme">Programme *</label>
      <input id="programme" name="programme" type="text" value="<?= e($student['programme']) ?>" required>
      <label for="level">Level *</label>
      <input id="level" name="level" type="text" value="<?= e($student['level']) ?>" required>
      <button class="button button-primary" type="submit" style="margin-top:20px">Save Changes</button>
      <a class="button button-outline" href="students.php">Cancel</a>
    </form>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
