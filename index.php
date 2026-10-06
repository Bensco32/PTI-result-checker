<?php
declare(strict_types=1);
$base = '';
require_once __DIR__ . '/includes/functions.php';
$context = 'public';
$active = 'home';
$title = 'PTI Result Checker | Home';
require __DIR__ . '/includes/header.php';
?>
<main>
  <section class="hero">
    <div class="hero-content">
      <p class="eyebrow">STUDENT ACADEMIC PORTAL</p>
      <h1>Welcome to<br><span>PTI Result Checker</span></h1>
      <p>Access your academic result information through this student result-checking portal.</p>
      <a class="button button-yellow" href="check-result.php">Check Your Result <span aria-hidden="true">→</span></a>
    </div>
  </section>

  <section class="feature-grid section-wrap">
    <article class="feature-card"><div class="feature-icon">▤</div><h2>Academic Excellence</h2><p>Supporting students in keeping track of their academic progress.</p></article>
    <article class="feature-card"><div class="feature-icon">▥</div><h2>Quality Training</h2><p>Learning and practical skills for a brighter future.</p></article>
    <article class="feature-card"><div class="feature-icon">⚙</div><h2>Skilled Workforce</h2><p>Building knowledge for careers in technical industries.</p></article>
    <article class="feature-card"><div class="feature-icon">◎</div><h2>Global Opportunities</h2><p>Encouraging growth, development and opportunity.</p></article>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
