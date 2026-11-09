# PANDUAN LENGKAP MIGRASI SISTEM SeMARAK KE LARAVEL

Dokumen ini berisi analisis menyeluruh atas seluruh kode sumber (*codebase*) sistem **SeMARAK** (Sistem Monitoring & Raport Karakter Anak Asuh) beserta panduan langkah demi langkah untuk mengintegrasikannya ke dalam framework **Laravel** (disarankan Laravel 10 / 11) **tanpa mengubah skema database, tanpa kehilangan data yang ada, dan tanpa mengurangi fungsi logika bisnis apa pun**.

---

## 1. ANALISIS KODE & ARSITEKTUR SISTEM EKSISTING

### A. Struktur Folder Saat Ini
```text
semarak/
├── backend/
│   ├── config.php      # Koneksi database via PDO murni
│   ├── api.php         # Monolithic REST-like API handler berbasis query string (?resource=...)
│   ├── schema.sql      # DDL database MySQL & data seeding awal
│   └── README.md       # Dokumentasi endpoint backend
├── semarak/            # Front End UI
│   ├── assets/
│   │   ├── css/style.css   # Styling kustom & print layout
│   │   └── js/script.js    # Client-side logic & API consumer (Fetch API)
│   ├── includes/
│   │   ├── header.php      # Header & navbar navigasi desktop
│   │   ├── footer.php      # Mobile dock bar & script tag
│   │   └── data_kegiatan.php
│   ├── index.php       # Dashboard ringkasan & distribusi status
│   ├── absensi.php     # Input evaluasi harian & izin sakit
│   ├── summary.php     # Analisis capaian per individu anak
│   ├── report.php      # Lembar dokumen raport resmi siap cetak
│   └── pengaturan.php  # Pengaturan jadwal kegiatan & manajemen siswa
```

### B. Analisis Database (MySQL / MariaDB: `semarak_db`)
Database terdiri dari 5 tabel berelasi kuat dengan foreign key `ON DELETE CASCADE`:

1. **`children`**: Master data anak binaan.
   - Kolom: `id`, `name`, `gender` (L/P), `religion`, `tribe`, `cohort`, `is_active`, `created_at`, `updated_at`.
2. **`activities`**: Master daftar kegiatan/indikator penilaian karakter (default: 22 kegiatan).
   - Kolom: `id`, `name`, `sort_order` (UNIQUE), `is_active`.
3. **`activity_schedules`**: Konfigurasi jadwal dinamis per kegiatan.
   - Kolom: `activity_id` (PK, FK), `rule_type` (`setiap_hari`, `hari_kerja`, `multi_rentang`, `hari_tertentu`), `config_json`, `updated_at`.
4. **`daily_absences`**: Data izin / sakit harian anak asuh (otomatis meliburkan anak dari evaluasi).
   - Kolom: `id`, `absence_date`, `child_id` (FK), `reason`, `created_at`.
   - Index unik: `(absence_date, child_id)`.
5. **`evaluations`**: Riwayat evaluasi per anak, per kegiatan, per tanggal.
   - Kolom: `id`, `evaluation_date`, `activity_id` (FK), `child_id` (FK), `status` (`sesuai`, `arahan`, `telat`, `tidak`), `notes`, `created_at`, `updated_at`.
   - Index unik: `(evaluation_date, activity_id, child_id)`.

### C. Analisis Logika Bisnis Kritis (Harus Dipertahankan di Laravel)
1. **Pengecualian Evaluasi Shalat untuk Non-Muslim**:
   - Jika nama kegiatan mengandung kata `shalat` atau `sholat`, dan agama anak terdata bukan `islam` (dan tidak kosong), anak tersebut **tidak boleh dievaluasi** dan disembunyikan/dilewati saat simpan nilai.
2. **Penanganan Otomatis Anak Izin / Sakit (`daily_absences`)**:
   - Jika anak terdaftar izin pada tanggal tertentu:
     - Saat GET attendance: status otomatis dihitung `libur` dan flag `is_absent = true`.
     - Saat POST attendance: evaluasi untuk anak yang sedang izin **otomatis di-skip** (`skipped_absent++`) untuk menjaga integritas data harian.
