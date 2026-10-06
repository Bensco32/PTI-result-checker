<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin();

$students = db()->query('SELECT id, matric_number, full_name FROM students ORDER BY full_name')->fetchAll();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $studentId = ctype_digit((string) ($_POST['student_id'] ?? '')) ? (int) $_POST['student_id'] : 0;
    $session   = trim((string) ($_POST['academic_session'] ?? ''));
    $semester  = trim((string) ($_POST['semester'] ?? ''));

    if ($studentId <= 0 || $session === '' || $semester === '') {
        $error = 'Please select a student, academic session and semester.';
    } elseif (!in_array($semester, ['First Semester', 'Second Semester'], true)) {
        $error = 'Please choose a valid semester.';
    } else {
        [$tmp, $uploadError] = validate_pdf_upload($_FILES['result_pdf'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
        if ($uploadError) {
            $error = $uploadError;
        } else {
            $check = db()->prepare('SELECT id FROM students WHERE id = ?');
            $check->execute([$studentId]);
            if (!$check->fetch()) {
                $error = 'Student not found.';
            } else {
                try {
                    $stored = store_pdf($tmp);
                    $stmt = db()->prepare(
                        'INSERT INTO results (student_id, academic_session, semester, result_pdf) VALUES (?, ?, ?, ?)'
                    );
                    $stmt->execute([$studentId, $session, $semester, $stored]);
                    set_flash('success', 'Result uploaded successfully.');
                    redirect('results.php');
                } catch (PDOException $e) {
                    $storedPath = __DIR__ . '/../uploads/results/' . $stored;
                    if (is_file($storedPath)) {
                        @unlink($storedPath);
                    }
                    if ($e->getCode() === '23505') {
                        $error = 'A result already exists for this student, session and semester. Use Replace instead.';
                    } else {
                        error_log($e->getMessage());
                        $error = 'Something went wrong while processing your request. Please try again.';
                    }
                } catch (Throwable $e) {
                    $error = 'Could not store the uploaded file.';
                }
            }
        }
    }
}

$context = 'admin';
$active = 'upload';
$title = 'PTI | Upload Result';
require __DIR__ . '/../includes/header.php';
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">Upload Student Result</h1>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <section class="table-panel wide">
    <form method="post" action="upload-result.php" enctype="multipart/form-data" data-loading>
      <?= csrf_field() ?>
      <label for="student_id">Student *</label>
      <select id="student_id" name="student_id" required>
        <option value="">-- Select a student --</option>
        <?php foreach ($students as $s): ?>
          <option value="<?= (int) $s['id'] ?>"><?= e($s['matric_number']) ?> — <?= e($s['full_name']) ?></option>
        <?php endforeach; ?>
      </select>
      <label for="academic_session">Academic Session *</label>
      <input id="academic_session" name="academic_session" type="text" placeholder="e.g. 2025/2026" required>
      <label for="semester">Semester *</label>
      <select id="semester" name="semester" required>
        <option>First Semester</option>
        <option>Second Semester</option>
      </select>
      <label for="result_pdf">Result PDF * (max 5MB)</label>
      <input id="result_pdf" name="result_pdf" type="file" accept="application/pdf" required>
      <button class="button button-primary" type="submit" style="margin-top:20px">Upload Result</button>
    </form>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
