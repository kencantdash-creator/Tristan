<?php
require __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT p.*, s.name AS shelter_name, s.municipality, s.barangay, s.address, s.map_query,
            s.contact, s.visit_days, s.visit_start, s.visit_end, s.feeding_time
     FROM pets p JOIN shelters s ON s.id = p.shelter_id WHERE p.id = :id"
);
$stmt->execute([':id' => $id]);
$pet = $stmt->fetch();
if (!$pet) http_response_code(404);

$pageTitle = $pet ? $pet['name'] : 'Pet not found';
$active = 'pets';
require __DIR__ . '/includes/header.php';

if (!$pet): ?>
  <section class="max-w-3xl mx-auto px-4 py-16 text-center">
    <i class="fa-solid fa-circle-question text-5xl text-brand-600" aria-hidden="true"></i>
    <h1 class="text-2xl font-bold mt-4">We could not find that pet</h1>
    <a href="pets.php" class="inline-block mt-4 text-brand-700 font-medium hover:underline">Back to pet listings</a>
  </section>
<?php require __DIR__ . '/includes/footer.php'; exit; endif;

$img = photo_path($pet['image']);
$available = $pet['status'] === 'available';
?>
<section class="max-w-6xl mx-auto px-4 py-8">
  <a href="pets.php" class="text-sm text-brand-700 font-medium hover:underline inline-flex items-center gap-2"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i>Back to pets</a>

  <div class="mt-4 grid lg:grid-cols-5 gap-8">
    <div class="lg:col-span-3 space-y-6">
      <div class="bg-white rounded-xl border border-stone-200 overflow-hidden">
        <div class="h-64 md:h-80 bg-brand-50 flex items-center justify-center">
          <?php if ($img): ?>
            <img src="<?= e($img) ?>" alt="<?= e($pet['name']) ?>" class="h-full w-full object-cover">
          <?php else: ?>
            <i class="fa-solid <?= pet_icon($pet['category']) ?> text-8xl text-brand-600" aria-hidden="true"></i>
          <?php endif; ?>
        </div>
        <div class="p-6">
          <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-3xl font-bold text-slate-900"><?= e($pet['name']) ?></h1>
            <span class="text-xs font-medium bg-brand-100 text-brand-800 rounded-full px-3 py-1"><?= e($pet['category']) ?></span>
            <?php if (!$available): ?><span class="text-xs font-medium bg-stone-200 text-stone-600 rounded-full px-3 py-1">Already adopted</span><?php endif; ?>
          </div>
          <p class="text-slate-700 mt-4"><?= nl2br(e($pet['description'])) ?></p>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-stone-200 p-6">
        <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-notes-medical text-brand-600" aria-hidden="true"></i>Pet information</h2>
        <dl class="mt-4 grid sm:grid-cols-2 gap-4 text-sm">
          <div><dt class="text-slate-500">Breed</dt><dd class="font-medium text-slate-900"><?= e($pet['breed']) ?></dd></div>
          <div><dt class="text-slate-500">Age</dt><dd class="font-medium text-slate-900"><?= e($pet['age_label']) ?></dd></div>
          <div><dt class="text-slate-500">Sex</dt><dd class="font-medium text-slate-900"><?= e($pet['sex']) ?></dd></div>
          <div><dt class="text-slate-500">Size</dt><dd class="font-medium text-slate-900"><?= e($pet['size']) ?></dd></div>
          <div>
            <dt class="text-slate-500">Vaccination status</dt>
            <dd class="mt-1"><span class="text-xs font-medium rounded-full px-2 py-1 <?= vacc_classes($pet['vaccination_status']) ?>"><?= e($pet['vaccination_status']) ?></span></dd>
          </div>
          <div class="sm:col-span-2"><dt class="text-slate-500">Health details</dt><dd class="font-medium text-slate-900"><?= e($pet['health_details']) ?></dd></div>
        </dl>
      </div>

      <div class="bg-white rounded-xl border border-stone-200 p-6">
        <h2 class="text-xl font-semibold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-map-location-dot text-brand-600" aria-hidden="true"></i>Where to find <?= e($pet['name']) ?></h2>
        <p class="text-sm text-slate-600 mt-2"><?= e($pet['shelter_name']) ?>, Brgy. <?= e($pet['barangay']) ?>, <?= e($pet['municipality']) ?>, Leyte</p>
        <div class="mt-4 rounded-lg overflow-hidden border border-stone-200 aspect-video">
          <iframe src="<?= e(map_src($pet['map_query'])) ?>" class="w-full h-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map of <?= e($pet['shelter_name']) ?>"></iframe>
        </div>
        <a href="<?= e(directions_url($pet['map_query'])) ?>" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-stone-300 hover:border-brand-600 px-4 py-2 text-sm font-medium text-slate-700">
          <i class="fa-solid fa-route" aria-hidden="true"></i>Get directions
        </a>
      </div>
    </div>

    <aside class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-xl border border-stone-200 p-6 lg:sticky lg:top-24">
        <?php if ($available): ?>
          <a href="adopt.php?pet_id=<?= (int)$pet['id'] ?>" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-sun-400 hover:bg-sun-500 text-slate-900 font-semibold px-5 py-3">
            <i class="fa-solid fa-heart" aria-hidden="true"></i>I want to adopt
          </a>
        <?php else: ?>
          <p class="text-sm text-slate-600">This pet already has a home. Browse other pets that are still available.</p>
          <a href="pets.php" class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 hover:bg-brand-700 text-white font-semibold px-5 py-3">See available pets</a>
        <?php endif; ?>

        <h2 class="text-lg font-semibold text-slate-900 mt-6 flex items-center gap-2"><i class="fa-regular fa-clock text-brand-600" aria-hidden="true"></i>Best time to visit</h2>
        <p class="text-sm text-slate-700 mt-2">
          Best to visit the shelter from <?= fmt_time($pet['visit_start']) ?> to <?= fmt_time($pet['visit_end']) ?> on <?= e($pet['visit_days']) ?>.
        </p>
        <p class="text-sm text-slate-600 mt-2">Feeding hours: <?= e($pet['feeding_time']) ?>.</p>

        <h2 class="text-lg font-semibold text-slate-900 mt-6 flex items-center gap-2"><i class="fa-solid fa-phone text-brand-600" aria-hidden="true"></i>Contact the shelter</h2>
        <p class="text-sm text-slate-700 mt-2"><?= e($pet['shelter_name']) ?></p>
        <p class="text-sm text-slate-600"><?= e($pet['contact']) ?></p>
      </div>
    </aside>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
