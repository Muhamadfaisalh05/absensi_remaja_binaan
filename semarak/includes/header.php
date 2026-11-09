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

<body class="bg-slate-50/80 text-slate-800 antialiased min-h-screen pb-24 md:pb-12 flex flex-col justify-between">
    <div>
        <header class="bg-gradient-to-r from-blue-700 via-blue-600 to-teal-600 text-white shadow-lg shadow-blue-900/10 sticky top-0 z-50 backdrop-blur-md">
            <div class="container mx-auto px-4 lg:px-8 py-3.5 flex justify-between items-center">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20 shadow-inner">
                        <i class="fa-solid fa-clipboard-user text-xl text-amber-300"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-extrabold tracking-tight leading-none text-white">
                                SeMARAK
                            </h1>
                            <span class="text-[11px] font-semibold bg-emerald-400/25 border border-emerald-300/40 text-emerald-100 px-2.5 py-0.5 rounded-full">
                                UPTD PPSGBR
                            </span>
                        </div>
                        <p class="text-xs text-blue-100/90 font-medium mt-0.5">Sistem Monitoring & Raport Karakter Binaan</p>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-2.5">
                    <div class="bg-white/10 hover:bg-white/15 px-3.5 py-1.5 rounded-lg text-xs font-medium flex items-center gap-2 border border-white/20 shadow-sm backdrop-blur-sm">
                        <i class="fa-regular fa-calendar-check text-teal-200"></i>
                        <span id="currentDate">Memuat tanggal...</span>
                    </div>
                    <button id="inputWeekly" type="button"
                        class="bg-amber-500 hover:bg-amber-600 active:scale-95 text-slate-900 font-semibold px-3.5 py-1.5 rounded-lg text-xs flex items-center gap-2 shadow-sm shadow-amber-900/20">
                        <i class="fa-solid fa-calendar-week"></i> Input Mingguan
                    </button>
                    <button id="generateWeekly" type="button"
                        class="bg-white/20 hover:bg-white/30 active:scale-95 text-white font-medium px-3.5 py-1.5 rounded-lg text-xs flex items-center gap-2 border border-white/30 shadow-sm">
                        <i class="fa-solid fa-wand-magic-sparkles text-amber-300"></i> Generate
                    </button>
                </div>
            </div>
        </header>

        <main class="container mx-auto px-4 lg:px-8 py-6 max-w-7xl">
            <!-- Desktop Navigation Bar -->
            <nav class="hidden md:flex bg-white/80 backdrop-blur-md rounded-2xl shadow-sm border border-slate-200/80 mb-6 p-1.5 gap-1.5">
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <a href="index.php"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all <?= $current_page == 'index.php' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/50' ?>">
                    <i class="fa-solid fa-chart-pie text-sm"></i> Dashboard
                </a>
                <a href="absensi.php"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all <?= $current_page == 'absensi.php' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/50' ?>">
                    <i class="fa-solid fa-list-check text-sm"></i> Absensi & Evaluasi
                </a>
                <a href="summary.php"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all <?= $current_page == 'summary.php' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/50' ?>">
                    <i class="fa-solid fa-users text-sm"></i> Summary Individu
                </a>
                <a href="report.php"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all <?= $current_page == 'report.php' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/50' ?>">
                    <i class="fa-solid fa-file-invoice text-sm"></i> Raport Dokumen
                </a>
                <a href="pengaturan.php"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all <?= $current_page == 'pengaturan.php' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/50' ?>">
                    <i class="fa-solid fa-gear text-sm"></i> Pengaturan
                </a>
            </nav>