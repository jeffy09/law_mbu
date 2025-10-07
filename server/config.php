<?php
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'law',
        'user' => 'root',
        'pass' => 'password',
        'charset' => 'utf8mb4'
    ],
    'security' => [
        'secret_key' => 'YcQT6SiURMbIqjwlu6tK5rUh3KyYdMQW', // คีย์สำหรับเข้ารหัส
        'cipher' => 'AES-256-CBC' // วิธีเข้ารหัส
    ]
];
?>