<?php include 'includes/header.php'; ?>

<div class="space-y-6">
    <!-- Card 1: Pengaturan Jadwal Kegiatan -->
    <div class="semarak-card p-5 lg:p-7 bg-white">
        <div class="flex items-center gap-3.5 mb-6">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">
                    Pengaturan Jadwal Kegiatan Dinamis
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Konfigurasi hari aktif untuk tiap kegiatan (harian, hari kerja, atau rentang tanggal)</p>
            </div>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-xl mb-5">
            <table class="w-full text-xs text-left">
                <thead class="text-xs font-bold text-slate-600 uppercase bg-slate-100/70 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 w-12 text-center">No</th>
                        <th class="px-4 py-3">Uraian Kegiatan</th>
                        <th class="px-4 py-3 w-80">Aturan Jadwal Aktif</th>
                    </tr>
                </thead>
                <tbody id="scheduleRows" class="divide-y divide-slate-100 font-medium text-slate-700">
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-400">
                            <i class="fa-solid fa-spinner fa-spin text-lg text-amber-500 mb-1 block"></i>
                            Memuat aturan jadwal kegiatan...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-2">
            <p id="scheduleMessage" class="text-xs font-medium" role="status" aria-live="polite"></p>
            <button id="saveSchedules" type="button"
                class="w-full sm:w-auto bg-amber-500 hover:bg-amber-600 active:scale-95 text-slate-900 font-bold px-6 py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm shadow-amber-900/20">
                <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Aturan Jadwal
            </button>
        </div>
    </div>

    <!-- Card 2: Manajemen Data Siswa Binaan -->
    <div class="semarak-card p-5 lg:p-7 bg-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-6 border-b border-slate-100 gap-3">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">
                        Data Siswa Anak Binaan
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola data identitas dan status kepesertaan anak asuh</p>
                </div>
            </div>
            <span id="studentCount" class="inline-flex items-center gap-1.5 bg-teal-50 text-teal-800 text-xs font-bold px-3 py-1.5 rounded-full border border-teal-200">
                0 siswa
            </span>
        </div>

        <!-- Form Tambah Siswa Manual -->
        <form id="studentForm" class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 mb-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-teal-600"></i> Tambah Siswa Baru
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3.5 items-end">
                <label class="text-xs font-semibold text-slate-600 sm:col-span-2">Nama Lengkap Siswa <span class="text-rose-500">*</span>
                    <input id="studentName" type="text" maxlength="120" required placeholder="Contoh: Muhammad Fikri"
                        class="mt-1 block w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 bg-white shadow-sm focus:border-teal-500">
                </label>
                <label class="text-xs font-semibold text-slate-600">Jenis Kelamin
                    <select id="studentGender"
                        class="mt-1 block w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 bg-white shadow-sm focus:border-teal-500">
                        <option value="">Pilih gender</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </label>
                <label class="text-xs font-semibold text-slate-600">Angkatan / Gelombang
                    <input id="studentCohort" type="text" maxlength="80" placeholder="Contoh: Angkatan 2026"
                        class="mt-1 block w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 bg-white shadow-sm focus:border-teal-500">
                </label>
                <label class="text-xs font-semibold text-slate-600">Agama
                    <select id="studentReligion"
                        class="mt-1 block w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 bg-white shadow-sm focus:border-teal-500">
                        <option value="">Pilih agama (opsional)</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Budha">Budha</option>
                        <option value="Konghuchu">Konghuchu</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </label>
                <label class="text-xs font-semibold text-slate-600">Suku Bangsa
                    <input id="studentTribe" type="text" maxlength="80" placeholder="Contoh: Sunda, Jawa"
                        class="mt-1 block w-full border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 bg-white shadow-sm focus:border-teal-500">
                </label>
                <div class="sm:col-span-2 md:col-span-2 flex justify-end">
                    <button type="submit"
                        class="w-full sm:w-auto bg-teal-600 hover:bg-teal-700 active:scale-95 text-white font-bold px-5 py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm shadow-teal-700/20">
                        <i class="fa-solid fa-plus text-xs"></i> Tambah Siswa
                    </button>
                </div>
            </div>
        </form>

        <!-- Box Import Berkas (Excel/Word) -->
        <div class="bg-blue-50/40 border border-blue-200/80 rounded-2xl p-5 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <i class="fa-solid fa-file-excel text-emerald-600 text-sm"></i>
                <label for="studentImportFile" class="text-xs font-bold uppercase tracking-wider text-slate-700">Impor Data Siswa Sekaligus (Excel / Word / CSV)</label>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <input id="studentImportFile" type="file" accept=".xlsx,.xls,.csv,.docx"
                    class="block w-full text-xs text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-blue-600 file:text-white file:px-4 file:py-2 file:text-xs file:font-semibold hover:file:bg-blue-700 cursor-pointer">
                <button id="importStudents" type="button" disabled
                    class="shrink-0 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white font-bold px-5 py-2 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-file-import text-xs"></i> Impor Terpilih
                </button>
            </div>
            <p class="mt-2.5 text-[11px] text-slate-500 leading-relaxed">
                <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> Format berkas didukung: <strong>.xlsx, .xls, .csv</strong> (kolom: nama, gender, agama, suku, angkatan) atau dokumen <strong>.docx</strong> (tabel/daftar per baris).
            </p>

            <div id="studentPreview" class="hidden mt-4 overflow-x-auto border border-blue-200 bg-white rounded-xl shadow-sm">
                <table class="w-full text-xs text-left">
                    <thead class="bg-blue-50 text-slate-700 font-bold border-b border-blue-200">
                        <tr>
                            <th class="px-3 py-2 w-10">Pilih</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Gender</th>
                            <th class="px-3 py-2">Agama</th>
                            <th class="px-3 py-2">Suku</th>
                            <th class="px-3 py-2">Angkatan</th>
                            <th class="px-3 py-2">Status Validasi</th>
                        </tr>
                    </thead>
                    <tbody id="studentPreviewRows" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>

        <p id="studentMessage" class="mb-4 text-xs font-medium" role="status" aria-live="polite"></p>

        <!-- Tabel Daftar Siswa -->
        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="w-full text-xs text-left">
                <thead class="text-xs font-bold text-slate-600 uppercase bg-slate-100/70 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 w-16">ID</th>
                        <th class="px-4 py-3">Nama Siswa</th>
                        <th class="px-4 py-3">Gender</th>
                        <th class="px-4 py-3">Agama</th>
                        <th class="px-4 py-3">Suku</th>
                        <th class="px-4 py-3">Angkatan</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="studentRows" class="divide-y divide-slate-100 font-medium text-slate-700">
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                            <i class="fa-solid fa-spinner fa-spin text-lg text-teal-600 mb-1 block"></i>
                            Memuat daftar siswa binaan...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Card 3: Pengaturan Masa Pembinaan -->
    <div class="semarak-card p-5 lg:p-7 bg-white">
        <div class="flex items-center gap-3.5 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-lg">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">
                    Pengaturan Masa Pembinaan (Kelulusan Otomatis)
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Penetapan tanggal berakhirnya masa pembinaan untuk tiap angkatan</p>
            </div>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-xl mb-5">
            <table class="w-full text-xs text-left">
                <thead class="text-xs font-bold text-slate-600 uppercase bg-slate-100/70 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Angkatan</th>
                        <th class="px-4 py-3">Total Anak</th>
                        <th class="px-4 py-3">Tanggal Selesai Pembinaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <tr>
                        <td class="px-4 py-3 font-bold text-slate-800">Angkatan II</td>
                        <td class="px-4 py-3"><span class="bg-purple-50 text-purple-700 border border-purple-200 px-2.5 py-1 rounded-lg font-semibold">50 Anak</span></td>
                        <td class="px-4 py-3">
                            <input type="date" class="border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-700 bg-white shadow-sm focus:border-purple-500">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <button type="button" class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 active:scale-95 text-white font-bold px-6 py-2.5 rounded-xl text-xs flex items-center justify-center gap-2 shadow-sm shadow-purple-600/30">
                <i class="fa-solid fa-check-double text-xs"></i> Simpan & Terapkan Aturan
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mammoth@1.9.0/mammoth.browser.min.js"></script>
<?php include 'includes/footer.php'; ?>