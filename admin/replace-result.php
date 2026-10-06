<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin();

$id = ctype_digit((string) ($_GET['id'] ?? $_POST['id'] ?? '')) ? (int) ($_GET['id'] ?? $_POST['id']) : 0;
$stmt = db()->prepare(
    'SELECT r.*, s.matric_number, s.full_name FROM results r JOIN students s ON s.id = r.student_id WHERE r.id = ?'
);
$stmt->execute([$id]);
$result = $stmt->fetch();
if (!$result) {
    set_flash('error', 'Result not found.');
    redirect('results.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    [$tmp, $uploadError] = validate_pdf_upload($_FILES['result_pdf'] ?? ['error' => UPLOAD_ERR_NO_FILE]);
    if ($uploadError) {
        $error = $uploadError;
    } else {
        try {
            $oldPath = result_file_path($result['result_pdf']);
            $stored = store_pdf($tmp);
            $upd = db()->prepare('UPDATE results SET result_pdf = ?, updated_at = now() WHERE id = ?');
            $upd->execute([$stored, $id]);
            if ($oldPath) {
                @unlink($oldPath);
            }
            set_flash('success', 'Result replaced successfully.');
            redirect('results.php');
        } catch (Throwable $e) {
            $error = 'Could not store the uploaded file.';
        }
    }
}

$context = 'admin';
$active = 'results';
$title = 'PTI | Replace Result';
require __DIR__ . '/../includes/header.php';
?>
<main class="page-wrap">
  <h1 style="color:var(--green-dark)">Replace Result</h1>
  <p><b><?= e($result['full_name']) ?></b> — <?= e($result['matric_number']) ?> — <?= e($result['academic_session']) ?>, <?= e($result['semester']) ?></p>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <section class="table-panel wide">
    <form method="post" action="replace-result.php" enctype="multipart/form-data" data-loading>
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) $result['id'] ?>">
      <label for="result_pdf">New Result PDF * (max 5MB)</label>
      <input id="result_pdf" name="result_pdf" type="file" accept="application/pdf" required>
      <button class="button button-primary" type="submit" style="margin-top:20px">Replace PDF</button>
      <a class="button button-outline" href="results.php">Cancel</a>
    </form>
  </section>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
