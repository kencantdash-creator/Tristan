<?php
require __DIR__ . '/config.php';

$types = ['Dog', 'Cat', 'Puppy', 'Kitten'];
$type = $_GET['type'] ?? '';
$q = trim($_GET['q'] ?? '');
if (!in_array($type, $types, true)) $type = '';

$sql = "SELECT p.*, s.municipality FROM pets p JOIN shelters s ON s.id = p.shelter_id WHERE p.status = 'available'";
$params = [];
if ($type !== '') {
    $sql .= " AND p.category = :type";
    $params[':type'] = $type;
}
if ($q !== '') {
    $sql .= " AND (p.name LIKE :q1 OR p.breed LIKE :q2 OR s.municipality LIKE :q3)";
    $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . $q . '%';
}
$sql .= " ORDER BY p.created_at DESC, p.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pets = $stmt->fetchAll();

$pageTitle = 'Find a Pet';
$active = 'pets';
require __DIR__ . '/includes/header.php';
?>
<section class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold text-slate-900">Pet listings</h1>
  <p class="text-slate-600 mt-2">Dogs, cats, puppies, and kittens from shelters in Leyte.</p>

  <form method="get" class="mt-6 flex flex-col sm:flex-row gap-3">
    <div class="relative flex-1">
      <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true"></i>
      <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search by name, breed, or municipality" class="<?= INPUT_CLS ?> pl-10" aria-label="Search pets">
    </div>
    <?php if ($type !== ''): ?><input type="hidden" name="type" value="<?= e($type) ?>"><?php endif; ?>
    <button class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 hover:bg-brand-700 text-white px-5 py-2 text-sm font-medium">
      <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>Search
    </button>
  </form>

  <div class="mt-4 flex flex-wrap gap-2">
    <?php
    $tabs = ['' => ['All', 'fa-paw'], 'Dog' => ['Dogs', 'fa-dog'], 'Cat' => ['Cats', 'fa-cat'], 'Puppy' => ['Puppies', 'fa-bone'], 'Kitten' => ['Kittens', 'fa-cat']];
    foreach ($tabs as $val => [$label, $icon]):
        $href = 'pets.php?' . http_build_query(array_filter(['type' => $val, 'q' => $q]));
    ?>
      <a href="<?= e($href) ?>" class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium border <?= $type === $val ? 'bg-brand-600 border-brand-600 text-white' : 'bg-white border-stone-300 text-slate-700 hover:border-brand-600' ?>">
        <i class="fa-solid <?= $icon ?>" aria-hidden="true"></i><?= $label ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php if ($pets): ?>
    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <?php foreach ($pets as $p) pet_card($p); ?>
    </div>
  <?php else: ?>
    <div class="mt-8 bg-white border border-stone-200 rounded-xl p-8 text-center">
      <i class="fa-solid fa-paw text-4xl text-brand-600" aria-hidden="true"></i>
      <p class="font-semibold text-slate-900 mt-3">No pets match your search.</p>
      <p class="text-sm text-slate-600 mt-1">Try a different name or clear the filter.</p>
      <a href="pets.php" class="inline-block mt-4 text-sm font-medium text-brand-700 hover:underline">Show all pets</a>
    </div>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
