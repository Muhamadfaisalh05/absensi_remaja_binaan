        </main>
    </div>

    <!-- Desktop Footer Info -->
    <footer class="hidden md:block py-6 border-t border-slate-200/60 mt-12 text-center text-xs text-slate-400">
        <p>&copy; <?= date('Y') ?> SeMARAK &bull; UPTD Pusat Pelayanan Sosial Griya Bina Remaja (PPSGBR) Dinas Sosial Provinsi Jawa Barat</p>
    </footer>

    <!-- Mobile Floating Bottom Navigation -->
    <nav class="md:hidden fixed bottom-3 left-3 right-3 bg-white/95 backdrop-blur-lg shadow-xl shadow-slate-900/10 border border-slate-200/90 rounded-2xl z-50 px-2 py-2">
        <div class="flex justify-around items-center">
            <?php $curr = basename($_SERVER['PHP_SELF']); ?>
            <a href="index.php"
                class="flex flex-col items-center py-1 px-2 rounded-xl transition-all <?= $curr == 'index.php' ? 'text-blue-600 font-bold bg-blue-50' : 'text-slate-500 hover:text-blue-600' ?>">
                <i class="fa-solid fa-chart-pie text-base mb-0.5"></i>
                <span class="text-[10px]">Dashboard</span>
            </a>
            <a href="absensi.php"
                class="flex flex-col items-center py-1 px-2 rounded-xl transition-all <?= $curr == 'absensi.php' ? 'text-blue-600 font-bold bg-blue-50' : 'text-slate-500 hover:text-blue-600' ?>">
                <i class="fa-solid fa-list-check text-base mb-0.5"></i>
                <span class="text-[10px]">Absensi</span>
            </a>
            <a href="summary.php"
                class="flex flex-col items-center py-1 px-2 rounded-xl transition-all <?= $curr == 'summary.php' ? 'text-blue-600 font-bold bg-blue-50' : 'text-slate-500 hover:text-blue-600' ?>">
                <i class="fa-solid fa-users text-base mb-0.5"></i>
                <span class="text-[10px]">Summary</span>
            </a>
            <a href="report.php"
                class="flex flex-col items-center py-1 px-2 rounded-xl transition-all <?= $curr == 'report.php' ? 'text-blue-600 font-bold bg-blue-50' : 'text-slate-500 hover:text-blue-600' ?>">
                <i class="fa-solid fa-file-invoice text-base mb-0.5"></i>
                <span class="text-[10px]">Raport</span>
            </a>
            <a href="pengaturan.php"
                class="flex flex-col items-center py-1 px-2 rounded-xl transition-all <?= $curr == 'pengaturan.php' ? 'text-blue-600 font-bold bg-blue-50' : 'text-slate-500 hover:text-blue-600' ?>">
                <i class="fa-solid fa-gear text-base mb-0.5"></i>
                <span class="text-[10px]">Pengaturan</span>
            </a>
            <button id="mobileInputWeekly" type="button"
                class="flex flex-col items-center py-1 px-2 rounded-xl text-amber-600 hover:text-amber-700 transition">
                <i class="fa-solid fa-calendar-week text-base mb-0.5"></i>
                <span class="text-[10px] font-semibold">Mingguan</span>
            </button>
        </div>
    </nav>

    <script src="assets/js/script.js"></script>
</body>

</html>