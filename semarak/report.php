<?php include 'includes/header.php'; ?>

<div class="space-y-6">
    <!-- Header & Filter Controls Card (No-print) -->
    <div class="semarak-card p-5 lg:p-7 bg-white no-print">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">
                        Cetak Dokumen Raport
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Generate dan cetak lembar evaluasi pilar karakter binaan resmi</p>
                </div>
            </div>

            <!-- Controls -->
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

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button id="loadChildReport" type="button"
                        class="flex-1 sm:flex-initial bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm shadow-blue-500/30">
                        <i class="fa-solid fa-arrows-rotate text-xs"></i> Tampilkan
                    </button>
                    <button id="printReport" type="button"
                        class="flex-1 sm:flex-initial bg-slate-800 hover:bg-slate-900 active:scale-95 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm shadow-slate-900/20">
                        <i class="fa-solid fa-print text-xs text-amber-300"></i> Cetak / PDF
                    </button>
                </div>
            </div>
        </div>
        <p id="reportMessage" class="mt-4 text-xs font-medium" role="status" aria-live="polite"></p>
    </div>

    <!-- Official Report Document Paper (Printable) -->
    <div class="print-sheet bg-white border border-slate-200/90 rounded-2xl p-8 lg:p-12 max-w-4xl mx-auto shadow-md">
        <!-- Kop Surat Resmi -->
        <div class="text-center mb-6 kop-surat-border pb-4">
            <h3 class="font-bold text-base md:text-lg tracking-wider text-slate-800 uppercase">Pemerintah Daerah Provinsi Jawa Barat</h3>
            <h2 class="font-extrabold text-xl md:text-2xl tracking-wide text-slate-900 uppercase">Dinas Sosial</h2>
            <h4 class="font-bold text-sm md:text-base text-slate-800 uppercase">Balai / UPTD Pusat Pelayanan Sosial Griya Bina Remaja</h4>
            <p class="text-xs text-slate-600 mt-1">Jalan Maribaya No. 22 Lembang, Bandung Barat, Jawa Barat 40391 &bull; Telepon: (022) 2786120</p>
            <p class="text-xs text-slate-500">Website: www.dinsos.ppsgbr.jabarprov.go.id &bull; Pos-el: uptdppsgbr@gmail.com</p>
        </div>

        <!-- Judul Raport -->
        <div class="flex flex-col sm:flex-row justify-between items-center bg-blue-50/80 border border-blue-200/90 px-4 py-2.5 rounded-xl mb-6 gap-2">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-award text-blue-600 text-sm"></i>
                <span class="font-bold text-xs md:text-sm tracking-wide text-blue-950 uppercase">Evaluasi Karakter & Disiplin Anak Asuh</span>
            </div>
            <span class="bg-blue-600 text-white text-[11px] px-3 py-1 rounded-full font-bold uppercase tracking-wider shadow-sm">
                Raport Pilar Karakter
            </span>
        </div>

        <!-- Biodata Anak -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-6 bg-slate-50/60 p-4 rounded-xl border border-slate-200/80">
            <div class="space-y-1.5">
                <p class="flex"><span class="font-semibold text-slate-500 w-36 shrink-0">Nama & ID Anak:</span> <span class="font-bold text-slate-800"><span id="reportChildName">-</span> (<span id="reportChildId">-</span>)</span></p>
                <p class="flex"><span class="font-semibold text-slate-500 w-36 shrink-0">Jenis Kelamin:</span> <span id="reportChildGender" class="font-medium text-slate-700">-</span></p>
                <p class="flex"><span class="font-semibold text-slate-500 w-36 shrink-0">Agama:</span> <span id="reportChildReligion" class="font-medium text-slate-700">-</span></p>
                <p class="flex"><span class="font-semibold text-slate-500 w-36 shrink-0">Suku Bangsa:</span> <span id="reportChildTribe" class="font-medium text-slate-700">-</span></p>
                <p class="flex"><span class="font-semibold text-slate-500 w-36 shrink-0">Angkatan / Gelombang:</span> <span id="reportChildCohort" class="font-medium text-slate-700">-</span></p>
            </div>
            <div class="space-y-1.5">
                <p class="flex"><span class="font-semibold text-slate-500 w-36 shrink-0">Periode Evaluasi:</span> <span id="reportPeriod" class="font-bold text-blue-700">-</span></p>
                <p class="flex"><span class="font-semibold text-slate-500 w-36 shrink-0">Status Pembinaan:</span> <span class="font-medium text-emerald-700">Aktif Binaan</span></p>
                <p class="flex"><span class="font-semibold text-slate-500 w-36 shrink-0">Standar Karakter:</span> <span class="font-medium text-slate-700">Berehan, Motekar, Calakan, Berseka, Waringkas</span></p>
            </div>
        </div>

        <!-- Tabel Hasil Evaluasi -->
        <div class="border border-slate-200 rounded-xl overflow-hidden mb-6">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2.5">Kegiatan</th>
                        <th class="px-2 py-2.5 text-center text-emerald-700">Sesuai</th>
                        <th class="px-2 py-2.5 text-center text-amber-700">Arahan</th>
                        <th class="px-2 py-2.5 text-center text-orange-700">Telat</th>
                        <th class="px-2 py-2.5 text-center text-rose-700">Tidak</th>
                        <th class="px-2 py-2.5 text-center text-slate-500">Izin</th>
                        <th class="px-2 py-2.5 text-center text-blue-700">Skor</th>
                    </tr>
                </thead>
                <tbody id="reportRows" class="divide-y divide-slate-100 font-medium text-slate-700">
                    <tr>
                        <td colspan="7" class="px-3 py-6 text-center text-slate-400">Pilih anak untuk memuat data raport.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Catatan & Rekomendasi -->
        <div class="border border-slate-200 rounded-xl overflow-hidden mb-8">
            <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200 font-bold text-xs text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-comments text-blue-500"></i> CATATAN PERILAKU & REKOMENDASI PENGEMBANGAN
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-1.5">Catatan Perilaku & Kedisiplinan:</p>
                    <div class="border border-slate-200 rounded-lg h-28 bg-slate-50/50 p-3 text-xs text-slate-500 italic">
                        Menunjukkan kepatuhan yang konsisten dan interaksi yang baik dalam kegiatan harian.
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-1.5">Rekomendasi Pembina / Instruktur:</p>
                    <div class="border border-slate-200 rounded-lg h-28 bg-slate-50/50 p-3 text-xs text-slate-500 italic">
                        Perlu peningkatan keaktifan pada kegiatan kemandirian dan keterampilan vokasi.
                    </div>
                </div>
            </div>
        </div>

        <!-- Area Tanda Tangan Resmi Dokumen -->
        <div class="grid grid-cols-2 gap-8 text-xs text-slate-700 pt-4">
            <div class="text-center">
                <p class="font-medium text-slate-500 mb-16">Pekerja Sosial / Pembina Asrama,</p>
                <p class="font-bold underline text-slate-900">( .................................................... )</p>
                <p class="text-[11px] text-slate-400 mt-0.5">NIP. ....................................................</p>
            </div>
            <div class="text-center">
                <p class="font-medium text-slate-500">Lembang, <?= date('d F Y') ?></p>
                <p class="font-medium text-slate-500 mb-16">Kepala UPTD PPSGBR,</p>
                <p class="font-bold underline text-slate-900">( .................................................... )</p>
                <p class="text-[11px] text-slate-400 mt-0.5">NIP. ....................................................</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>