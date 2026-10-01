<?php
const DB_HOST = 'localhost';
const DB_NAME = 'ampon_db';
const DB_USER = 'root';
const DB_PASS = '';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $ex) {
    http_response_code(500);
    exit('Could not connect to the database. Start MySQL in XAMPP and import database.sql in phpMyAdmin.');
}

require_once __DIR__ . '/includes/helpers.php';
