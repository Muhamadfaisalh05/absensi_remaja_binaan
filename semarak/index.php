<?php include 'includes/header.php'; ?>

<div class="semarak-card p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-blue-500"></i> Rekapitulasi Disiplin & Karakter
        </h2>
        <div class="flex flex-wrap items-end gap-2">
            <label class="text-xs text-slate-600">Dari
                <input id="dashboardStart" type="date"
                    class="block border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
            </label>
            <label class="text-xs text-slate-600">Sampai
                <input id="dashboardEnd" type="date"
                    class="block border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
            </label>
            <button id="loadDashboardSummary" type="button"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded text-sm">Tampilkan</button>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="border border-green-200 bg-green-50 rounded-lg p-4">
            <p class="text-xs font-semibold text-green-700 mb-1">Rata-rata Sesuai</p>
            <p id="dashboard-sesuai" class="text-3xl font-bold text-green-600">0.0%</p>
            <p id="dashboard-count-sesuai" class="text-xs text-green-700 mt-1">0 evaluasi</p>
        </div>
        <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4">
            <p class="text-xs font-semibold text-yellow-700 mb-1">Dengan Arahan</p>
            <p id="dashboard-arahan" class="text-3xl font-bold text-yellow-600">0.0%</p>
            <p id="dashboard-count-arahan" class="text-xs text-yellow-700 mt-1">0 evaluasi</p>
        </div>
        <div class="border border-orange-200 bg-orange-50 rounded-lg p-4">
            <p class="text-xs font-semibold text-orange-700 mb-1">Terlambat</p>
            <p id="dashboard-telat" class="text-3xl font-bold text-orange-600">0.0%</p>
            <p id="dashboard-count-telat" class="text-xs text-orange-700 mt-1">0 evaluasi</p>
        </div>
        <div class="border border-red-200 bg-red-50 rounded-lg p-4">
            <p class="text-xs font-semibold text-red-700 mb-1">Tidak (Pelanggaran)</p>
            <p id="dashboard-tidak" class="text-3xl font-bold text-red-600">0.0%</p>
            <p id="dashboard-count-tidak" class="text-xs text-red-700 mt-1">0 evaluasi</p>
        </div>
    </div>

    <div class="border border-slate-200 rounded-lg p-4 bg-white mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-slate-700"><i class="fa-solid fa-chart-area mr-2 text-blue-500"></i>Distribusi
                Evaluasi</h3>
            <span id="dashboardTotal" class="text-sm text-slate-500">0 evaluasi</span>
        </div>
        <div id="dashboardBars" class="space-y-3" aria-live="polite"></div>
    </div>
    <p id="dashboardMessage" class="text-sm" role="status" aria-live="polite">Memuat ringkasan...</p>
</div>

<?php include 'includes/footer.php'; ?>