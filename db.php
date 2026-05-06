<?php
$host = "dpg-d7te07l0lvsc739523tg-a.ohio-postgres.render.com"; 
$db   = "mail_archive_kh";
$user = "mail_archive_kh_user";
$pass = "vk7iwNURJs6JQMokMtaW4aSrkftAh3wd";
$port = "5432";

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // كود سحري لإنشاء الجدول إذا لم يكن موجوداً
    $sql = "CREATE TABLE IF NOT EXISTS appointments (
        id SERIAL PRIMARY KEY,
        subject TEXT NOT NULL,
        notes TEXT,
        appointment_date TIMESTAMP NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";
    
    $pdo->exec($sql);

} catch (PDOException $e) {
    die("خطأ في الاتصال: " . $e->getMessage());
}
?>
