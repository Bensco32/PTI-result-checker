<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $matric     = trim((string) ($_POST['matric_number'] ?? ''));
    $name       = trim((string) ($_POST['full_name'] ?? ''));
    $email      = trim((string) ($_POST['email'] ?? ''));
    $password   = (string) ($_POST['password'] ?? '');
    $department = trim((string) ($_POST['department'] ?? ''));
    $programme  = trim((string) ($_POST['programme'] ?? ''));
    $level      = trim((string) ($_POST['level'] ?? ''));

    if ($matric === '' || $name === '' || $email === '' || $password === '' || $department === '' || $programme === '' || $level === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        $photoPath = null;
        $photoError = null;
        if (!empty($_FILES['student_photo']['name'])) {
            [$tmp, $photoError] = validate_photo_upload($_FILES['student_photo']);
            if ($photoError) {
                $error = is_string($photoError) ? $photoError : 'Invalid photo upload.';
            } else {
                try {
                    $photoPath = store_photo($tmp, $_FILES['student_photo']['name']);
                } catch (Throwable $e) {
                    $error = 'Could not save the uploaded photo.';
                }
            }
        }

        if ($error === null) {
            try {
                $stmt = db()->prepare(
                    'INSERT INTO students (matric_number, full_name, email, password_hash, department, programme, level, student_photo)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([$matric, $name, $email, password_hash($password, PASSWORD_DEFAULT), $department, $programme, $level, $photoPath]);
                set_flash('success', 'Student registered successfully.');
                redirect('students.php');
            } catch (PDOException $e) {
                if ($e->getCode() === '23505') {
                    if (str_contains($e->getMessage(), 'matric_number')) {
                        $error = 'A student with this matriculation number already exists.';
                    } else {
                        $error = 'A student with this email already exists.';
                    }
                } else {
                    error_log($e->getMessage());
                    $error = 'Something went wrong while processing your request. Please try again.';
                }
            }
        }
    }
}

$context = 'admin';
$active = 'add';
$title = 'PTI | Add Student';
require __DIR__ . '/../includes/header.php';
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">Register a New Student</h1>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <section class="table-panel wide">
    <form method="post" action="add-student.php" enctype="multipart/form-data" data-loading>
      <?= csrf_field() ?>
      <label for="matric_number">Matriculation Number *</label>
      <input id="matric_number" name="matric_number" type="text" placeholder="e.g. CSIT/ICE/ND/2023/7297" required>
      <label for="full_name">Full Name *</label>
      <input id="full_name" name="full_name" type="text" required>
      <label for="email">Email Address *</label>
      <input id="email" name="email" type="email" required>
      <label for="password">Login Password * (min 8 characters)</label>
      <input id="password" name="password" type="password" required>
      <label for="department">Department *</label>
      <input id="department" name="department" type="text" placeholder="e.g. Computer Science" required>
      <label for="programme">Programme *</label>
      <input id="programme" name="programme" type="text" placeholder="e.g. ND" required>
      <label for="level">Level *</label>
      <input id="level" name="level" type="text" placeholder="e.g. ND 2" required>
      <label for="student_photo">Student Photo (JPG/PNG, optional)</label>
      <input id="student_photo" name="student_photo" type="file" accept="image/jpeg,image/png">
      <button class="button button-primary" type="submit" style="margin-top:20px">Register Student</button>
    </form>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
