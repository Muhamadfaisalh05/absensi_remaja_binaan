<?php include 'includes/header.php'; ?>

<div class="semarak-card p-6">
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-list-check text-blue-500"></i> Input Evaluasi Karakter
        </h2>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            Tanggal evaluasi
            <input id="attendanceDate" type="date" class="border border-slate-300 rounded px-3 py-1.5 bg-white">
        </label>
    </div>

    <section id="weeklyInputPanel" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6"
        aria-label="Input evaluasi mingguan">
        <div class="flex justify-between items-center gap-3 mb-3">
            <button id="previousWeek" type="button"
                class="border border-blue-200 bg-white text-blue-700 rounded px-3 py-2" aria-label="Minggu sebelumnya"
                title="Minggu sebelumnya">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <h3 id="weeklyPeriod" class="font-semibold text-slate-700 text-center"></h3>
            <button id="nextWeek" type="button" class="border border-blue-200 bg-white text-blue-700 rounded px-3 py-2"
                aria-label="Minggu berikutnya" title="Minggu berikutnya">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
        <div id="weeklyDateButtons" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2"></div>
    </section>

    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <p class="text-sm font-medium text-yellow-800 mb-3"><i class="fa-solid fa-briefcase-medical mr-2"></i>PANEL ANAK
            SAKIT / IZIN (OTOMATIS LIBUR HARIAN)</p>
        <div id="absenceChildren" class="flex flex-wrap gap-3 text-sm text-slate-500">Memuat daftar anak...</div>
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <input id="absenceReason" type="text" maxlength="255" placeholder="Alasan izin (opsional)"
                class="border border-yellow-300 rounded px-3 py-1.5 text-sm bg-white">
            <button id="saveAbsences" type="button"
                class="text-sm bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded px-4 py-1.5">Simpan
                izin</button>
        </div>
    </div>

    <p id="attendanceMessage" class="mb-4 text-sm" role="status" aria-live="polite"></p>
    <div id="activitiesContainer" class="space-y-3">Memuat data evaluasi...</div>
</div>

<?php include 'includes/footer.php'; ?>