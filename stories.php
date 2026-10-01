<?php
require __DIR__ . '/config.php';
$stories = $pdo->query("SELECT * FROM stories ORDER BY story_date DESC, id DESC")->fetchAll();

$pageTitle = 'Stories';
$active = 'stories';
require __DIR__ . '/includes/header.php';
?>
<section class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold text-slate-900">Photos and experiences</h1>
  <p class="text-slate-600 mt-2">Stories from adopters who gave a pet a home.</p>

  <div class="mt-8 grid gap-6 md:grid-cols-2">
    <?php foreach ($stories as $s): $img = photo_path($s['image']); ?>
      <article class="bg-white rounded-xl border border-stone-200 overflow-hidden flex flex-col">
        <div class="h-48 bg-brand-50 flex items-center justify-center">
          <?php if ($img): ?>
            <img src="<?= e($img) ?>" alt="<?= e($s['pet_name']) ?> with its new family" class="h-full w-full object-cover">
          <?php else: ?>
            <i class="fa-solid fa-camera text-5xl text-brand-600" aria-hidden="true"></i>
          <?php endif; ?>
        </div>
        <div class="p-5 flex-1 flex flex-col">
          <h2 class="text-lg font-semibold text-slate-900"><?= e($s['title']) ?></h2>
          <p class="text-slate-700 text-sm mt-2 flex-1"><?= e($s['story']) ?></p>
          <div class="mt-4 text-sm text-slate-500 flex flex-wrap gap-x-4 gap-y-1">
            <span class="flex items-center gap-2"><i class="fa-solid fa-paw text-brand-600" aria-hidden="true"></i><?= e($s['pet_name']) ?></span>
            <span class="flex items-center gap-2"><i class="fa-solid fa-user text-brand-600" aria-hidden="true"></i><?= e($s['adopter_name']) ?></span>
            <span class="flex items-center gap-2"><i class="fa-solid fa-location-dot text-brand-600" aria-hidden="true"></i><?= e($s['municipality']) ?></span>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
    <?php if (!$stories): ?><p class="text-slate-600">No stories yet.</p><?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