3. **Aturan Jadwal Dinamis (`activity_schedules`)**:
   - `setiap_hari`: Aktif Senin - Minggu.
   - `hari_kerja`: Aktif Senin - Jumat (ISO weekday 1 - 5).
   - `hari_tertentu`: Aktif pada array `days` tertentu (1 = Senin, 7 = Minggu).
   - `multi_rentang`: Aktif jika tanggal berada di antara salah satu range `{start, end}`.
4. **Perhitungan Statistik Summary**:
   - Mengelompokkan status `sesuai`, `arahan`, `telat`, `tidak`.
   - Menghitung persentase dengan rumus: `round((count / total) * 100, 1)`.
5. **Perhitungan Rekap Raport**:
   - Mengakumulasi jumlah per kegiatan untuk 1 anak dalam rentang `start` sampai `end`.
   - Mengambil jumlah hari izin (`daily_absences`) dalam rentang yang sama untuk kolom `libur`.

---

## 2. STRATEGI INTEGRASI KE LARAVEL

Ada dua opsi integrasi yang bisa dipilih:

* **Opsi A (Backend API Only - Paling Cepat & Minim Risiko)**:
  Laravel hanya menggantikan folder `backend/api.php`. Front-end HTML/JS yang ada saat ini tetap berjalan apa adanya, hanya URL target API di `script.js` diarahkan ke endpoint Laravel.
* **Opsi B (Fullstack Laravel Blade - Standar Arsitektur Enterprise)**:
  Memindahkan seluruh file PHP front-end ke **Blade Templates** (`resources/views`), memanfaatkan asset bundling (Vite / Mix), otentikasi resmi Laravel (Breeze/Jetstream), dan Controller MVC terstruktur.

Berikut panduan implementasi untuk **Opsi B (Lengkap & Komprehensif)** yang juga mencakup penyediaan API sehingga kedua opsi dapat berjalan sekaligus.

---

## 3. LANGKAH-LANGKAH IMPLEMENTASI MIGRASI

### Langkah 1: Setup Proyek Laravel & Koneksi Database
Jalankan di terminal:
```bash
composer create-project laravel/laravel semarak-laravel
cd semarak-laravel
```

