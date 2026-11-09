<?php include 'includes/header.php'; ?>

<div class="semarak-card p-6 mb-6">
    <h2 class="text-lg font-bold text-slate-700 mb-6 flex items-center gap-2">
        <i class="fa-solid fa-calendar-days text-orange-500"></i> Pengaturan Jadwal Kegiatan (Dinamis)
    </h2>

    <div class="overflow-x-auto border border-slate-200 rounded-lg mb-6">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-600 bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 w-12 text-center">No</th>
                    <th class="px-4 py-3">Uraian Kegiatan</th>
                    <th class="px-4 py-3 w-80">Aturan Jadwal Aktif</th>
                </tr>
            </thead>
            <tbody id="scheduleRows">
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-slate-500">Memuat jadwal...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="text-right">
        <button id="saveSchedules" type="button"
            class="bg-orange-500 hover:bg-orange-600 text-white font-medium px-6 py-2 rounded shadow-sm">
            Simpan Jadwal
        </button>
    </div>
    <p id="scheduleMessage" class="mt-3 text-sm" role="status" aria-live="polite"></p>
</div>

<div class="semarak-card p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
        <h2 class="text-lg font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-teal-600"></i> Data Siswa
        </h2>
        <span id="studentCount" class="text-sm text-slate-500"></span>
    </div>

    <form id="studentForm" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end border-b border-slate-200 pb-6 mb-6">
        <label class="text-sm text-slate-600 md:col-span-2">Nama siswa
            <input id="studentName" type="text" maxlength="120" required
                class="mt-1 block w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white">
        </label>
        <label class="text-sm text-slate-600">Jenis kelamin
            <select id="studentGender"
                class="mt-1 block w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white">
                <option value="">Belum diisi</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </label>
        <label class="text-sm text-slate-600">Angkatan
            <input id="studentCohort" type="text" maxlength="80"
                class="mt-1 block w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white">
        </label>
        <label class="text-sm text-slate-600">Agama
            <select id="studentReligion"
                class="mt-1 block w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white">
                <option value="">Pilih agama (opsional)</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Hindu">Hindu</option>
                <option value="Budha">Budha</option>
                <option value="Konghuchu">Konghuchu</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </label>
        <label class="text-sm text-slate-600">Suku
            <input id="studentTribe" type="text" maxlength="80"
                class="mt-1 block w-full border border-slate-300 rounded px-3 py-2 text-sm bg-white">
        </label>
        <div class="md:col-span-4 flex justify-end">
            <button type="submit" class="bg-teal-700 hover:bg-teal-800 text-white font-medium px-5 py-2 rounded">
                <i class="fa-solid fa-plus mr-1"></i> Tambah siswa
            </button>
        </div>
    </form>

    <div class="border-b border-slate-200 pb-6 mb-6">
        <label for="studentImportFile" class="block text-sm font-medium text-slate-700 mb-2">Impor dari berkas</label>
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <input id="studentImportFile" type="file" accept=".xlsx,.xls,.csv,.docx"
                class="block w-full text-sm text-slate-600 file:mr-3 file:rounded file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium">
            <button id="importStudents" type="button" disabled
                class="shrink-0 bg-blue-700 hover:bg-blue-800 disabled:bg-slate-300 text-white font-medium px-5 py-2 rounded">
                <i class="fa-solid fa-file-import mr-1"></i> Impor terpilih
            </button>
        </div>
        <p class="mt-2 text-xs text-slate-500">Excel/CSV: kolom nama wajib; gender (L/P), agama/religion, suku/tribe,
            dan cohort opsional. Word:
            tabel atau daftar satu siswa per baris (.docx).</p>
        <div id="studentPreview" class="hidden mt-4 overflow-x-auto border border-slate-200 rounded">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-xs text-slate-600">
                    <tr>
                        <th class="px-3 py-2">Impor</th>
                        <th class="px-3 py-2">Nama</th>
                        <th class="px-3 py-2">Gender</th>
                        <th class="px-3 py-2">Agama</th>
                        <th class="px-3 py-2">Suku</th>
                        <th class="px-3 py-2">Angkatan</th>
                        <th class="px-3 py-2">Status</th>
                    </tr>
                </thead>
                <tbody id="studentPreviewRows"></tbody>
            </table>
        </div>
    </div>

    <p id="studentMessage" class="mb-4 text-sm" role="status" aria-live="polite"></p>
    <div class="overflow-x-auto border border-slate-200 rounded">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-xs text-slate-600">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Gender</th>
                    <th class="px-4 py-3">Agama</th>
                    <th class="px-4 py-3">Suku</th>
                    <th class="px-4 py-3">Angkatan</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody id="studentRows">
                <tr>
                    <td colspan="7" class="px-4 py-5 text-center text-slate-500">Memuat siswa...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="semarak-card p-6 mb-6">
    <h2 class="text-lg font-bold text-slate-700 mb-6 flex items-center gap-2">
        <i class="fa-solid fa-graduation-cap text-purple-500"></i> Pengaturan Masa Pembinaan (Kelulusan Otomatis)
    </h2>
    <div class="overflow-x-auto border border-slate-200 rounded-lg mb-4">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-slate-600 bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3">Angkatan</th>
                    <th class="px-4 py-3">Total Anak</th>
                    <th class="px-4 py-3">Tanggal Selesai Pembinaan</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-slate-100">
                    <td class="px-4 py-3 font-medium">Angkatan II</td>
                    <td class="px-4 py-3">50 Anak</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 border border-slate-300 rounded px-2 py-1.5 w-48 bg-white">
                            <input type="date" class="w-full outline-none text-sm text-slate-600 bg-transparent">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="text-right">
        <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded shadow-sm">
            Simpan & Terapkan Aturan
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mammoth@1.9.0/mammoth.browser.min.js"></script>
<?php include 'includes/footer.php'; ?>