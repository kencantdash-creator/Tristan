<?php
require __DIR__ . '/config.php';

$cats = ['Adoption', 'Lost & Found', 'Rescue', 'Vaccination Drive'];
$cat = $_GET['category'] ?? '';
if (!in_array($cat, $cats, true)) $cat = '';

$sql = "SELECT e.*, (SELECT COUNT(*) FROM event_signups x WHERE x.event_id = e.id) AS joined FROM events e";
$params = [];
if ($cat !== '') {
    $sql .= " WHERE e.category = :cat";
    $params[':cat'] = $cat;
}
$sql .= " ORDER BY (e.event_date < CURDATE()) ASC, e.event_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

$pageTitle = 'Activities';
$active = 'events';
require __DIR__ . '/includes/header.php';
?>
<section class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold text-slate-900">Adoption and animal welfare activities</h1>
  <p class="text-slate-600 mt-2">Pick a category to find an activity where you can adopt or volunteer.</p>

  <div class="mt-6 flex flex-wrap gap-2">
    <a href="events.php" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium border <?= $cat === '' ? 'bg-brand-600 border-brand-600 text-white' : 'bg-white border-stone-300 text-slate-700 hover:border-brand-600' ?>">
      <i class="fa-solid fa-paw" aria-hidden="true"></i>All
    </a>
    <?php foreach ($cats as $c): ?>
      <a href="events.php?category=<?= urlencode($c) ?>" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium border <?= $cat === $c ? 'bg-brand-600 border-brand-600 text-white' : 'bg-white border-stone-300 text-slate-700 hover:border-brand-600' ?>">
        <i class="fa-solid <?= category_icon($c) ?>" aria-hidden="true"></i><?= e($c) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="mt-8 space-y-4">
    <?php foreach ($events as $ev) event_card($ev); ?>
    <?php if (!$events): ?>
      <div class="bg-white border border-stone-200 rounded-xl p-8 text-center">
        <i class="fa-solid fa-calendar-xmark text-4xl text-brand-600" aria-hidden="true"></i>
        <p class="font-semibold text-slate-900 mt-3">No activities in this category yet.</p>
        <a href="events.php" class="inline-block mt-3 text-sm font-medium text-brand-700 hover:underline">Show all activities</a>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
