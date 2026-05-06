<?php
// بيانات الاتصال بقاعدة بيانات Postgres من Render
$host = "dpg-d7te07l0lvsc739523tg-a.ohio-postgres.render.com"; 
$db   = "mail_archive_kh";
$user = "mail_archive_kh_user";
$pass = "vk7iwNURJs6JQMokMtaW4aSrkftAh3wd";
$port = "5432";

try {
    // تم إضافة sslmode=require لضمان قبول الاتصال من Render
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require"; 
    
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5 // مهلة الاتصال
    ]);
} catch (PDOException $e) {
    die("خطأ في الاتصال بالسيرفر: " . $e->getMessage());
}
?>
