<?php
const INPUT_CLS = 'w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-brand-600 focus:ring-1 focus:ring-brand-600';

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_ok(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function pet_icon(string $category): string
{
    return in_array($category, ['Cat', 'Kitten'], true) ? 'fa-cat' : 'fa-dog';
}

function category_icon(string $category): string
{
    switch ($category) {
        case 'Adoption':          return 'fa-heart';
        case 'Lost & Found':      return 'fa-magnifying-glass';
        case 'Rescue':            return 'fa-life-ring';
        case 'Vaccination Drive': return 'fa-syringe';
        default:                  return 'fa-paw';
    }
}

function fmt_time(?string $time): string
{
    if (!$time) return '';
    $ts = strtotime($time);
    return date('i', $ts) === '00' ? date('gA', $ts) : date('g:iA', $ts);
}

function fmt_date(?string $date): string
{
    return $date ? date('M j, Y', strtotime($date)) : '';
}

function map_src(string $query): string
{
    return 'https://maps.google.com/maps?q=' . rawurlencode($query) . '&output=embed';
}

function directions_url(string $query): string
{
    return 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode($query);
}

function vacc_classes(string $status): string
{
    if ($status === 'Fully vaccinated')     return 'bg-emerald-100 text-emerald-800';
    if ($status === 'Partially vaccinated') return 'bg-amber-100 text-amber-800';
    return 'bg-rose-100 text-rose-800';
}

function valid_phone(string $phone): bool
{
    return (bool)preg_match('/^[0-9+\-\s()]{7,20}$/', $phone);
}

function photo_path(?string $file): ?string
{
    if (!$file) return null;
    $name = basename($file);
    return is_file(__DIR__ . '/../assets/img/' . $name) ? 'assets/img/' . $name : null;
}

function pet_card(array $p): void
{
    $img = photo_path($p['image'] ?? null);
    ?>
    <a href="pet.php?id=<?= (int)$p['id'] ?>" class="group flex flex-col bg-white rounded-xl border border-stone-200 overflow-hidden hover:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600">
      <div class="h-44 bg-brand-50 flex items-center justify-center">
        <?php if ($img): ?>
          <img src="<?= e($img) ?>" alt="<?= e($p['name']) ?>" class="h-full w-full object-cover">
        <?php else: ?>
          <i class="fa-solid <?= pet_icon($p['category']) ?> text-6xl text-brand-600" aria-hidden="true"></i>
        <?php endif; ?>
      </div>
      <div class="p-4 flex-1 flex flex-col">
        <div class="flex items-start justify-between gap-2">
          <h3 class="font-semibold text-lg text-slate-900"><?= e($p['name']) ?></h3>
          <span class="text-xs font-medium bg-brand-100 text-brand-800 rounded-full px-2 py-1"><?= e($p['category']) ?></span>
        </div>
        <p class="text-sm text-slate-600 mt-1"><?= e($p['breed']) ?>, <?= e($p['age_label']) ?></p>
        <p class="text-sm text-slate-500 mt-3 flex items-center gap-2">
          <i class="fa-solid fa-location-dot text-brand-600" aria-hidden="true"></i><?= e($p['municipality']) ?>
        </p>
        <span class="mt-3 self-start text-xs font-medium rounded-full px-2 py-1 <?= vacc_classes($p['vaccination_status']) ?>">
          <i class="fa-solid fa-syringe mr-1" aria-hidden="true"></i><?= e($p['vaccination_status']) ?>
        </span>
      </div>
    </a>
    <?php
}

function event_card(array $ev): void
{
    $past = $ev['event_date'] < date('Y-m-d');
    $left = max(0, (int)$ev['slots'] - (int)$ev['joined']);
    ?>
    <article class="bg-white rounded-xl border border-stone-200 p-5 flex flex-col sm:flex-row gap-4">
      <div class="shrink-0 w-16 h-16 rounded-lg bg-brand-600 text-white flex flex-col items-center justify-center">
        <span class="text-xl font-bold leading-none"><?= date('j', strtotime($ev['event_date'])) ?></span>
        <span class="text-xs mt-1"><?= date('M', strtotime($ev['event_date'])) ?></span>
      </div>
      <div class="flex-1">
        <div class="flex flex-wrap items-center gap-2">
          <h3 class="font-semibold text-lg text-slate-900"><?= e($ev['title']) ?></h3>
          <span class="text-xs font-medium bg-brand-100 text-brand-800 rounded-full px-2 py-1">
            <i class="fa-solid <?= category_icon($ev['category']) ?> mr-1" aria-hidden="true"></i><?= e($ev['category']) ?>
          </span>
        </div>
        <p class="text-sm text-slate-600 mt-2"><?= e($ev['description']) ?></p>
        <ul class="text-sm text-slate-600 mt-3 space-y-1">
          <li class="flex items-center gap-2"><i class="fa-regular fa-clock w-4 text-brand-600" aria-hidden="true"></i><?= fmt_time($ev['start_time']) ?> to <?= fmt_time($ev['end_time']) ?></li>
          <li class="flex items-center gap-2"><i class="fa-solid fa-location-dot w-4 text-brand-600" aria-hidden="true"></i><?= e($ev['venue']) ?>, Brgy. <?= e($ev['barangay']) ?>, <?= e($ev['municipality']) ?></li>
          <li class="flex items-center gap-2"><i class="fa-solid fa-users w-4 text-brand-600" aria-hidden="true"></i><?= $past ? 'This activity has ended' : $left . ' slots left' ?></li>
        </ul>
        <div class="mt-4 flex flex-wrap gap-2">
          <?php if ($past): ?>
            <span class="inline-flex items-center gap-2 rounded-lg bg-stone-200 text-stone-500 px-4 py-2 text-sm font-medium">Activity ended</span>
          <?php elseif ($left <= 0): ?>
            <span class="inline-flex items-center gap-2 rounded-lg bg-stone-200 text-stone-500 px-4 py-2 text-sm font-medium">Fully booked</span>
          <?php else: ?>
            <a href="join.php?event_id=<?= (int)$ev['id'] ?>" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 text-sm font-medium">
              <i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i>Join this activity
            </a>
          <?php endif; ?>
          <a href="<?= e(directions_url($ev['map_query'])) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-stone-300 hover:border-brand-600 text-slate-700 px-4 py-2 text-sm font-medium">
            <i class="fa-solid fa-route" aria-hidden="true"></i>Get directions
          </a>
        </div>
      </div>
    </article>
    <?php
}
