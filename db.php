<?php
// بيانات الاتصال بقاعدة بيانات Postgres من Render بناءً على الصورة
$host = "dpg-d7te07l0lvsc739523tg-a.ohio-postgres.render.com"; // تم استخدام الرابط الخارجي لضمان الاتصال
$db   = "mail_archive_kh";
$user = "mail_archive_kh_user";
$pass = "vk7iwNURJs6JQMokMtaW4aSrkftAh3wd";
$port = "5432";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("خطأ في الاتصال بالسيرفر: " . $e->getMessage());
}
?>
