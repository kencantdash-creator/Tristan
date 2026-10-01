<?php
require_once __DIR__ . '/../config.php';
$pageTitle = $pageTitle ?? 'AMPON';
$active = $active ?? '';
$nav = [
    'home'    => ['index.php',   'Home',       'fa-house'],
    'pets'    => ['pets.php',    'Find a Pet', 'fa-paw'],
    'events'  => ['events.php',  'Activities', 'fa-calendar-days'],
    'stories' => ['stories.php', 'Stories',    'fa-camera'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> - AMPON</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: {
        brand: { 50: '#eef6f3', 100: '#d6ebe4', 600: '#1f7a63', 700: '#18624f', 800: '#124a3c' },
        sun: { 400: '#f2b84b', 500: '#e8a317' }
      } } }
    };
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-stone-50 text-slate-800 min-h-screen flex flex-col">
  <header class="bg-white border-b border-stone-200 sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-2 font-bold text-xl text-brand-700">
        <span class="w-9 h-9 rounded-lg bg-brand-600 text-white flex items-center justify-center"><i class="fa-solid fa-paw" aria-hidden="true"></i></span>
        AMPON
      </a>
      <nav class="hidden md:flex items-center gap-1" aria-label="Main">
        <?php foreach ($nav as $key => [$href, $label, $icon]): ?>
          <a href="<?= $href ?>" class="px-3 py-2 rounded-lg text-sm font-medium flex items-center gap-2 <?= $active === $key ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-stone-100' ?>">
            <i class="fa-solid <?= $icon ?>" aria-hidden="true"></i><?= $label ?>
          </a>
        <?php endforeach; ?>
      </nav>
      <button id="menuBtn" class="md:hidden w-10 h-10 rounded-lg border border-stone-300 flex items-center justify-center" aria-label="Open menu" aria-expanded="false">
        <i class="fa-solid fa-bars" aria-hidden="true"></i>
      </button>
    </div>
    <nav id="mobileMenu" class="hidden md:hidden border-t border-stone-200 px-4 py-2 bg-white" aria-label="Mobile">
      <?php foreach ($nav as $key => [$href, $label, $icon]): ?>
        <a href="<?= $href ?>" class="flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium <?= $active === $key ? 'bg-brand-50 text-brand-700' : 'text-slate-700' ?>">
          <i class="fa-solid <?= $icon ?> w-5" aria-hidden="true"></i><?= $label ?>
        </a>
      <?php endforeach; ?>
    </nav>
  </header>
  <main class="flex-1">
