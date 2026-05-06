<?php
include 'db.php';
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "INSERT INTO appointments (subject, notes, appointment_date) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_POST['subject'], $_POST['notes'], $_POST['appointment_date']]);
        
        $msg = "<div class='alert alert-success shadow-sm'>✅ تم حفظ الموعد بنجاح!</div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>❌ خطأ في الحفظ: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة موعد جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .form-card { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); margin-top: 40px; }
        textarea { resize: none; overflow: hidden; min-height: 45px; }
        .btn-back { background-color: #212529; color: white; border-radius: 8px; padding: 8px 20px; text-decoration: none; display: inline-flex; align-items: center; border: none; }
        .btn-back:hover { background-color: #323539; color: #fff; transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="form-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="index.php" class="btn-back"><i class="fas fa-home"></i> الرئيسية</a>
                    <h3 class="fw-bold m-0 text-primary">إضافة موعد جديد</h3>
                </div>
                <?= $msg ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">موضوع الموعد</label>
                        <textarea name="subject" class="form-control" placeholder="ما هو موضوع الموعد؟" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الملاحظات</label>
                        <textarea name="notes" class="form-control" placeholder="اكتب التفاصيل هنا..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">التاريخ والوقت</label>
                        <input type="datetime-local" name="appointment_date" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow">حفظ الموعد <i class="fas fa-save ms-1"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
