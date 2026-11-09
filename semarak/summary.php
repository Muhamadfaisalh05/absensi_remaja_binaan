<?php include 'includes/header.php'; ?>

<div class="space-y-6">
    <!-- Header & Filter Card -->
    <div class="semarak-card p-5 lg:p-7 bg-white">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">
                        Summary & Analisis Individu
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Analisis riwayat karakter dan perkembangan perilaku tiap anak</p>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="w-full lg:w-auto bg-slate-50 border border-slate-200/90 rounded-2xl p-2.5 flex flex-wrap items-center gap-2.5 shadow-sm">
                <div class="flex items-center gap-2 flex-1 sm:flex-initial">
                    <span class="text-xs font-semibold text-slate-500 pl-1">Anak</span>
                    <select id="reportChild"
                        class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-700 min-w-44 shadow-sm focus:border-blue-500">
                        <option value="">Memuat data anak...</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 flex-1 sm:flex-initial">
                    <span class="text-xs font-semibold text-slate-500">Dari</span>
                    <input id="reportStart" type="date"
                        class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm focus:border-blue-500">
                </div>

                <div class="flex items-center gap-2 flex-1 sm:flex-initial">
                    <span class="text-xs font-semibold text-slate-500">Sampai</span>
                    <input id="reportEnd" type="date"
                        class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm focus:border-blue-500">
                </div>

                <button id="loadChildReport" type="button"
                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm shadow-blue-500/30">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Tampilkan
                </button>
            </div>
        </div>
    </div>

    <!-- Child Profile Card -->
    <div class="semarak-card p-6 bg-gradient-to-r from-white via-slate-50/50 to-blue-50/20 border border-slate-200">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
            <div id="reportInitials"
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-teal-500 text-white flex items-center justify-center text-xl font-extrabold shadow-md shadow-blue-500/20 shrink-0">
                -
            </div>
            <div class="flex-1 text-center sm:text-left">
                <h3 id="reportChildName" class="text-xl font-extrabold text-slate-800 tracking-tight">Pilih anak</h3>
                <p id="reportChildDetail" class="text-xs text-slate-500 font-medium mt-1">-</p>

                <!-- Metadata Badges -->
                <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-3 text-xs">
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg border border-slate-200 font-medium">
                        ID: <strong id="reportChildId" class="text-slate-800 font-bold">-</strong>
                    </span>
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg border border-slate-200 font-medium">
                        Agama: <strong id="reportChildReligion" class="text-slate-800 font-bold">-</strong>
                    </span>
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg border border-slate-200 font-medium">
                        Suku: <strong id="reportChildTribe" class="text-slate-800 font-bold">-</strong>
                    </span>
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg border border-slate-200 font-medium">
                        Angkatan: <strong id="reportChildCohort" class="text-slate-800 font-bold">-</strong>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 5 Performance Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        <!-- Sesuai -->
        <div class="semarak-card p-4 bg-emerald-50/40 border-l-4 border-l-emerald-500 text-center">
            <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider mb-1">SESUAI</p>
            <p id="count-sesuai" class="text-2xl font-extrabold text-emerald-600">0</p>
            <p id="percent-sesuai" class="text-xs font-semibold text-emerald-700 mt-1">0.0%</p>
        </div>

        <!-- Arahan -->
        <div class="semarak-card p-4 bg-amber-50/40 border-l-4 border-l-amber-500 text-center">
            <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider mb-1">ARAHAN</p>
            <p id="count-arahan" class="text-2xl font-extrabold text-amber-600">0</p>
            <p id="percent-arahan" class="text-xs font-semibold text-amber-700 mt-1">0.0%</p>
        </div>

        <!-- Terlambat -->
        <div class="semarak-card p-4 bg-orange-50/40 border-l-4 border-l-orange-500 text-center">
            <p class="text-[11px] font-bold text-orange-700 uppercase tracking-wider mb-1">TERLAMBAT</p>
            <p id="count-telat" class="text-2xl font-extrabold text-orange-600">0</p>
            <p id="percent-telat" class="text-xs font-semibold text-orange-700 mt-1">0.0%</p>
        </div>

        <!-- Tidak -->
        <div class="semarak-card p-4 bg-rose-50/40 border-l-4 border-l-rose-500 text-center">
            <p class="text-[11px] font-bold text-rose-700 uppercase tracking-wider mb-1">TIDAK</p>
            <p id="count-tidak" class="text-2xl font-extrabold text-rose-600">0</p>
            <p id="percent-tidak" class="text-xs font-semibold text-rose-700 mt-1">0.0%</p>
        </div>

        <!-- Total Skor (Spesial Highlight) -->
        <div class="col-span-2 sm:col-span-1 semarak-card p-4 bg-gradient-to-br from-blue-600 to-teal-600 text-white text-center shadow-md shadow-blue-500/20">
            <p class="text-[11px] font-bold text-blue-100 uppercase tracking-wider mb-1">SKOR TOTAL</p>
            <p id="reportScore" class="text-2xl font-extrabold text-white">0.0%</p>
            <p class="text-xs text-teal-100 font-medium mt-1">Indeks Disiplin</p>
        </div>
    </div>

    <!-- Breakdown Table Card -->
    <div class="semarak-card overflow-hidden bg-white">
        <div class="bg-slate-50/80 px-5 py-4 border-b border-slate-200/80 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-list-check text-blue-600 text-sm"></i>
                <h4 class="text-sm font-bold text-slate-800">REKAPITULASI RINCI PER KEGIATAN</h4>
            </div>
            <span class="text-xs text-slate-400 font-medium">22 Indikator</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="text-xs font-bold text-slate-600 uppercase bg-slate-100/70 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Kegiatan</th>
                        <th class="px-4 py-3 text-center text-emerald-700">Sesuai</th>
                        <th class="px-4 py-3 text-center text-amber-700">Arahan</th>
                        <th class="px-4 py-3 text-center text-orange-700">Telat</th>
                        <th class="px-4 py-3 text-center text-rose-700">Tidak</th>
                        <th class="px-4 py-3 text-center text-slate-500">Libur</th>
                        <th class="px-4 py-3 text-center text-blue-700">Skor</th>
                    </tr>
                </thead>
                <tbody id="reportRows" class="divide-y divide-slate-100 font-medium text-slate-700">
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            <i class="fa-solid fa-user-clock text-2xl text-slate-300 mb-2 block"></i>
                            Pilih anak untuk melihat rekapitulasi data.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <p id="reportMessage" class="text-xs font-medium" role="status" aria-live="polite"></p>
</div>

<?php include 'includes/footer.php'; ?>