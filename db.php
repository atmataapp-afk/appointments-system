<?php
$host = "dpg-d7te07l0lvsc739523tg-a.ohio-postgres.render.com"; 
$db   = "mail_archive_kh";
$user = "mail_archive_kh_user";
$pass = "vk7iwNURJs6JQMokMtaW4aSrkftAh3wd";
$port = "5432";

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // إضافة حقل الحالة إذا لم يكن موجوداً
    $pdo->exec("ALTER TABLE appointments ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'active'");
    
    // كود ذكي: تحديث المواعيد التي فات تاريخها لتصبح 'expired' تلقائياً
    $pdo->exec("UPDATE appointments SET status = 'expired' WHERE appointment_date < NOW() AND status = 'active'");

} catch (PDOException $e) {
    die("خطأ: " . $e->getMessage());
}
?>
