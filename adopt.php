<?php
require __DIR__ . '/config.php';

$id = (int)($_GET['pet_id'] ?? 0);
$stmt = $pdo->prepare(
    "SELECT p.*, s.name AS shelter_name, s.municipality, s.contact
     FROM pets p JOIN shelters s ON s.id = p.shelter_id WHERE p.id = :id"
);
$stmt->execute([':id' => $id]);
$pet = $stmt->fetch();
if (!$pet) http_response_code(404);

$errors = [];
$done = false;
$old = ['full_name' => '', 'email' => '', 'phone' => '', 'address' => '', 'housing' => '', 'has_other_pets' => '', 'reason' => ''];
$housingOptions = ['Own house', 'Renting', 'Living with family'];

if ($pet && $pet['status'] === 'available' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $v) {
        $old[$k] = trim($_POST[$k] ?? '');
    }
    if (!csrf_ok()) $errors[] = 'Your session expired. Reload the page and try again.';
    if ($old['full_name'] === '' || mb_strlen($old['full_name']) > 100) $errors[] = 'Enter your full name.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (!valid_phone($old['phone'])) $errors[] = 'Enter a valid phone number.';
    if ($old['address'] === '' || mb_strlen($old['address']) > 200) $errors[] = 'Enter your home address.';
    if (!in_array($old['housing'], $housingOptions, true)) $errors[] = 'Choose your housing type.';
    if (!in_array($old['has_other_pets'], ['Yes', 'No'], true)) $errors[] = 'Tell us if you have other pets.';
    if (mb_strlen($old['reason']) < 10) $errors[] = 'Tell us why you want to adopt (at least 10 characters).';

    if (!$errors) {
        $ins = $pdo->prepare(
            "INSERT INTO adoption_requests (pet_id, full_name, email, phone, address, housing, has_other_pets, reason)
             VALUES (:pet_id, :full_name, :email, :phone, :address, :housing, :has_other_pets, :reason)"
        );
        $ins->execute([
            ':pet_id' => $pet['id'], ':full_name' => $old['full_name'], ':email' => $old['email'],
            ':phone' => $old['phone'], ':address' => $old['address'], ':housing' => $old['housing'],
            ':has_other_pets' => $old['has_other_pets'], ':reason' => $old['reason'],
        ]);
        $done = true;
    }
}

$pageTitle = $pet ? 'Adopt ' . $pet['name'] : 'Pet not found';
$active = 'pets';
require __DIR__ . '/includes/header.php';

if (!$pet): ?>
  <section class="max-w-3xl mx-auto px-4 py-16 text-center">
    <i class="fa-solid fa-circle-question text-5xl text-brand-600" aria-hidden="true"></i>
    <h1 class="text-2xl font-bold mt-4">We could not find that pet</h1>
    <a href="pets.php" class="inline-block mt-4 text-brand-700 font-medium hover:underline">Back to pet listings</a>
  </section>
<?php require __DIR__ . '/includes/footer.php'; exit; endif;

