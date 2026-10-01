<?php
require __DIR__ . '/config.php';

$id = (int)($_GET['event_id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT e.*, (SELECT COUNT(*) FROM event_signups x WHERE x.event_id = e.id) AS joined
     FROM events e WHERE e.id = :id"
);
$stmt->execute([':id' => $id]);
$ev = $stmt->fetch();
if (!$ev) http_response_code(404);

$errors = [];
$done = false;
$old = ['full_name' => '', 'email' => '', 'phone' => '', 'role' => 'Adopter'];
$open = $ev && $ev['event_date'] >= date('Y-m-d') && (int)$ev['joined'] < (int)$ev['slots'];

if ($ev && $open && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['full_name'] = trim($_POST['full_name'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $old['phone'] = trim($_POST['phone'] ?? '');
    $old['role'] = $_POST['role'] ?? '';

    if (!csrf_ok()) $errors[] = 'Your session expired. Reload the page and try again.';
    if ($old['full_name'] === '' || mb_strlen($old['full_name']) > 100) $errors[] = 'Enter your full name.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (!valid_phone($old['phone'])) $errors[] = 'Enter a valid phone number.';
    if (!in_array($old['role'], ['Adopter', 'Volunteer'], true)) $errors[] = 'Choose how you want to join.';

    if (!$errors) {
        $ins = $pdo->prepare(
            "INSERT INTO event_signups (event_id, full_name, email, phone, role)
             VALUES (:event_id, :full_name, :email, :phone, :role)"
        );
        $ins->execute([
            ':event_id' => $ev['id'], ':full_name' => $old['full_name'],
            ':email' => $old['email'], ':phone' => $old['phone'], ':role' => $old['role'],
        ]);
        $done = true;
    }
}

$pageTitle = $ev ? 'Join ' . $ev['title'] : 'Activity not found';
$active = 'events';
require __DIR__ . '/includes/header.php';

if (!$ev): ?>
  <section class="max-w-3xl mx-auto px-4 py-16 text-center">
    <i class="fa-solid fa-circle-question text-5xl text-brand-600" aria-hidden="true"></i>
    <h1 class="text-2xl font-bold mt-4">We could not find that activity</h1>
    <a href="events.php" class="inline-block mt-4 text-brand-700 font-medium hover:underline">Back to activities</a>
  </section>
<?php require __DIR__ . '/includes/footer.php'; exit; endif; ?>

<section class="max-w-6xl mx-auto px-4 py-8">
  <a href="events.php" class="text-sm text-brand-700 font-medium hover:underline inline-flex items-center gap-2"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i>Back to activities</a>
  <div class="mt-4 grid lg:grid-cols-5 gap-8">
    <div class="lg:col-span-3 space-y-6">
      <div class="bg-white rounded-xl border border-stone-200 p-6">
        <span class="text-xs font-medium bg-brand-100 text-brand-800 rounded-full px-3 py-1"><i class="fa-solid <?= category_icon($ev['category']) ?> mr-1" aria-hidden="true"></i><?= e($ev['category']) ?></span>
        <h1 class="text-2xl font-bold text-slate-900 mt-3"><?= e($ev['title']) ?></h1>
        <p class="text-slate-700 mt-3"><?= e($ev['description']) ?></p>
        <ul class="text-sm text-slate-700 mt-4 space-y-2">
          <li class="flex items-center gap-2"><i class="fa-regular fa-calendar w-4 text-brand-600" aria-hidden="true"></i><?= fmt_date($ev['event_date']) ?></li>
          <li class="flex items-center gap-2"><i class="fa-regular fa-clock w-4 text-brand-600" aria-hidden="true"></i><?= fmt_time($ev['start_time']) ?> to <?= fmt_time($ev['end_time']) ?></li>
          <li class="flex items-center gap-2"><i class="fa-solid fa-location-dot w-4 text-brand-600" aria-hidden="true"></i><?= e($ev['venue']) ?>, Brgy. <?= e($ev['barangay']) ?>, <?= e($ev['municipality']) ?></li>
        </ul>
      </div>
      <div class="bg-white rounded-xl border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-map-location-dot text-brand-600" aria-hidden="true"></i>Venue map</h2>
        <div class="mt-4 rounded-lg overflow-hidden border border-stone-200 aspect-video">
          <iframe src="<?= e(map_src($ev['map_query'])) ?>" class="w-full h-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map of <?= e($ev['venue']) ?>"></iframe>
        </div>
        <a href="<?= e(directions_url($ev['map_query'])) ?>" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-stone-300 hover:border-brand-600 px-4 py-2 text-sm font-medium text-slate-700">
          <i class="fa-solid fa-route" aria-hidden="true"></i>Get directions
        </a>
      </div>
    </div>

    <div class="lg:col-span-2">
      <div class="bg-white rounded-xl border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900">Sign up for this activity</h2>
        <?php if ($done): ?>
          <div class="mt-4 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-900">
            <p class="font-semibold flex items-center gap-2"><i class="fa-solid fa-circle-check" aria-hidden="true"></i>You are signed up.</p>
            <p class="mt-1">See you on <?= fmt_date($ev['event_date']) ?> at <?= e($ev['venue']) ?>.</p>
          </div>
        <?php elseif (!$open): ?>
          <p class="mt-4 text-sm text-slate-600"><?= $ev['event_date'] < date('Y-m-d') ? 'This activity has already ended.' : 'This activity is fully booked.' ?></p>
        <?php else: ?>
          <?php if ($errors): ?>
            <div class="mt-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-900" role="alert">
              <ul class="list-disc pl-5 space-y-1"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
            </div>
          <?php endif; ?>
          <form method="post" class="mt-4 space-y-4" novalidate>
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div>
              <label for="full_name" class="block text-sm font-medium mb-1">Full name</label>
              <input id="full_name" name="full_name" value="<?= e($old['full_name']) ?>" class="<?= INPUT_CLS ?>" required>
            </div>
            <div>
              <label for="email" class="block text-sm font-medium mb-1">Email</label>
              <input id="email" type="email" name="email" value="<?= e($old['email']) ?>" class="<?= INPUT_CLS ?>" required>
            </div>
            <div>
              <label for="phone" class="block text-sm font-medium mb-1">Phone number</label>
              <input id="phone" name="phone" value="<?= e($old['phone']) ?>" class="<?= INPUT_CLS ?>" required>
            </div>
            <fieldset>
              <legend class="block text-sm font-medium mb-2">Join as</legend>
              <div class="flex gap-4 text-sm">
                <label class="flex items-center gap-2"><input type="radio" name="role" value="Adopter" <?= $old['role'] === 'Adopter' ? 'checked' : '' ?>>Adopter</label>
                <label class="flex items-center gap-2"><input type="radio" name="role" value="Volunteer" <?= $old['role'] === 'Volunteer' ? 'checked' : '' ?>>Volunteer</label>
              </div>
            </fieldset>
            <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 hover:bg-brand-700 text-white font-semibold px-5 py-3">
              <i class="fa-solid fa-hand-holding-heart" aria-hidden="true"></i>Join this activity
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
