<?php include 'includes/header.php'; ?>

<div class="semarak-card p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-pdf text-blue-500"></i> Cetak Dokumen Raport
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
            <button id="printReport" type="button"
                class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-1.5 rounded text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak
            </button>
        </div>
    </div>

    <div class="border border-slate-200 rounded-lg p-10 max-w-4xl mx-auto shadow-sm bg-white">
        <div class="text-center mb-8 border-b-2 border-black pb-4">
            <h3 class="font-bold text-lg">PEMERINTAH PROVINSI JAWA BARAT</h3>
            <h2 class="font-bold text-xl">DINAS SOSIAL</h2>
            <h4 class="font-bold">UPTD PUSAT PELAYANAN SOSIAL GRIYA BINA REMAJA</h4>
            <p class="text-xs">Jalan Maribaya No. 22 Lembang, Bandung Barat, Jawa Barat 40391</p>
            <p class="text-xs">Telepon. (022) 2786120</p>
            <p class="text-xs">Website: www.dinsos.ppsgbr.jabarprov.go.id Pos-el uptdppsgbr@gmail.com</p>
        </div>

        <div class="flex justify-between items-center bg-blue-50 border border-blue-200 p-3 rounded mb-6">
            <span class="font-bold text-sm tracking-wide">EVALUASI KARAKTER ANAK ASUH</span>
            <span class="bg-blue-600 text-white text-xs px-3 py-1 rounded font-bold">RAPORT PILAR KARAKTER</span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-8">
            <div>
                <p class="mb-1"><span class="font-medium inline-block w-40">ID & Nama Anak:</span> <span
                        id="reportChildName">-</span> <span>(<span id="reportChildId">-</span>)</span></p>
                <p class="mb-1"><span class="font-medium inline-block w-40">Jenis Kelamin:</span> <span
                        id="reportChildGender">-</span></p>
                <p class="mb-1"><span class="font-medium inline-block w-40">Agama:</span> <span
                        id="reportChildReligion">-</span></p>
                <p class="mb-1"><span class="font-medium inline-block w-40">Suku:</span> <span
                        id="reportChildTribe">-</span></p>
                <p class="mb-1"><span class="font-medium inline-block w-40">Asal Daerah:</span> -</p>
                <p class="mb-1"><span class="font-medium inline-block w-40">Angkatan / Gelombang:</span> <span
                        id="reportChildCohort">-</span></p>
            </div>
            <div>
                <p class="mb-1"><span class="font-medium inline-block w-32">Periode:</span> <span
                        id="reportPeriod">-</span></p>
                <p class="mb-1"><span class="font-medium inline-block w-32">Status Kepesertaan:</span> -</p>
                <p class="mb-1"><span class="font-medium inline-block w-32">Jurusan / Vokasi:</span> -</p>
                <p class="mb-1"><span class="font-medium inline-block w-32">Standar Evaluasi:</span> Berehan, Motekar,
                    Calakan, Berseka, Waringkas</p>
            </div>
        </div>

        <div class="border border-slate-200 rounded-lg overflow-hidden mb-6">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-100 border-b border-slate-200">
                    <tr>
                        <th class="px-3 py-2">Kegiatan</th>
                        <th class="px-2 py-2 text-center">Sesuai</th>
                        <th class="px-2 py-2 text-center">Arahan</th>
                        <th class="px-2 py-2 text-center">Telat</th>
                        <th class="px-2 py-2 text-center">Tidak</th>
                        <th class="px-2 py-2 text-center">Izin</th>
                        <th class="px-2 py-2 text-center">Skor</th>
                    </tr>
                </thead>
                <tbody id="reportRows">
                    <tr>
                        <td colspan="7" class="px-3 py-4 text-center text-slate-500">Pilih anak untuk memuat raport.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p id="reportMessage" class="mb-4 text-sm" role="status" aria-live="polite"></p>

        <div class="border border-slate-200 rounded-lg overflow-hidden">
            <div class="bg-slate-50 px-4 py-2 border-b border-slate-200">
                <h4 class="text-sm font-bold text-slate-700"><i class="fa-solid fa-comments mr-2"></i> CATATAN &
                    REKOMENDASI</h4>
            </div>
            <div class="p-4 grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium mb-2">Catatan Perilaku:</p>
                    <div class="border border-slate-200 rounded h-32 bg-slate-50"></div>
                </div>
                <div>
                    <p class="text-sm font-medium mb-2">Rekomendasi:</p>
                    <div class="border border-slate-200 rounded h-32 bg-slate-50"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>