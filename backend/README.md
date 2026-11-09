# Backend SeMARAK

Backend ini memakai PHP 8+ dan MySQL/MariaDB melalui PDO. API mengembalikan JSON dan tidak memerlukan framework atau paket Composer.

## Menjalankan di XAMPP

1. Jalankan Apache dan MySQL dari XAMPP.
2. Import `backend/schema.sql` melalui phpMyAdmin atau jalankan dengan klien MySQL. File ini membuat database `semarak_db` beserta data awal 5 anak dan 22 kegiatan.
   Untuk database lama, jalankan `ALTER TABLE children ADD COLUMN religion VARCHAR(80) NULL;` dan/atau `ALTER TABLE children ADD COLUMN tribe VARCHAR(80) NULL;` hanya untuk kolom yang belum tersedia.
3. Bila kredensial MySQL berbeda dari setelan XAMPP standar, atur environment variable `SEMARAK_DB_HOST`, `SEMARAK_DB_NAME`, `SEMARAK_DB_USER`, dan `SEMARAK_DB_PASSWORD` sebelum Apache dijalankan. Nilai bawaan: `127.0.0.1`, `semarak_db`, `root`, dan password kosong.
4. Coba buka `http://localhost/semarak/backend/api.php?resource=health` (sesuaikan prefix URL jika folder proyek ditempatkan berbeda).

## Endpoint

Semua permintaan memakai `backend/api.php?resource=...`. Kirim dan terima JSON dengan UTF-8.

| Method | Resource                                            | Keterangan                                                                          |
| ------ | --------------------------------------------------- | ----------------------------------------------------------------------------------- |
| GET    | `health`                                            | Status API dan koneksi database                                                     |
| GET    | `children`                                          | Daftar anak                                                                         |
| POST   | `children`                                          | Tambah anak: `name`, opsional `gender` (`L`/`P`), `religion`, `tribe`, dan `cohort` |
| PUT    | `children&id=1`                                     | Perbarui data anak, termasuk `religion`, `tribe`, dan `is_active`                   |
| GET    | `activities`                                        | Daftar kegiatan aktif, berurutan                                                    |
| GET    | `attendance&date=2026-09-28&activity_id=1`          | Nilai anak untuk kegiatan dan tanggal; anak izin berstatus `libur`                  |
| POST   | `attendance`                                        | Simpan evaluasi kegiatan                                                            |
| POST   | `absences`                                          | Ganti daftar anak izin untuk satu tanggal                                           |
| GET    | `schedules`                                         | Aturan jadwal semua kegiatan                                                        |
| PUT    | `schedules`                                         | Simpan aturan jadwal kegiatan                                                       |
| GET    | `summary&start=2026-09-01&end=2026-09-28`           | Hitungan dan persentase status evaluasi                                             |
| GET    | `report&child_id=1&start=2026-09-01&end=2026-09-28` | Rekap per kegiatan untuk satu anak                                                  |

Contoh body simpan evaluasi:

```json
{
  "date": "2026-09-28",
  "activity_id": 1,
  "evaluations": [
    { "child_id": 1, "status": "sesuai" },
    { "child_id": 2, "status": "arahan", "notes": "Perlu diingatkan" }
  ]
}
```

Status evaluasi yang diterima: `sesuai`, `arahan`, `telat`, dan `tidak`.

Contoh body izin harian (daftar ini menggantikan semua data izin pada tanggal tersebut; kirim array kosong untuk menghapusnya):

```json
{
  "date": "2026-09-28",
  "child_ids": [2, 5],
  "reason": "Sakit"
}
```

Contoh body jadwal:

```json
{
  "schedules": [
    { "activity_id": 1, "rule_type": "setiap_hari", "config": null },
    {
      "activity_id": 7,
      "rule_type": "hari_tertentu",
      "config": { "days": [1, 3, 5] }
    },
    {
      "activity_id": 9,
      "rule_type": "multi_rentang",
      "config": { "ranges": [{ "start": "2026-09-01", "end": "2026-09-30" }] }
    }
  ]
}
```

Untuk `hari_kerja`, `config` boleh `null`. Angka `days` mengikuti ISO-8601: Senin `1` sampai Minggu `7`. Backend hanya menyimpan aturan; penerapan aturan jadwal saat membuat evaluasi belum dipaksakan.
