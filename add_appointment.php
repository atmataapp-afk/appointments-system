<?php
// بيانات الاتصال المباشرة لضمان الوصول للسيرفر الخارجي
$host = "dpg-d7te07l0lvsc739523tg-a.ohio-postgres.render.com"; 
$db   = "mail_archive_kh";
$user = "mail_archive_kh_user";
$pass = "vk7iwNURJs6JQMokMtaW4aSrkftAh3wd";
$port = "5432";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // نص اتصال يجبر النظام على استخدام الشبكة وتجاوز خطأ No such file or directory
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

        $subject = $_POST['subject'];
        $notes = $_POST['notes'];
        $date = $_POST['appointment_date'];

        // الإدخال في الجدول الذي أنشأته بنجاح
        $sql = "INSERT INTO appointments (subject, notes, appointment_date) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$subject, $notes, $date]);

        $message = "<div class='alert alert-success shadow-sm'>✅ تم حفظ الموعد بنجاح!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger shadow-sm'>❌ خطأ في الحفظ: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة موعد جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; }
        .card { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-primary { padding: 12px; font-weight: bold; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary fw-bold m-0">إضافة موعد</h2>
                        <a href="index.php" class="btn btn-outline-dark btn-sm">🏠 المواعيد</a>
                    </div>

                    <?php echo $message; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">موضوع الموعد</label>
                            <input type="text" name="subject" class="form-control form-control-lg" placeholder="مثال: اجتماع اللجنة" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">الملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="تفاصيل إضافية..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">التاريخ والوقت</label>
                            <input type="datetime-local" name="appointment_date" class="form-control form-control-lg" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">💾 حفظ الموعد الآن</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
