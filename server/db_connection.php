<?php
define('SECURE_ACCESS', true);
class Database {
    private $conn;

    public function getConnection() {
        $config = include __DIR__ . '/config.php'; // โหลดค่าจาก config.php
        
        $host = $config['db']['host'];
        $db_name = $config['db']['name'];
        $username = $config['db']['user'];
        $password = $config['db']['pass'];
        $charset = $config['db']['charset'];

        $this->conn = null;

        try {
            $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
            $this->conn = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $exception) {
            throw new Exception('การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $exception->getMessage());
        }

        return $this->conn;
    }
}

// ใช้งาน
$db = new Database();
$connection = $db->getConnection();
?>