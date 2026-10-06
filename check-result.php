<?php
declare(strict_types=1);
$base = '';
require_once __DIR__ . '/includes/functions.php';
$context = 'public';
$active = 'check';
$title = 'PTI Result Checker | Check Result';
require __DIR__ . '/includes/header.php';
$flash = get_flash();
?>
<main class="form-page">
  <section class="checker-card">
    <div class="user-icon">♟</div>
    <h1>Student Result Checker</h1>
    <p class="muted">Enter your matriculation number, session and semester to view your result.</p>
    <?php if ($flash): ?><div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div><?php endif; ?>
    <form method="get" action="result.php" data-loading>
      <label for="matric">Matriculation Number</label>
      <input id="matric" name="matric" type="text" placeholder="e.g. CSIT/ICE/ND/2023/7297" required>
      <label for="session">Academic Session</label>
      <input id="session" name="session" type="text" list="sessions" placeholder="e.g. 2025/2026" required>
      <datalist id="sessions"><option>2025/2026</option><option>2024/2025</option><option>2023/2024</option></datalist>
      <fieldset>
        <legend>Semester</legend>
        <label class="radio-label"><input type="radio" name="semester" value="First Semester" checked> First Semester</label>
        <label class="radio-label"><input type="radio" name="semester" value="Second Semester"> Second Semester</label>
      </fieldset>
      <button class="button button-primary full-width" type="submit">⌕ &nbsp; View Result</button>
    </form>
    <p class="notice">✓ &nbsp; Results are stored securely and verified against official records.</p>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