$requirements = [
    'You are at least 18 years old.',
    'You have a valid government-issued ID.',
    'You can show proof of address.',
    'Your household agrees to the adoption.',
    'You agree to a shelter check on your home if requested.',
    'You will keep your pet vaccinated and bring it to a vet when sick.',
];
$steps = [
    'Send your adoption request using the form on this page.',
    'The shelter contacts you to confirm your details.',
    'Visit the shelter and meet ' . $pet['name'] . '.',
    'Complete the shelter requirements and adoption agreement.',
    'Take your new pet home.',
];
?>
<section class="max-w-6xl mx-auto px-4 py-8">
  <a href="pet.php?id=<?= (int)$pet['id'] ?>" class="text-sm text-brand-700 font-medium hover:underline inline-flex items-center gap-2"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i>Back to <?= e($pet['name']) ?></a>
  <h1 class="text-3xl font-bold text-slate-900 mt-4">Adopt <?= e($pet['name']) ?></h1>
  <p class="text-slate-600 mt-1"><?= e($pet['breed']) ?>, <?= e($pet['age_label']) ?>, at <?= e($pet['shelter_name']) ?> in <?= e($pet['municipality']) ?>.</p>

  <div class="mt-6 grid lg:grid-cols-5 gap-8">
    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-xl border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-clipboard-check text-brand-600" aria-hidden="true"></i>Adoption requirements</h2>
        <ul class="mt-3 space-y-2 text-sm text-slate-700">
          <?php foreach ($requirements as $r): ?>
            <li class="flex gap-2"><i class="fa-solid fa-check text-brand-600 mt-1" aria-hidden="true"></i><span><?= e($r) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="bg-white rounded-xl border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2"><i class="fa-solid fa-list-ol text-brand-600" aria-hidden="true"></i>Adoption process</h2>
        <ol class="mt-3 space-y-3 text-sm text-slate-700">
          <?php foreach ($steps as $i => $s): ?>
            <li class="flex gap-3">
              <span class="shrink-0 w-6 h-6 rounded-full bg-brand-600 text-white text-xs font-semibold flex items-center justify-center"><?= $i + 1 ?></span>
              <span><?= e($s) ?></span>
            </li>
          <?php endforeach; ?>
        </ol>
        <p class="text-xs text-slate-500 mt-4">Shelter contact: <?= e($pet['contact']) ?></p>
      </div>
    </div>

    <div class="lg:col-span-3">
      <div class="bg-white rounded-xl border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900">Adoption request form</h2>
        <?php if ($pet['status'] !== 'available'): ?>
          <p class="mt-4 text-sm text-slate-600"><?= e($pet['name']) ?> has already been adopted.</p>
          <a href="pets.php" class="inline-block mt-3 text-sm font-medium text-brand-700 hover:underline">See available pets</a>
        <?php elseif ($done): ?>
          <div class="mt-4 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-900">
            <p class="font-semibold flex items-center gap-2"><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Request sent.</p>
            <p class="mt-1">The shelter will contact you at <?= e($old['email']) ?> or <?= e($old['phone']) ?>.</p>
          </div>
          <a href="pets.php" class="inline-block mt-4 text-sm font-medium text-brand-700 hover:underline">Browse more pets</a>
        <?php else: ?>
          <?php if ($errors): ?>
            <div class="mt-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-sm text-rose-900" role="alert">
              <ul class="list-disc pl-5 space-y-1"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
            </div>
          <?php endif; ?>
          <form method="post" class="mt-4 grid sm:grid-cols-2 gap-4" novalidate>
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <div class="sm:col-span-2">
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
            <div class="sm:col-span-2">
              <label for="address" class="block text-sm font-medium mb-1">Home address</label>
              <input id="address" name="address" value="<?= e($old['address']) ?>" class="<?= INPUT_CLS ?>" required>
            </div>
            <div>
              <label for="housing" class="block text-sm font-medium mb-1">Housing type</label>
              <select id="housing" name="housing" class="<?= INPUT_CLS ?>" required>
                <option value="">Choose one</option>
                <?php foreach ($housingOptions as $h): ?>
                  <option value="<?= e($h) ?>" <?= $old['housing'] === $h ? 'selected' : '' ?>><?= e($h) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label for="has_other_pets" class="block text-sm font-medium mb-1">Do you have other pets?</label>
              <select id="has_other_pets" name="has_other_pets" class="<?= INPUT_CLS ?>" required>
                <option value="">Choose one</option>
                <option value="Yes" <?= $old['has_other_pets'] === 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= $old['has_other_pets'] === 'No' ? 'selected' : '' ?>>No</option>
              </select>
            </div>
            <div class="sm:col-span-2">
              <label for="reason" class="block text-sm font-medium mb-1">Why do you want to adopt <?= e($pet['name']) ?>?</label>
              <textarea id="reason" name="reason" rows="4" class="<?= INPUT_CLS ?>" required><?= e($old['reason']) ?></textarea>
            </div>
            <div class="sm:col-span-2">
              <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-sun-400 hover:bg-sun-500 text-slate-900 font-semibold px-5 py-3">
                <i class="fa-solid fa-heart" aria-hidden="true"></i>Send adoption request
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
