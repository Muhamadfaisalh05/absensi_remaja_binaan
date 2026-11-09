<?php include 'includes/header.php'; ?>

<div class="space-y-6">
    <!-- Top Filter & Banner Card -->
    <div class="semarak-card p-5 lg:p-7 bg-white">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">
                            Rekapitulasi Disiplin & Karakter
                        </h2>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Ringkasan performa dan kepatuhan kegiatan harian anak asuh</p>
                    </div>
                </div>
            </div>

            <!-- Date Range Filter Controls -->
            <div class="w-full lg:w-auto bg-slate-50 border border-slate-200/90 rounded-2xl p-2.5 flex flex-wrap items-center gap-2.5 shadow-sm">
                <div class="flex items-center gap-2 flex-1 sm:flex-initial">
                    <span class="text-xs font-semibold text-slate-500 pl-1">Dari</span>
                    <div class="relative">
                        <input id="dashboardStart" type="date"
                            class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-1 sm:flex-initial">
                    <span class="text-xs font-semibold text-slate-500">Sampai</span>
                    <div class="relative">
                        <input id="dashboardEnd" type="date"
                            class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-medium text-slate-700 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <button id="loadDashboardSummary" type="button"
                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm shadow-blue-500/30">
                    <i class="fa-solid fa-filter text-xs"></i> Tampilkan
                </button>
            </div>
        </div>
    </div>

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">
        <!-- Card Sesuai -->
        <div class="semarak-card p-5 border-l-4 border-l-emerald-500 relative overflow-hidden bg-gradient-to-br from-white to-emerald-50/30">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Rata-rata Sesuai</span>
                    <h3 id="dashboard-sesuai" class="text-3xl font-extrabold text-emerald-600 mt-2 tracking-tight">0.0%</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100/80 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-emerald-100 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <p id="dashboard-count-sesuai" class="text-xs font-medium text-emerald-800">0 evaluasi</p>
            </div>
        </div>

        <!-- Card Dengan Arahan -->
        <div class="semarak-card p-5 border-l-4 border-l-amber-500 relative overflow-hidden bg-gradient-to-br from-white to-amber-50/30">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Dengan Arahan</span>
                    <h3 id="dashboard-arahan" class="text-3xl font-extrabold text-amber-600 mt-2 tracking-tight">0.0%</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-100/80 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-hand-holding-hand"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-amber-100 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <p id="dashboard-count-arahan" class="text-xs font-medium text-amber-800">0 evaluasi</p>
            </div>
        </div>

        <!-- Card Terlambat -->
        <div class="semarak-card p-5 border-l-4 border-l-orange-500 relative overflow-hidden bg-gradient-to-br from-white to-orange-50/30">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-700">Terlambat</span>
                    <h3 id="dashboard-telat" class="text-3xl font-extrabold text-orange-600 mt-2 tracking-tight">0.0%</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-100/80 text-orange-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-orange-100 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                <p id="dashboard-count-telat" class="text-xs font-medium text-orange-800">0 evaluasi</p>
            </div>
        </div>

        <!-- Card Pelanggaran / Tidak -->
        <div class="semarak-card p-5 border-l-4 border-l-rose-500 relative overflow-hidden bg-gradient-to-br from-white to-rose-50/30">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Tidak (Pelanggaran)</span>
                    <h3 id="dashboard-tidak" class="text-3xl font-extrabold text-rose-600 mt-2 tracking-tight">0.0%</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-100/80 text-rose-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-rose-100 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                <p id="dashboard-count-tidak" class="text-xs font-medium text-rose-800">0 evaluasi</p>
            </div>
        </div>
    </div>

    <!-- Chart Distribution Card -->
    <div class="semarak-card p-6 bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-5 border-b border-slate-100 gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-chart-column"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm md:text-base">Distribusi Evaluasi Karakter</h3>
                    <p class="text-xs text-slate-400">Proporsi capaian dari seluruh indikator kegiatan</p>
                </div>
            </div>
            <span id="dashboardTotal" class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1 rounded-full border border-slate-200">
                <i class="fa-solid fa-layer-group text-[10px] text-slate-400"></i>
                0 evaluasi
            </span>
        </div>

        <div id="dashboardBars" class="space-y-4" aria-live="polite">
            <!-- Progress bars rendered by JS -->
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-2">
            <i class="fa-solid fa-info-circle text-xs text-blue-500"></i>
            <p id="dashboardMessage" class="text-xs font-medium text-slate-500" role="status" aria-live="polite">Memuat ringkasan...</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>