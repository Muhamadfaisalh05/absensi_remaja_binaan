<?php include 'includes/header.php'; ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="semarak-card p-5 lg:p-7 bg-white">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">
                        Input Evaluasi Karakter
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Penilaian harian disiplin dan kebiasaan anak asuh binaan</p>
                </div>
            </div>

            <!-- Date Selector -->
            <div class="flex items-center gap-2.5 bg-slate-50 border border-slate-200/90 rounded-2xl p-1.5 shadow-sm">
                <span class="text-xs font-semibold text-slate-600 pl-2">
                    <i class="fa-regular fa-calendar text-blue-500 mr-1"></i> Tanggal
                </span>
                <input id="attendanceDate" type="date"
                    class="border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-semibold bg-white text-slate-700 shadow-sm focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Weekly Input Panel (Collapsible) -->
    <section id="weeklyInputPanel" class="hidden semarak-card bg-gradient-to-br from-blue-50/70 to-indigo-50/40 border border-blue-200/80 p-5"
        aria-label="Input evaluasi mingguan">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-calendar-week"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Mode Kalender Mingguan</h3>
                    <p class="text-xs text-slate-500">Pilih hari untuk langsung mengisi absensi</p>
                </div>
            </div>

            <div class="flex items-center gap-2 bg-white rounded-xl border border-blue-200 p-1 shadow-sm">
                <button id="previousWeek" type="button"
                    class="w-8 h-8 flex items-center justify-center hover:bg-blue-50 text-blue-700 rounded-lg transition" aria-label="Minggu sebelumnya"
                    title="Minggu sebelumnya">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <h3 id="weeklyPeriod" class="font-bold text-xs text-slate-700 px-3 min-w-36 text-center"></h3>
                <button id="nextWeek" type="button"
                    class="w-8 h-8 flex items-center justify-center hover:bg-blue-50 text-blue-700 rounded-lg transition"
                    aria-label="Minggu berikutnya" title="Minggu berikutnya">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>
        <div id="weeklyDateButtons" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2"></div>
    </section>

    <!-- Absence / Leave Panel -->
    <div class="semarak-card bg-gradient-to-r from-amber-50/60 to-yellow-50/40 border border-amber-200/90 p-5 lg:p-6">
        <div class="flex items-center gap-2.5 mb-3.5">
            <span class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-xs">
                <i class="fa-solid fa-briefcase-medical"></i>
            </span>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-amber-900">
                    Panel Anak Sakit / Izin (Otomatis Libur Harian)
                </p>
                <p class="text-xs text-amber-700">Centang anak yang berhalangan hadir hari ini, mereka akan otomatis ditandai izin di seluruh kegiatan</p>
            </div>
        </div>

        <div id="absenceChildren" class="flex flex-wrap gap-2.5 text-xs text-slate-600 mb-4 bg-white/70 p-3.5 rounded-xl border border-amber-200/60">
            <span class="text-xs text-slate-400">Memuat daftar anak...</span>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="relative flex-1">
                <i class="fa-regular fa-comment-dots absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input id="absenceReason" type="text" maxlength="255" placeholder="Alasan izin / surat dokter (opsional)"
                    class="w-full border border-amber-300/80 rounded-xl pl-8 pr-3 py-2 text-xs bg-white text-slate-700 shadow-sm focus:border-amber-500">
            </div>
            <button id="saveAbsences" type="button"
                class="bg-amber-600 hover:bg-amber-700 active:scale-95 text-white font-semibold rounded-xl px-5 py-2 text-xs flex items-center justify-center gap-2 shadow-sm shadow-amber-900/10">
                <i class="fa-solid fa-check text-xs"></i> Simpan Status Izin
            </button>
        </div>
    </div>

    <!-- Feedback Message -->
    <div class="flex items-center gap-2">
        <p id="attendanceMessage" class="text-xs font-semibold px-3 py-1.5 rounded-lg inline-flex items-center" role="status" aria-live="polite"></p>
    </div>

    <!-- Activities Container -->
    <div id="activitiesContainer" class="space-y-4">
        <div class="semarak-card p-8 text-center text-slate-400">
            <i class="fa-solid fa-spinner fa-spin text-2xl text-blue-500 mb-2"></i>
            <p class="text-xs font-medium">Memuat data evaluasi kegiatan...</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>