CREATE DATABASE IF NOT EXISTS semarak_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE semarak_db;

CREATE TABLE IF NOT EXISTS children (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    gender ENUM('L', 'P') NULL,
    religion VARCHAR(80) NULL,
    tribe VARCHAR(80) NULL,
    cohort VARCHAR(80) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    UNIQUE KEY uq_activities_sort_order (sort_order)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS activity_schedules (
    activity_id INT UNSIGNED PRIMARY KEY,
    rule_type ENUM('setiap_hari', 'hari_kerja', 'multi_rentang', 'hari_tertentu') NOT NULL DEFAULT 'setiap_hari',
    config_json LONGTEXT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_schedules_activity FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS daily_absences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    absence_date DATE NOT NULL,
    child_id INT UNSIGNED NOT NULL,
    reason VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_daily_absence (absence_date, child_id),
    CONSTRAINT fk_absences_child FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS evaluations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    evaluation_date DATE NOT NULL,
    activity_id INT UNSIGNED NOT NULL,
    child_id INT UNSIGNED NOT NULL,
    status ENUM('sesuai', 'arahan', 'telat', 'tidak') NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_evaluation (evaluation_date, activity_id, child_id),
    KEY idx_evaluations_date_status (evaluation_date, status),
    CONSTRAINT fk_evaluations_activity FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    CONSTRAINT fk_evaluations_child FOREIGN KEY (child_id) REFERENCES children(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO children (id, name) VALUES
    (1, 'Endang Sri Ayu'),
    (2, 'Doddy'),
    (3, 'Rohmah Hidayah'),
    (4, 'Isti Komah insani'),
    (5, 'Cece')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO activities (id, name, sort_order) VALUES
    (1, 'Aku Bangun Pagi Sendiri Jam 04.30', 1),
    (2, 'Aku Shalat Shubuh Jam 04.30 di Masjid', 2),
    (3, 'Aku Mengikuti Kegiatan Olahraga Jam 05.30 s.d. 06.15', 3),
    (4, 'Aku Mengikuti Kegiatan Mandi Pagi Jam 06.15 s.d. 06.45', 4),
    (5, 'Aku Mengikuti Kegiatan Makan Pagi Jam 06.45 s.d. 07.15', 5),
    (6, 'Aku Membersihkan Tempat Tidur, Menyapu dan Mengepel Asrama Jam 07.15 s.d. 08.00', 6),
    (7, 'Aku Mengikuti Kegiatan Bimbingan Keterampilan Jam 08.00 s.d. 11.00', 7),
    (8, 'Aku Mengikuti Kegiatan Bimbingan Kedisiplinan Jam 08.00 s.d. 11.00', 8),
    (9, 'Aku Mengikuti Kegiatan Bimbingan Batik Ecoprint Jam 08.00 s.d. 12.00', 9),
    (10, 'Aku Mengikuti Kegiatan Bimbingan Sosial I Jam 11.00 s.d. 12.00', 10),
    (11, 'Aku Shalat Dzuhur Jam 12.00 di Masjid', 11),
    (12, 'Aku Mengikuti Kegiatan Makan Siang Jam 12.30 s.d. 13.00', 12),
    (13, 'Aku Mengikuti Kegiatan Bimbingan Sosial II Jam 13.00 s.d. 15.00', 13),
    (14, 'Aku Mengikuti Kegiatan Bimbingan Farming Jam 13.00 s.d. 15.00', 14),
    (15, 'Aku Shalat Ashar Jam 15.00 di Masjid', 15),
    (16, 'Aku Mandi Sore Jam 15.30 s.d. 16.00', 16),
    (17, 'Aku Mencuci Pakaian Kotor dan Menyetrika Pakaian Jam 16.00 s.d. 17.30', 17),
    (18, 'Aku Shalat Maghrib Jam 17.45 di Masjid', 18),
    (19, 'Aku Mengikuti Kegiatan Makan Malam Jam 18.00 s.d. 18.30', 19),
    (20, 'Aku Shalat Isya\' Jam 19.00 di Masjid', 20),
    (21, 'Aku Mengikuti Kegiatan Pengajian Jam 19.15 s.d. 20.00', 21),
    (22, 'Aku Tidur Tepat Waktu di Kamar Tempat Tidurku Jam 21.00', 22)
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT IGNORE INTO activity_schedules (activity_id, rule_type, config_json) VALUES
    (7, 'hari_kerja', NULL),
    (8, 'multi_rentang', '{"ranges":[]}'),
    (9, 'hari_tertentu', '{"days":[6]}'),
    (10, 'hari_tertentu', '{"days":[1,2,3,4]}'),
    (13, 'hari_tertentu', '{"days":[1,2,3]}'),
    (14, 'hari_tertentu', '{"days":[2,4,5]}');