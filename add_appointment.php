<?php
// إعدادات الاتصال المباشرة لضمان تجاوز خطأ Socket المفقود
$host = "dpg-d7te07l0lvsc739523tg-a.ohio-postgres.render.com"; 
$db   = "mail_archive_kh";
$user = "mail_archive_kh_user";
$pass = "vk7iwNURJs6JQMokMtaW4aSrkftAh3wd";
$port = "5432";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // نص الاتصال الصارم بإضافة host= لضمان الاتصال عبر الشبكة وليس محلياً
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
        
        // إنشاء الاتصال مع ضبط وضع الخطأ ليكون استثناءً (Exception)
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // استقبال البيانات من النموذج
        $subject = $_POST['subject'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $date = $_POST['appointment_date'] ?? '';

        if (!empty($subject) && !empty($date)) {
            // تنفيذ جملة الإدخال في جدول appointments
            $sql = "INSERT INTO appointments (subject, notes, appointment_date) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$subject, $notes, $date]);

            $message = "<div class='alert alert-success'>✅ تم حفظ الموعد بنجاح في قاعدة البيانات!</div>";
        } else {
            $message = "<div class='alert alert-warning'>⚠️ يرجى ملء الحقول المطلوبة (الموضوع والتاريخ).</div>";
        }

    } catch (PDOException $e) {
        // سيتم عرض الخطأ التقني بدقة هنا إذا فشل الاتصال أو الإدخال
        $message = "<div class='alert alert-danger'>❌ خطأ في الحفظ: " . $e->getMessage() . "</div>";
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
        body { 
            font-family: 'Tajawal', sans-serif; 
            background-color: #f0f2f5; 
        }
        .card { 
            border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            border: none;
            margin-top: 50px;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary fw-bold m-0">إضافة موعد جديد</h2>
                        <a href="index.php" class="btn btn-dark btn-sm">🏠 الرئيسية</a>
                    </div>

                    <!-- عرض التنبيهات -->
                    <?php echo $message; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">موضوع الموعد</label>
                            <input type="text" name="subject" class="form-control" placeholder="ما هو موضوع الموعد؟" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">الملاحظات</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="اكتب التفاصيل هنا..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">التاريخ والوقت</label>
                            <input type="datetime-local" name="appointment_date" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">💾 حفظ الموعد</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
