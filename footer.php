  </main>
  <footer class="bg-brand-800 text-brand-100 mt-12">
    <div class="max-w-6xl mx-auto px-4 py-8 grid gap-6 sm:grid-cols-2">
      <div>
        <p class="font-bold text-white text-lg flex items-center gap-2"><i class="fa-solid fa-paw" aria-hidden="true"></i>AMPON</p>
        <p class="text-sm mt-2">A community portal for pet adoption and rescue in Leyte.</p>
      </div>
      <div class="text-sm space-y-2 sm:text-right">
        <p><a href="pets.php" class="hover:underline">Find a pet</a></p>
        <p><a href="events.php" class="hover:underline">Join an activity</a></p>
        <p><a href="stories.php" class="hover:underline">Read adoption stories</a></p>
      </div>
    </div>
    <div class="border-t border-brand-700 text-center text-xs py-4">&copy; <?= date('Y') ?> AMPON.</div>
  </footer>
  <script>
    var btn = document.getElementById('menuBtn');
    var menu = document.getElementById('mobileMenu');
    btn.addEventListener('click', function () {
      var open = menu.classList.toggle('hidden') === false;
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  </script>
</body>
</html>
