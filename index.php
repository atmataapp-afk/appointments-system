<?php
$host = "dpg-d7te07l0lvsc739523tg-a.ohio-postgres.render.com"; 
$db   = "mail_archive_kh";
$user = "mail_archive_kh_user";
$pass = "vk7iwNURJs6JQMokMtaW4aSrkftAh3wd";

$appointments = [];
$error = "";

try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db;sslmode=require";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // جلب المواعيد مرتبة من الأحدث إلى الأقدم
    $stmt = $pdo->query("SELECT * FROM appointments ORDER BY appointment_date DESC");
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "❌ فشل جلب البيانات: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معاينة المواعيد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #f8f9fa; padding-top: 50px; }
        .card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .table thead { background-color: #0d6efd; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-primary fw-bold m-0">قائمة المواعيد</h2>
                <a href="add_appointment.php" class="btn btn-success fw-bold">➕ إضافة موعد جديد</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>الموضوع</th>
                            <th>الملاحظات</th>
                            <th>التاريخ والوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $row): ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes']); ?></td>
                            <td class="text-muted"><?php echo date('Y-m-d h:i A', strtotime($row['appointment_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($appointments) && !$error): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">لا توجد مواعيد مسجلة حالياً.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
