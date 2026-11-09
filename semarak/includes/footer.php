</main>
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white shadow-[0_-2px_10px_rgba(0,0,0,0.1)] z-50">
    <div class="flex justify-around items-center py-3">
        <a href="index.php"
            class="flex flex-col items-center <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-blue-600' : 'text-slate-500' ?> hover:text-blue-600 transition">
            <i class="fa-solid fa-chart-pie mb-1"></i>
            <span class="text-[10px]">Dashboard</span>
        </a>
        <a href="absensi.php"
            class="flex flex-col items-center <?= basename($_SERVER['PHP_SELF']) == 'absensi.php' ? 'text-blue-600' : 'text-slate-500' ?> hover:text-blue-600 transition">
            <i class="fa-solid fa-list-check mb-1"></i>
            <span class="text-[10px]">Absensi</span>
        </a>
        <a href="summary.php"
            class="flex flex-col items-center <?= basename($_SERVER['PHP_SELF']) == 'summary.php' ? 'text-blue-600' : 'text-slate-500' ?> hover:text-blue-600 transition">
            <i class="fa-solid fa-users mb-1"></i>
            <span class="text-[10px]">Summary</span>
        </a>
        <a href="pengaturan.php"
            class="flex flex-col items-center <?= basename($_SERVER['PHP_SELF']) == 'pengaturan.php' ? 'text-blue-600' : 'text-slate-500' ?> hover:text-blue-600 transition">
            <i class="fa-solid fa-gear mb-1"></i>
            <span class="text-[10px]">Pengaturan</span>
        </a>
        <button id="mobileInputWeekly" type="button"
            class="flex flex-col items-center text-slate-500 hover:text-blue-600 transition">
            <i class="fa-solid fa-calendar-week mb-1"></i>
            <span class="text-[10px]">Mingguan</span>
        </button>
    </div>
</nav>
<script src="assets/js/script.js"></script>
</body>

</html>