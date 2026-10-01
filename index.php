<?php
require __DIR__ . '/config.php';

$pets = $pdo->query(
    "SELECT p.*, s.municipality FROM pets p JOIN shelters s ON s.id = p.shelter_id
     WHERE p.status = 'available' ORDER BY p.created_at DESC, p.id DESC LIMIT 4"
)->fetchAll();

$events = $pdo->query(
    "SELECT e.*, (SELECT COUNT(*) FROM event_signups x WHERE x.event_id = e.id) AS joined
     FROM events e WHERE e.event_date >= CURDATE() ORDER BY e.event_date ASC LIMIT 3"
)->fetchAll();

$features = [
    ['fa-paw',               'Pet Listings',            'Browse dogs, cats, puppies, and kittens from shelters in Leyte.', 'pets.php'],
    ['fa-hand-holding-heart','Adoption Opportunities',  'Find adoption days and outreach where you can join as an adopter or volunteer.', 'events.php'],
    ['fa-tags',              'Event Categories',        'Filter activities by Adoption, Lost & Found, Rescue, or Vaccination Drive.', 'events.php'],
    ['fa-notes-medical',     'Pet Information',         'See breed, age, vaccination status, and health details for each pet.', 'pets.php'],
    ['fa-clock',             'Best Time to Visit',      'Check the best days and hours to visit a shelter and meet a pet.', 'pets.php'],
    ['fa-map-location-dot',  'Shelter Map Preview',     'Preview the shelter on Google Maps and get directions before you go.', 'pets.php'],
    ['fa-location-dot',      'Location',                'Know the municipality, barangay, or venue where adopters will meet.', 'events.php'],
    ['fa-camera',            'Photos & Experiences',    'Read stories from adopters who gave a pet a home.', 'stories.php'],
    ['fa-heart',             'Adopter Participation',   'Tap "I Want to Adopt" to see the requirements and send your request.', 'pets.php'],
];

$pageTitle = 'Home';
$active = 'home';
require __DIR__ . '/includes/header.php';
?>
<section class="bg-brand-700 text-white">
  <div class="max-w-6xl mx-auto px-4 py-14 md:py-20 grid md:grid-cols-2 gap-10 items-center">
    <div>
      <h1 class="text-3xl md:text-5xl font-bold leading-tight">Every pet in Leyte deserves a home.</h1>
      <p class="mt-4 text-brand-100 text-lg max-w-xl">AMPON helps you adopt a pet, find a lost one, and join rescue and vaccination activities near you.</p>
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="pets.php" class="inline-flex items-center gap-2 bg-sun-400 hover:bg-sun-500 text-slate-900 font-semibold rounded-lg px-5 py-3">
          <i class="fa-solid fa-paw" aria-hidden="true"></i>Find a pet
        </a>
        <a href="events.php" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 rounded-lg px-5 py-3 font-semibold">
          <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>See activities
        </a>
      </div>
    </div>
    <div class="hidden md:grid grid-cols-2 gap-4" aria-hidden="true">
      <div class="bg-brand-600 rounded-2xl h-40 flex items-center justify-center"><i class="fa-solid fa-dog text-7xl text-brand-100"></i></div>
      <div class="bg-sun-400 rounded-2xl h-40 flex items-center justify-center"><i class="fa-solid fa-cat text-7xl text-slate-900"></i></div>
      <div class="bg-sun-400 rounded-2xl h-40 flex items-center justify-center"><i class="fa-solid fa-bone text-7xl text-slate-900"></i></div>
      <div class="bg-brand-600 rounded-2xl h-40 flex items-center justify-center"><i class="fa-solid fa-heart text-7xl text-brand-100"></i></div>
    </div>
  </div>
</section>

<section class="max-w-6xl mx-auto px-4 mt-12">
  <h2 class="text-2xl font-bold text-slate-900">What you can do on AMPON</h2>
  <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach ($features as [$icon, $title, $text, $href]): ?>
      <a href="<?= $href ?>" class="bg-white rounded-xl border border-stone-200 p-5 hover:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-600">
        <span class="w-11 h-11 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-lg"><i class="fa-solid <?= $icon ?>" aria-hidden="true"></i></span>
        <h3 class="font-semibold text-slate-900 mt-3"><?= e($title) ?></h3>
        <p class="text-sm text-slate-600 mt-1"><?= e($text) ?></p>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="max-w-6xl mx-auto px-4 mt-14">
  <div class="flex items-end justify-between gap-4">
    <h2 class="text-2xl font-bold text-slate-900">Pets waiting for a home</h2>
    <a href="pets.php" class="text-sm font-medium text-brand-700 hover:underline">View all pets</a>
  </div>
  <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ($pets as $p) pet_card($p); ?>
    <?php if (!$pets): ?><p class="text-slate-600">No pets are listed right now. Check back soon.</p><?php endif; ?>
  </div>
</section>

<section class="max-w-6xl mx-auto px-4 mt-14">
  <div class="flex items-end justify-between gap-4">
    <h2 class="text-2xl font-bold text-slate-900">Coming up</h2>
    <a href="events.php" class="text-sm font-medium text-brand-700 hover:underline">View all activities</a>
  </div>
  <div class="mt-6 space-y-4">
    <?php foreach ($events as $ev) event_card($ev); ?>
    <?php if (!$events): ?><p class="text-slate-600">No upcoming activities yet.</p><?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
