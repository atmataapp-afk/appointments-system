<?php
// بيانات الاتصال بقاعدة بيانات Postgres من Render
$host = "dpg-d7te07l0lvsc739523tg-a";
$db   = "mail_archive_kh";
$user = "mail_archive_kh_user";
$pass = "vk7iwNURJs6JQMokMtaW4aSrkftAh3wd";
$port = "5432";

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("خطأ في الاتصال: " . $e->getMessage());
}
?>