Buka file `.env` di proyek Laravel dan hubungkan ke database yang sudah ada tanpa merusak data:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=semarak_db
DB_USERNAME=root
DB_PASSWORD=
```

> **PENTING**: Karena tabel dan data sudah ada di MySQL, jangan jalankan `php artisan migrate:fresh` karena itu akan menghapus seluruh isi database!

---

### Langkah 2: Buat Eloquent Models Sesuai Skema Asli

#### 1. Model `Child` (`app/Models/Child.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    protected $table = 'children';

    protected $fillable = [
        'name',
        'gender',
        'religion',
        'tribe',
        'cohort',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'child_id');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(DailyAbsence::class, 'child_id');
    }

    public function isMuslim(): bool
    {
        $religion = mb_strtolower(trim((string) $this->religion));
        return $religion === '' || $religion === 'islam';
    }
}
```

#### 2. Model `Activity` (`app/Models/Activity.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    protected $table = 'activities';
    public $timestamps = false; // Tabel activities tidak memiliki created_at/updated_at

    protected $fillable = [
        'name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function schedule(): HasOne
    {
        return $this->hasOne(ActivitySchedule::class, 'activity_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'activity_id');
    }

    public function isPrayer(): bool
    {
        return preg_match('/\bsh(?:a|o)lat\b/i', $this->name) === 1;
    }
}
```

#### 3. Model `ActivitySchedule` (`app/Models/ActivitySchedule.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivitySchedule extends Model
{
    protected $table = 'activity_schedules';
    protected $primaryKey = 'activity_id';
    public $incrementing = false;
    const CREATED_AT = null; // hanya memiliki updated_at

    protected $fillable = [
        'activity_id',
        'rule_type',
        'config_json',
    ];

    protected $casts = [
        'config_json' => 'array',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function isScheduledFor(string $date): bool
    {
        $rule = $this->rule_type ?? 'setiap_hari';
        $isoDay = (int) date('N', strtotime($date)); // 1 (Senin) - 7 (Minggu)

        if ($rule === 'hari_kerja') {
            return $isoDay <= 5;
        }

        if ($rule === 'hari_tertentu') {
            $days = $this->config_json['days'] ?? [];
            return in_array($isoDay, array_map('intval', $days), true);
        }

        if ($rule === 'multi_rentang') {
            $ranges = $this->config_json['ranges'] ?? [];
            foreach ($ranges as $range) {
                if (!empty($range['start']) && !empty($range['end'])) {
                    if ($range['start'] <= $date && $date <= $range['end']) {
                        return true;
                    }
                }
            }
            return false;
        }

        return true;
    }
}
```

#### 4. Model `DailyAbsence` (`app/Models/DailyAbsence.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyAbsence extends Model
{
    protected $table = 'daily_absences';
    const UPDATED_AT = null; // hanya created_at

    protected $fillable = [
        'absence_date',
        'child_id',
        'reason',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }
}
```

#### 5. Model `Evaluation` (`app/Models/Evaluation.php`)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    protected $table = 'evaluations';

    protected $fillable = [
        'evaluation_date',
        'activity_id',
        'child_id',
        'status',
        'notes',
    ];

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class, 'child_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
```

---

### Langkah 3: Buat Controller & Service API di Laravel

Buat controller via Artisan:
```bash
php artisan make:controller Api/SemarakApiController
```

Isi Controller `app/Http/Controllers/Api/SemarakApiController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivitySchedule;
use App\Models\Child;
use App\Models\DailyAbsence;
use App\Models\Evaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SemarakApiController extends Controller
{
    public function health(): JsonResponse
    {
        DB::select('SELECT 1');
        return response()->json(['status' => 'ok', 'database' => 'connected']);
    }

    public function getChildren(): JsonResponse
    {
        $children = Child::orderBy('name')->get([
            'id', 'name', 'gender', 'religion', 'tribe', 'cohort', 'is_active'
        ]);
        return response()->json(['data' => $children]);
    }

    public function storeChild(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'religion' => 'nullable|string|max:80',
            'tribe' => 'nullable|string|max:80',
            'cohort' => 'nullable|string|max:80',
        ]);

        $child = Child::create($validated);
        return response()->json(['data' => ['id' => $child->id, 'name' => $child->name]], 201);
    }

    public function updateChild(Request $request, int $id): JsonResponse
    {
        $child = Child::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'religion' => 'nullable|string|max:80',
            'tribe' => 'nullable|string|max:80',
            'cohort' => 'nullable|string|max:80',
            'is_active' => 'required|boolean',
        ]);

        $child->update($validated);
        return response()->json(['message' => 'Data anak diperbarui.']);
    }

    public function getActivities(): JsonResponse
    {
        $activities = Activity::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'name', 'sort_order']);

        return response()->json(['data' => $activities]);
    }

    public function getAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'activity_id' => 'required|integer|exists:activities,id',
        ]);

        $date = $request->input('date');
        $activityId = (int) $request->input('activity_id');

        $rows = DB::select("
            SELECT c.id AS child_id, c.name, c.religion, e.status, e.notes,
                   CASE WHEN da.id IS NULL THEN 0 ELSE 1 END AS is_absent
            FROM children c
            LEFT JOIN evaluations e ON e.child_id = c.id AND e.activity_id = :activity_id AND e.evaluation_date = :eval_date
            LEFT JOIN daily_absences da ON da.child_id = c.id AND da.absence_date = :abs_date
            WHERE c.is_active = 1
            ORDER BY c.name
        ", [
            'activity_id' => $activityId,
            'eval_date' => $date,
            'abs_date' => $date,
        ]);

        $result = array_map(function ($row) {
            $isAbsent = (bool) $row->is_absent;
            return [
                'child_id' => (int) $row->child_id,
                'name' => $row->name,
                'religion' => $row->religion,
                'status' => $isAbsent ? 'libur' : $row->status,
                'notes' => $row->notes,
                'is_absent' => $isAbsent,
            ];
        }, $rows);

        return response()->json([
            'date' => $date,
            'activity_id' => $activityId,
            'data' => $result,
        ]);
    }

    public function saveAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'activity_id' => 'required|integer|exists:activities,id',
            'evaluations' => 'required|array',
            'evaluations.*.child_id' => 'required|integer|exists:children,id',
            'evaluations.*.status' => ['required', Rule::in(['sesuai', 'arahan', 'telat', 'tidak'])],
            'evaluations.*.notes' => 'nullable|string',
        ]);

        $date = $request->input('date');
        $activityId = (int) $request->input('activity_id');
        $evaluations = $request->input('evaluations');

        $activity = Activity::findOrFail($activityId);
        $isPrayer = $activity->isPrayer();

        $saved = 0;
        $skippedAbsent = 0;

        DB::transaction(function () use ($date, $activityId, $evaluations, $isPrayer, &$saved, &$skippedAbsent) {
            $absentChildIds = DailyAbsence::where('absence_date', $date)
                ->pluck('child_id')
                ->flip();

            foreach ($evaluations as $item) {
                $child = Child::find($item['child_id']);
                if (!$child || !$child->is_active) {
                    continue;
                }

                // Proteksi shalat untuk non-muslim
                if ($isPrayer && !$child->isMuslim()) {
                    continue;
                }

                // Lewati jika anak izin
                if (isset($absentChildIds[$child->id])) {
                    $skippedAbsent++;
                    continue;
                }

                Evaluation::updateOrCreate(
                    [
                        'evaluation_date' => $date,
                        'activity_id' => $activityId,
                        'child_id' => $child->id,
                    ],
                    [
                        'status' => $item['status'],
                        'notes' => $item['notes'] ?? null,
                    ]
                );
                $saved++;
            }
        });

        return response()->json([
            'message' => 'Penilaian tersimpan.',
            'saved' => $saved,
            'skipped_absent' => $skippedAbsent,
        ]);
    }

    public function saveAbsences(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'child_ids' => 'present|array',
            'child_ids.*' => 'integer|exists:children,id',
            'reason' => 'nullable|string|max:255',
        ]);

        $date = $request->input('date');
        $childIds = array_unique($request->input('child_ids'));
        $reason = $request->input('reason');

        DB::transaction(function () use ($date, $childIds, $reason) {
            DailyAbsence::where('absence_date', $date)->delete();

            foreach ($childIds as $childId) {
                DailyAbsence::create([
                    'absence_date' => $date,
                    'child_id' => $childId,
                    'reason' => $reason,
                ]);
            }
        });

        return response()->json([
            'message' => 'Daftar anak izin diperbarui.',
            'count' => count($childIds),
        ]);
    }

    public function getSchedules(): JsonResponse
    {
        $rows = DB::select("
            SELECT a.id AS activity_id, a.name, s.rule_type, s.config_json
            FROM activities a
            LEFT JOIN activity_schedules s ON s.activity_id = a.id
            WHERE a.is_active = 1
            ORDER BY a.sort_order, a.id
        ");

        $data = array_map(function ($row) {
            return [
                'activity_id' => (int) $row->activity_id,
                'name' => $row->name,
                'rule_type' => $row->rule_type ?? 'setiap_hari',
                'config' => $row->config_json ? json_decode($row->config_json, true) : null,
            ];
        }, $rows);

        return response()->json(['data' => $data]);
    }

    public function saveSchedules(Request $request): JsonResponse
    {
        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.activity_id' => 'required|integer|exists:activities,id',
            'schedules.*.rule_type' => ['required', Rule::in(['setiap_hari', 'hari_kerja', 'multi_rentang', 'hari_tertentu'])],
            'schedules.*.config' => 'nullable|array',
        ]);

        $schedules = $request->input('schedules');

        DB::transaction(function () use ($schedules) {
            foreach ($schedules as $item) {
                ActivitySchedule::updateOrCreate(
                    ['activity_id' => $item['activity_id']],
                    [
                        'rule_type' => $item['rule_type'],
                        'config_json' => $item['config'] ?? null,
                    ]
                );
            }
        });

        return response()->json([
            'message' => 'Jadwal kegiatan tersimpan.',
            'saved' => count($schedules),
        ]);
    }

    public function getSummary(Request $request): JsonResponse
    {
        $request->validate([
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d|after_or_equal:start',
            'child_id' => 'nullable|integer|exists:children,id',
        ]);

        $start = $request->input('start');
        $end = $request->input('end');
        $childId = $request->input('child_id');

        $query = Evaluation::whereBetween('evaluation_date', [$start, $end]);
        if ($childId) {
            $query->where('child_id', $childId);
        }

        $rawCounts = $query->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $counts = [
            'sesuai' => (int) ($rawCounts['sesuai'] ?? 0),
            'arahan' => (int) ($rawCounts['arahan'] ?? 0),
            'telat' => (int) ($rawCounts['telat'] ?? 0),
            'tidak' => (int) ($rawCounts['tidak'] ?? 0),
        ];

        $total = array_sum($counts);
        $percentages = [];
        foreach ($counts as $status => $count) {
            $percentages[$status] = $total === 0 ? 0.0 : round(($count / $total) * 100, 1);
        }

        return response()->json([
            'start' => $start,
            'end' => $end,
            'total_evaluations' => $total,
            'counts' => $counts,
            'percentages' => $percentages,
        ]);
    }

    public function getReport(Request $request): JsonResponse
    {
        $request->validate([
            'child_id' => 'required|integer|exists:children,id',
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d|after_or_equal:start',
        ]);

        $childId = (int) $request->input('child_id');
        $start = $request->input('start');
        $end = $request->input('end');

        $child = Child::findOrFail($childId, ['id', 'name', 'gender', 'religion', 'tribe', 'cohort']);

        $absenceCount = DailyAbsence::where('child_id', $childId)
            ->whereBetween('absence_date', [$start, $end])
            ->count();

        $rows = DB::select("
            SELECT a.id AS activity_id, a.name AS activity,
                   CAST(SUM(e.status = 'sesuai') AS UNSIGNED) AS sesuai,
                   CAST(SUM(e.status = 'arahan') AS UNSIGNED) AS arahan,
                   CAST(SUM(e.status = 'telat') AS UNSIGNED) AS telat,
                   CAST(SUM(e.status = 'tidak') AS UNSIGNED) AS tidak,
                   CAST(COUNT(e.id) AS UNSIGNED) AS total
            FROM activities a
            LEFT JOIN evaluations e ON e.activity_id = a.id AND e.child_id = :child_id
                AND e.evaluation_date BETWEEN :start AND :end
            WHERE a.is_active = 1
            GROUP BY a.id, a.name, a.sort_order
            ORDER BY a.sort_order, a.id
        ", [
            'child_id' => $childId,
            'start' => $start,
            'end' => $end,
        ]);

        $data = array_map(function ($row) use ($absenceCount) {
            return [
                'activity_id' => (int) $row->activity_id,
                'activity' => $row->activity,
                'sesuai' => (int) $row->sesuai,
                'arahan' => (int) $row->arahan,
                'telat' => (int) $row->telat,
                'tidak' => (int) $row->tidak,
                'total' => (int) $row->total,
                'libur' => $absenceCount,
            ];
        }, $rows);

        return response()->json([
            'child' => $child,
            'start' => $start,
            'end' => $end,
            'data' => $data,
        ]);
    }
}
```

---

### Langkah 4: Daftarkan Routing di `routes/api.php`

Daftarkan rute RESTful modern sekaligus rute kompatibilitas query string (`?resource=...`) agar front-end saat ini tetap bisa berjalan tanpa perubahan baris kode:

```php
use App\Http\Controllers\Api\SemarakApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 1. Rute Standar RESTful Laravel
Route::prefix('v1')->group(function () {
    Route::get('/health', [SemarakApiController::class, 'health']);
    Route::get('/children', [SemarakApiController::class, 'getChildren']);
    Route::post('/children', [SemarakApiController::class, 'storeChild']);
    Route::put('/children/{id}', [SemarakApiController::class, 'updateChild']);
    Route::get('/activities', [SemarakApiController::class, 'getActivities']);
    Route::get('/attendance', [SemarakApiController::class, 'getAttendance']);
    Route::post('/attendance', [SemarakApiController::class, 'saveAttendance']);
    Route::post('/absences', [SemarakApiController::class, 'saveAbsences']);
    Route::get('/schedules', [SemarakApiController::class, 'getSchedules']);
    Route::put('/schedules', [SemarakApiController::class, 'saveSchedules']);
    Route::get('/summary', [SemarakApiController::class, 'getSummary']);
    Route::get('/report', [SemarakApiController::class, 'getReport']);
});

// 2. Rute Kompatibilitas untuk script.js eksisting (?resource=...)
Route::any('/api.php', function (Request $request, SemarakApiController $controller) {
    $resource = $request->query('resource');
    $method = $request->method();

    return match (true) {
        $resource === 'health' && $method === 'GET' => $controller->health(),
        $resource === 'children' && $method === 'GET' => $controller->getChildren(),
        $resource === 'children' && $method === 'POST' => $controller->storeChild($request),
        $resource === 'children' && $method === 'PUT' => $controller->updateChild($request, (int) $request->query('id')),
        $resource === 'activities' && $method === 'GET' => $controller->getActivities(),
        $resource === 'attendance' && $method === 'GET' => $controller->getAttendance($request),
        $resource === 'attendance' && $method === 'POST' => $controller->saveAttendance($request),
        $resource === 'absences' && $method === 'POST' => $controller->saveAbsences($request),
        $resource === 'schedules' && $method === 'GET' => $controller->getSchedules(),
        $resource === 'schedules' && $method === 'PUT' => $controller->saveSchedules($request),
        $resource === 'summary' && $method === 'GET' => $controller->getSummary($request),
        $resource === 'report' && $method === 'GET' => $controller->getReport($request),
        default => response()->json(['error' => 'Resource tidak ditemukan'], 404),
    };
});
```

---

### Langkah 5: Migrasi Front-End ke Laravel Blade

Pindahkan aset dan tampilan:
1. Pindahkan folder `assets/css` dan `assets/js` ke `public/assets/`.
2. Buat master layout di `resources/views/layouts/app.blade.php`:
   - Gabungkan isi `includes/header.php` dan `includes/footer.php`.
   - Gunakan directive `@yield('content')` untuk membungkus konten halaman.
3. Buat file view:
   - `resources/views/dashboard.blade.php` (dari `index.php`)
   - `resources/views/absensi.blade.php` (dari `absensi.php`)
   - `resources/views/summary.blade.php` (dari `summary.php`)
   - `resources/views/report.blade.php` (dari `report.php`)
   - `resources/views/pengaturan.blade.php` (dari `pengaturan.php`)
4. Tambahkan rute Web di `routes/web.php`:
```php
Route::get('/', fn() => view('dashboard'))->name('dashboard');
Route::get('/absensi', fn() => view('absensi'))->name('absensi');
Route::get('/summary', fn() => view('summary'))->name('summary');
Route::get('/report', fn() => view('report'))->name('report');
Route::get('/pengaturan', fn() => view('pengaturan'))->name('pengaturan');
```

---

## 4. KEUNTUNGAN SETELAH INTEGRASI KE LARAVEL

1. **Keamanan Ekstra (Enterprise Ready)**:
   - Dilindungi dari serangan CSRF secara otomatis pada form web.
   - Form request validation yang ketat dan pesan error bilingual.
   - Sanitasi SQL injection tingkat tinggi melalui Eloquent PDO parameter binding.
2. **Kemudahan Fitur Lanjutan**:
   - Menambahkan login / hak akses (Admin, Pekerja Sosial, Pembina Asrama) dalam beberapa menit menggunakan **Laravel Breeze**.
   - Export Raport ke PDF secara server-side menggunakan package `barryvdh/laravel-dompdf`.
   - Export/Import Excel dengan format rapi melalui package `maatwebsite/excel`.
3. **Database & Data 100% Utuh**:
   - Seluruh histori penilaian, izin anak, konfigurasi jadwal dinamis, dan daftar anak binaan tetap tersimpan di tabel eksisting tanpa perlu konversi ulang.
