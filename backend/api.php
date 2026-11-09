<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function respond(mixed $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requestBody(): array
{
    $body = json_decode(file_get_contents('php://input'), true);
    if (!is_array($body)) {
        respond(['error' => 'Body harus berupa JSON object yang valid.'], 400);
    }

    return $body;
}

function requiredDate(mixed $value, string $field): string
{
    if (!is_string($value)) {
        respond(['error' => "Field {$field} wajib memakai format YYYY-MM-DD."], 422);
    }

    $date = DateTime::createFromFormat('!Y-m-d', $value);
    if (!$date || $date->format('Y-m-d') !== $value) {
        respond(['error' => "Field {$field} wajib memakai tanggal valid berformat YYYY-MM-DD."], 422);
    }

    return $value;
}

function dateRange(): array
{
    $today = date('Y-m-d');
    $start = requiredDate($_GET['start'] ?? $today, 'start');
    $end = requiredDate($_GET['end'] ?? $today, 'end');
    if ($start > $end) {
        respond(['error' => 'Tanggal start tidak boleh melewati tanggal end.'], 422);
    }

    return [$start, $end];
}

function positiveId(mixed $value, string $field): int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        respond(['error' => "Field {$field} wajib berupa ID positif."], 422);
    }

    return $id;
}

$pdo = require __DIR__ . '/config.php';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$resource = $_GET['resource'] ?? '';

