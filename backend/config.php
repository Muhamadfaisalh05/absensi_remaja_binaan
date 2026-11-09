<?php

declare(strict_types=1);

$host = getenv('SEMARAK_DB_HOST') ?: '127.0.0.1';
$database = getenv('SEMARAK_DB_NAME') ?: 'semarak_db';
$username = getenv('SEMARAK_DB_USER') ?: 'root';
$password = getenv('SEMARAK_DB_PASSWORD') ?: '';

try {
    return new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Koneksi database gagal. Periksa konfigurasi backend.']);
    exit;
}