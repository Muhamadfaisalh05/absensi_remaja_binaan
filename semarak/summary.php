<?php include 'includes/header.php'; ?>

<div class="semarak-card p-6">
    <div class="flex flex-col lg:flex-row justify-between lg:items-end mb-6 gap-4">
        <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-user-check text-blue-500"></i> Summary & Analisis Individu
        </h2>
        <div class="flex flex-wrap items-end gap-2">
            <label class="text-xs text-slate-600">Anak
                <select id="reportChild"
                    class="block border border-slate-300 rounded px-3 py-1.5 text-sm bg-white min-w-44">
                    <option value="">Memuat...</option>
                </select>
            </label>
            <label class="text-xs text-slate-600">Dari
                <input id="reportStart" type="date"
                    class="block border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
            </label>
            <label class="text-xs text-slate-600">Sampai
                <input id="reportEnd" type="date"
                    class="block border border-slate-300 rounded px-3 py-1.5 text-sm bg-white">
            </label>
            <button id="loadChildReport" type="button"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded text-sm">Tampilkan</button>
        </div>
    </div>

    <div class="border border-slate-200 rounded-lg p-6 flex items-center gap-6 mb-6">
        <div id="reportInitials"
            class="w-16 h-16 bg-blue-600 text-white rounded-full flex items-center justify-center text-2xl font-bold">
            -
        </div>
        <div>
            <h3 id="reportChildName" class="font-bold text-lg">Pilih anak</h3>
            <p id="reportChildDetail" class="text-sm text-slate-500">-</p>
        </div>
        <div class="ml-auto text-right text-sm">
            <p class="text-slate-500">ID: <span id="reportChildId">-</span></p>
            <p class="text-slate-500">Agama: <span id="reportChildReligion">-</span></p>
            <p class="text-slate-500">Suku: <span id="reportChildTribe">-</span></p>
            <p class="text-slate-500">Angkatan: <span id="reportChildCohort">-</span></p>
        </div>
    </div>

    <div class="grid grid-cols-5 gap-4 mb-6 text-center">
        <div class="bg-green-50 border border-green-200 rounded-lg py-4">
            <p class="text-xs font-bold text-green-700 mb-1">SESUAI</p>
            <p id="count-sesuai" class="text-2xl font-bold text-green-600">0</p>
            <p id="percent-sesuai" class="text-xs text-green-600">0.0%</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg py-4">
            <p class="text-xs font-bold text-yellow-700 mb-1">ARAHAN</p>
            <p id="count-arahan" class="text-2xl font-bold text-yellow-600">0</p>
            <p id="percent-arahan" class="text-xs text-yellow-600">0.0%</p>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-lg py-4">
            <p class="text-xs font-bold text-orange-700 mb-1">TERLAMBAT</p>
            <p id="count-telat" class="text-2xl font-bold text-orange-600">0</p>
            <p id="percent-telat" class="text-xs text-orange-600">0.0%</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg py-4">
            <p class="text-xs font-bold text-red-700 mb-1">TIDAK</p>
            <p id="count-tidak" class="text-2xl font-bold text-red-600">0</p>
            <p id="percent-tidak" class="text-xs text-red-600">0.0%</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg py-4">
            <p class="text-xs font-bold text-blue-700 mb-1">SKOR TOTAL</p>
            <p id="reportScore" class="text-2xl font-bold text-blue-600">0.0%</p>
        </div>
    </div>

    <div class="border border-slate-200 rounded-lg overflow-hidden">
        <div class="bg-slate-50 px-4 py-3 border-b border-slate-200">
            <h4 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-list-ul mr-2"></i> REKAPITULASI RINCI PER
                KEGIATAN (22 KEGIATAN)</h4>
        </div>
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-500 bg-white border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Kegiatan</th>
                    <th class="px-4 py-3 text-center">Sesuai</th>
                    <th class="px-4 py-3 text-center">Arahan</th>
                    <th class="px-4 py-3 text-center">Telat</th>
                    <th class="px-4 py-3 text-center">Tidak</th>
                    <th class="px-4 py-3 text-center">Libur</th>
                    <th class="px-4 py-3 text-center">Skor</th>
                </tr>
            </thead>
            <tbody id="reportRows">
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-slate-500">Pilih anak untuk melihat rekap.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p id="reportMessage" class="mt-4 text-sm" role="status" aria-live="polite"></p>
</div>

<?php include 'includes/footer.php'; ?>