try {
    if ($resource === 'health' && $method === 'GET') {
        $pdo->query('SELECT 1');
        respond(['status' => 'ok', 'database' => 'connected']);
    }

    if ($resource === 'children') {
        if ($method === 'GET') {
            $statement = $pdo->query(
                'SELECT id, name, gender, religion, tribe, cohort, is_active FROM children ORDER BY name'
            );
            respond(['data' => $statement->fetchAll()]);
        }

        if ($method === 'POST' || $method === 'PUT') {
            $body = requestBody();
            $name = trim((string) ($body['name'] ?? ''));
            $gender = $body['gender'] ?? null;
            $religion = isset($body['religion']) ? trim((string) $body['religion']) : null;
            $religion = $religion === '' ? null : $religion;
            $tribe = isset($body['tribe']) ? trim((string) $body['tribe']) : null;
            $tribe = $tribe === '' ? null : $tribe;
            $cohort = isset($body['cohort']) ? trim((string) $body['cohort']) : null;
            if ($name === '' || mb_strlen($name) > 120) {
                respond(['error' => 'Nama anak wajib diisi dan maksimal 120 karakter.'], 422);
            }
            if ($gender !== null && !in_array($gender, ['L', 'P'], true)) {
                respond(['error' => 'gender hanya boleh bernilai L, P, atau null.'], 422);
            }
            if ($religion !== null && mb_strlen($religion) > 80) {
                respond(['error' => 'Agama maksimal 80 karakter.'], 422);
            }
            if ($tribe !== null && mb_strlen($tribe) > 80) {
                respond(['error' => 'Suku maksimal 80 karakter.'], 422);
            }

            if ($method === 'POST') {
                $statement = $pdo->prepare(
                    'INSERT INTO children (name, gender, religion, tribe, cohort) VALUES (:name, :gender, :religion, :tribe, :cohort)'
                );
                $statement->execute([
                    'name' => $name,
                    'gender' => $gender,
                    'religion' => $religion,
                    'tribe' => $tribe,
                    'cohort' => $cohort,
                ]);
                respond(['data' => ['id' => (int) $pdo->lastInsertId(), 'name' => $name]], 201);
            }

            $id = positiveId($_GET['id'] ?? null, 'id');
            $active = filter_var($body['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($active === null) {
                respond(['error' => 'is_active harus berupa boolean.'], 422);
            }
            $statement = $pdo->prepare(
                'UPDATE children SET name = :name, gender = :gender, religion = :religion, tribe = :tribe, cohort = :cohort, is_active = :is_active WHERE id = :id'
            );
            $statement->execute([
                'name' => $name,
                'gender' => $gender,
                'religion' => $religion,
                'tribe' => $tribe,
                'cohort' => $cohort,
                'is_active' => (int) $active,
                'id' => $id,
            ]);
            if ($statement->rowCount() === 0 && !childExists($pdo, $id)) {
                respond(['error' => 'Anak tidak ditemukan.'], 404);
            }
            respond(['message' => 'Data anak diperbarui.']);
        }
    }

    if ($resource === 'activities' && $method === 'GET') {
        $statement = $pdo->query(
            'SELECT id, name, sort_order FROM activities WHERE is_active = 1 ORDER BY sort_order, id'
        );
        respond(['data' => $statement->fetchAll()]);
    }

    if ($resource === 'attendance') {
        if ($method === 'GET') {
            $date = requiredDate($_GET['date'] ?? null, 'date');
            $activityId = positiveId($_GET['activity_id'] ?? null, 'activity_id');
            $statement = $pdo->prepare(
                'SELECT c.id AS child_id, c.name, c.religion, e.status, e.notes,
                        CASE WHEN da.id IS NULL THEN 0 ELSE 1 END AS is_absent
                 FROM children c
                 LEFT JOIN evaluations e ON e.child_id = c.id AND e.activity_id = :activity_id AND e.evaluation_date = :evaluation_date
                 LEFT JOIN daily_absences da ON da.child_id = c.id AND da.absence_date = :absence_date
                 WHERE c.is_active = 1
                 ORDER BY c.name'
            );
            $statement->execute([
                'activity_id' => $activityId,
                'evaluation_date' => $date,
                'absence_date' => $date,
            ]);
            $rows = $statement->fetchAll();
            foreach ($rows as &$row) {
                $row['status'] = (int) $row['is_absent'] === 1 ? 'libur' : $row['status'];
                $row['is_absent'] = (bool) $row['is_absent'];
            }
            respond(['date' => $date, 'activity_id' => $activityId, 'data' => $rows]);
        }

        if ($method === 'POST') {
            $body = requestBody();
            $date = requiredDate($body['date'] ?? null, 'date');
            $activityId = positiveId($body['activity_id'] ?? null, 'activity_id');
            $evaluations = $body['evaluations'] ?? null;
            if (!is_array($evaluations)) {
                respond(['error' => 'evaluations wajib berupa array.'], 422);
            }
            $activityCheck = $pdo->prepare('SELECT name FROM activities WHERE id = :id AND is_active = 1');
            $activityCheck->execute(['id' => $activityId]);
            $activityName = $activityCheck->fetchColumn();
            if ($activityName === false) {
                respond(['error' => 'Kegiatan tidak ditemukan atau tidak aktif.'], 422);
            }
            $isPrayerActivity = stripos($activityName, 'shalat') !== false || stripos($activityName, 'sholat') !== false;

            $upsert = $pdo->prepare(
                'INSERT INTO evaluations (evaluation_date, activity_id, child_id, status, notes)
                 VALUES (:evaluation_date, :activity_id, :child_id, :status, :notes)
                 ON DUPLICATE KEY UPDATE status = VALUES(status), notes = VALUES(notes)'
            );
            $childCheck = $pdo->prepare('SELECT is_active, religion FROM children WHERE id = :id');
            $absenceCheck = $pdo->prepare(
                'SELECT 1 FROM daily_absences WHERE absence_date = :date AND child_id = :child_id'
            );
            $saved = 0;
            $skippedAbsent = 0;
            $validStatuses = ['sesuai', 'arahan', 'telat', 'tidak'];

            $pdo->beginTransaction();
            foreach ($evaluations as $evaluation) {
                if (!is_array($evaluation)) {
                    $pdo->rollBack();
                    respond(['error' => 'Setiap item evaluations harus berupa object.'], 422);
                }
                $childId = positiveId($evaluation['child_id'] ?? null, 'child_id');
                $status = $evaluation['status'] ?? null;
                if (!in_array($status, $validStatuses, true)) {
                    $pdo->rollBack();
                    respond(['error' => 'status harus sesuai, arahan, telat, atau tidak.'], 422);
                }
                $childCheck->execute(['id' => $childId]);
                $child = $childCheck->fetch();
                if (!$child || !(bool) $child['is_active']) {
                    $pdo->rollBack();
                    respond(['error' => "Anak dengan ID {$childId} tidak ditemukan atau tidak aktif."], 422);
                }
                $childReligion = mb_strtolower(trim((string) $child['religion']));
                if ($isPrayerActivity && $childReligion !== '' && $childReligion !== 'islam') {
                    $pdo->rollBack();
                    respond(['error' => "Evaluasi sholat tidak berlaku untuk anak dengan ID {$childId} yang bukan beragama Islam."], 422);
                }
                $absenceCheck->execute(['date' => $date, 'child_id' => $childId]);
                if ($absenceCheck->fetchColumn()) {
                    $skippedAbsent++;
                    continue;
                }
                $notes = isset($evaluation['notes']) ? trim((string) $evaluation['notes']) : null;
                $upsert->execute([
                    'evaluation_date' => $date,
                    'activity_id' => $activityId,
                    'child_id' => $childId,
                    'status' => $status,
                    'notes' => $notes,
                ]);
                $saved++;
            }
            $pdo->commit();
            respond(['message' => 'Penilaian tersimpan.', 'saved' => $saved, 'skipped_absent' => $skippedAbsent]);
        }
    }

    if ($resource === 'absences' && $method === 'POST') {
        $body = requestBody();
        $date = requiredDate($body['date'] ?? null, 'date');
        $childIds = $body['child_ids'] ?? null;
        if (!is_array($childIds)) {
            respond(['error' => 'child_ids wajib berupa array; kirim [] untuk menghapus semua izin pada tanggal tersebut.'], 422);
        }
        $childIds = array_values(array_unique(array_map(
            static fn($id) => positiveId($id, 'child_ids'),
            $childIds
        )));

        $pdo->beginTransaction();
        $delete = $pdo->prepare('DELETE FROM daily_absences WHERE absence_date = :date');
        $delete->execute(['date' => $date]);
        $insert = $pdo->prepare(
            'INSERT INTO daily_absences (absence_date, child_id, reason) VALUES (:date, :child_id, :reason)'
        );
        $childCheck = $pdo->prepare('SELECT id FROM children WHERE id = :id AND is_active = 1');
        foreach ($childIds as $childId) {
            $childCheck->execute(['id' => $childId]);
            if (!$childCheck->fetchColumn()) {
                $pdo->rollBack();
                respond(['error' => "Anak aktif dengan ID {$childId} tidak ditemukan."], 422);
            }
            $insert->execute([
                'date' => $date,
                'child_id' => $childId,
                'reason' => isset($body['reason']) ? trim((string) $body['reason']) : null,
            ]);
        }
        $pdo->commit();
        respond(['message' => 'Daftar anak izin diperbarui.', 'count' => count($childIds)]);
    }

    if ($resource === 'schedules') {
        if ($method === 'GET') {
            $statement = $pdo->query(
                'SELECT a.id AS activity_id, a.name, s.rule_type, s.config_json
                 FROM activities a LEFT JOIN activity_schedules s ON s.activity_id = a.id
                 WHERE a.is_active = 1 ORDER BY a.sort_order, a.id'
            );
            $rows = $statement->fetchAll();
            foreach ($rows as &$row) {
                $row['config'] = $row['config_json'] === null ? null : json_decode($row['config_json'], true);
                unset($row['config_json']);
                $row['rule_type'] ??= 'setiap_hari';
            }
            respond(['data' => $rows]);
        }

        if ($method === 'PUT') {
            $body = requestBody();
            $schedules = $body['schedules'] ?? null;
            if (!is_array($schedules)) {
                respond(['error' => 'schedules wajib berupa array.'], 422);
            }
            $rules = ['setiap_hari', 'hari_kerja', 'multi_rentang', 'hari_tertentu'];
            $upsert = $pdo->prepare(
                'INSERT INTO activity_schedules (activity_id, rule_type, config_json)
                 VALUES (:activity_id, :rule_type, :config_json)
                 ON DUPLICATE KEY UPDATE rule_type = VALUES(rule_type), config_json = VALUES(config_json)'
            );
            $activityCheck = $pdo->prepare('SELECT id FROM activities WHERE id = :id AND is_active = 1');
            $pdo->beginTransaction();
            foreach ($schedules as $schedule) {
                if (!is_array($schedule)) {
                    $pdo->rollBack();
                    respond(['error' => 'Setiap item schedules harus berupa object.'], 422);
                }
                $activityId = positiveId($schedule['activity_id'] ?? null, 'activity_id');
                $rule = $schedule['rule_type'] ?? null;
                if (!in_array($rule, $rules, true)) {
                    $pdo->rollBack();
                    respond(['error' => 'rule_type jadwal tidak valid.'], 422);
                }
                $config = $schedule['config'] ?? null;
                if ($config !== null && !is_array($config)) {
                    $pdo->rollBack();
                    respond(['error' => 'config jadwal harus berupa object, array, atau null.'], 422);
                }
                $activityCheck->execute(['id' => $activityId]);
                if (!$activityCheck->fetchColumn()) {
                    $pdo->rollBack();
                    respond(['error' => "Kegiatan aktif dengan ID {$activityId} tidak ditemukan."], 422);
                }
                $upsert->execute([
                    'activity_id' => $activityId,
                    'rule_type' => $rule,
                    'config_json' => $config === null ? null : json_encode($config, JSON_UNESCAPED_UNICODE),
                ]);
            }
            $pdo->commit();
            respond(['message' => 'Jadwal kegiatan tersimpan.', 'saved' => count($schedules)]);
        }
    }

    if ($resource === 'summary' && $method === 'GET') {
        [$start, $end] = dateRange();
        $sql = 'SELECT status, COUNT(*) AS total FROM evaluations WHERE evaluation_date BETWEEN :start AND :end';
        $params = ['start' => $start, 'end' => $end];
        if (isset($_GET['child_id'])) {
            $sql .= ' AND child_id = :child_id';
            $params['child_id'] = positiveId($_GET['child_id'], 'child_id');
        }
        $sql .= ' GROUP BY status';
        $statement = $pdo->prepare($sql);
        $statement->execute($params);
        $counts = ['sesuai' => 0, 'arahan' => 0, 'telat' => 0, 'tidak' => 0];
        foreach ($statement->fetchAll() as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }
        $total = array_sum($counts);
        $percentages = [];
        foreach ($counts as $status => $count) {
            $percentages[$status] = $total === 0 ? 0 : round(($count / $total) * 100, 1);
        }
        respond([
            'start' => $start,
            'end' => $end,
            'total_evaluations' => $total,
            'counts' => $counts,
            'percentages' => $percentages,
        ]);
    }

    if ($resource === 'report' && $method === 'GET') {
        $childId = positiveId($_GET['child_id'] ?? null, 'child_id');
        [$start, $end] = dateRange();
        $statement = $pdo->prepare(
            'SELECT a.id AS activity_id, a.name AS activity,
                    SUM(e.status = "sesuai") AS sesuai,
                    SUM(e.status = "arahan") AS arahan,
                    SUM(e.status = "telat") AS telat,
                    SUM(e.status = "tidak") AS tidak,
                    COUNT(e.id) AS total
             FROM activities a
             LEFT JOIN evaluations e ON e.activity_id = a.id AND e.child_id = :child_id
                 AND e.evaluation_date BETWEEN :start AND :end
             WHERE a.is_active = 1
             GROUP BY a.id, a.name, a.sort_order
             ORDER BY a.sort_order, a.id'
        );
        $statement->execute(['child_id' => $childId, 'start' => $start, 'end' => $end]);
        $rows = $statement->fetchAll();
        foreach ($rows as &$row) {
            foreach (['sesuai', 'arahan', 'telat', 'tidak', 'total'] as $key) {
                $row[$key] = (int) $row[$key];
            }
        }
        unset($row);
        $absenceStatement = $pdo->prepare(
            'SELECT COUNT(*) FROM daily_absences
             WHERE child_id = :child_id AND absence_date BETWEEN :start AND :end'
        );
        $absenceStatement->execute(['child_id' => $childId, 'start' => $start, 'end' => $end]);
        $absenceCount = (int) $absenceStatement->fetchColumn();
        foreach ($rows as &$row) {
            $row['libur'] = $absenceCount;
        }
        unset($row);
        $childStatement = $pdo->prepare('SELECT id, name, gender, religion, tribe, cohort FROM children WHERE id = :id');
        $childStatement->execute(['id' => $childId]);
        $child = $childStatement->fetch();
        if (!$child) {
            respond(['error' => 'Anak tidak ditemukan.'], 404);
        }
        respond(['child' => $child, 'start' => $start, 'end' => $end, 'data' => $rows]);
    }

    if (!in_array($resource, ['health', 'children', 'activities', 'attendance', 'absences', 'schedules', 'summary', 'report'], true)) {
        respond(['error' => 'Resource tidak dikenal.'], 404);
    }
    header('Allow: GET, POST, PUT');
    respond(['error' => 'Method tidak didukung untuk resource ini.'], 405);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log($exception->getMessage());
    respond(['error' => 'Terjadi kesalahan pada server.'], 500);
}

function childExists(PDO $pdo, int $id): bool
{
    $statement = $pdo->prepare('SELECT 1 FROM children WHERE id = :id');
    $statement->execute(['id' => $id]);
    return (bool) $statement->fetchColumn();
}