<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SeMARAK - Sistem Monitoring & Raport Karakter Anak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-slate-50 text-slate-800 font-sans pb-20 md:pb-0">
    <header class="bg-gradient-to-r from-blue-600 to-teal-500 text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <i class="fa-solid fa-clipboard-user text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold leading-tight flex items-center gap-2">
                        SeMARAK <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full font-normal">UPTD
                            PPSGBR</span>
                    </h1>
                    <p class="text-xs text-blue-100">Sistem Monitoring & Raport Karakter Anak</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-3">
                <div class="bg-white/10 px-3 py-1.5 rounded text-sm flex items-center gap-2 border border-white/20">
                    <i class="fa-regular fa-calendar"></i>
                    <span id="currentDate">24/09/2026</span>
                </div>
                <button id="inputWeekly" type="button"
                    class="bg-orange-400 hover:bg-orange-500 transition px-4 py-1.5 rounded text-sm font-medium flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-calendar-week"></i> Input Mingguan
                </button>
                <button id="generateWeekly" type="button"
                    class="bg-blue-500 hover:bg-blue-600 transition px-4 py-1.5 rounded text-sm font-medium flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
                </button>
                <button
                    class="bg-teal-500 hover:bg-teal-600 transition px-4 py-1.5 rounded text-sm font-medium flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Simpan
                </button>
            </div>
        </div>
    </header>
    <main class="container mx-auto px-4 py-6 max-w-7xl">
        <div class="hidden md:flex bg-white rounded-lg shadow-sm border border-slate-200 mb-6 p-2 gap-2">
            <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
            <a href="index.php"
                class="px-4 py-2 rounded-md text-sm font-medium <?= $current_page == 'index.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' ?>"><i
                    class="fa-solid fa-chart-pie mr-2"></i>Dashboard</a>
            <a href="absensi.php"
                class="px-4 py-2 rounded-md text-sm font-medium <?= $current_page == 'absensi.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' ?>"><i
                    class="fa-solid fa-list-check mr-2"></i>Absensi</a>
            <a href="summary.php"
                class="px-4 py-2 rounded-md text-sm font-medium <?= $current_page == 'summary.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' ?>"><i
                    class="fa-solid fa-users mr-2"></i>Summary</a>
            <a href="report.php"
                class="px-4 py-2 rounded-md text-sm font-medium <?= $current_page == 'report.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' ?>"><i
                    class="fa-solid fa-file-pdf mr-2"></i>Report</a>
            <a href="pengaturan.php"
                class="px-4 py-2 rounded-md text-sm font-medium <?= $current_page == 'pengaturan.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' ?>"><i
                    class="fa-solid fa-gear mr-2"></i>Pengaturan</a>
        </div>