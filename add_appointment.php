<?php
include 'db.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'];
    $notes = $_POST['notes'];
    $appointment_date = $_POST['appointment_date'];

    try {
        $sql = "INSERT INTO appointments (subject, notes, appointment_date, status) VALUES (?, ?, ?, 'active')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$subject, $notes, $appointment_date]);
        $message = "<div class='alert alert-success animate__animated animate__fadeIn'>✅ تم حفظ الموعد بنجاح!</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>❌ خطأ في الحفظ: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>إضافة موعد جديد</title>
    <!-- Bootstrap RTL & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #0062cc 0%, #004085 100%); }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        
        /* الهيدر العلوي المحسن */
        .top-nav { background: white; padding: 12px 20px; border-bottom: 1px solid #ddd; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .nav-link-custom { text-decoration: none; color: #666; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 5px; }
        
        .form-container { max-width: 500px; margin: 20px auto; padding: 15px; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .card-header-custom { background: var(--primary-gradient); color: white; padding: 25px; text-align: center; border: none; }
        
        .form-label { font-weight: 600; color: #444; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .form-control { border-radius: 12px; padding: 12px 15px; border: 1.5px solid #eee; background-color: #f8f9fa; transition: 0.3s; font-size: 1rem; }
        .form-control:focus { background-color: #fff; border-color: #0062cc; box-shadow: 0 0 0 0.25rem rgba(0,98,204,0.1); }
        
        .btn-submit { background: var(--primary-gradient); border: none; border-radius: 15px; padding: 15px; font-weight: 700; font-size: 1.1rem; color: white; width: 100%; box-shadow: 0 5px 15px rgba(0,98,204,0.3); transition: 0.3s; }
        .btn-submit:active { transform: scale(0.98); }

        .secondary-actions { display: flex; gap: 10px; margin-top: 15px; }
        .btn-outline-custom { flex: 1; border: 1.5px solid #ddd; border-radius: 12px; padding: 10px; text-decoration: none; color: #555; text-align: center; font-size: 0.9rem; font-weight: 600; transition: 0.3s; }
        .btn-outline-custom:hover { background: #f8f9fa; border-color: #bbb; }
    </style>
</head>
<body>

<div class="top-nav shadow-sm">
    <a href="index.php" class="nav-link-custom"><i class="fas fa-home"></i> الرئيسية</a>
    <span class="fw-bold">نظام المواعيد</span>
    <a href="view_appointments.php" class="nav-link-custom text-primary"><i class="fas fa-list-ul"></i> السجل</a>
</div>

<div class="container form-container">
    <?= $message ?>

    <div class="card card-custom animate__animated animate__fadeInUp">
        <div class="card-header-custom">
            <i class="fas fa-calendar-plus fa-3x mb-3"></i>
            <h4 class="m-0">حجز موعد جديد</h4>
            <p class="small mb-0 opacity-75">سجل بيانات الموعد القادم</p>
        </div>
        
        <div class="card-body p-4">
            <form method="POST" id="appointmentForm">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-heading"></i> موضوع الموعد</label>
                    <input type="text" name="subject" class="form-control" placeholder="مثال: مراجعة العقد الفني" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-clock"></i> التاريخ والوقت</label>
                    <input type="datetime-local" name="appointment_date" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label"><i class="fas fa-align-left"></i> ملاحظات إضافية</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="تفاصيل الاجتماع أو الموقع..."></textarea>
                </div>

                <button type="submit" class="btn btn-submit mb-3">
                    <i class="fas fa-save me-2"></i> حفظ الموعد الآن
                </button>
                
                <div class="secondary-actions">
                    <a href="view_appointments.php" class="btn-outline-custom">
                        <i class="fas fa-calendar-alt me-1"></i> قائمة المواعيد
                    </a>
                    <a href="index.php" class="btn-outline-custom">
                        <i class="fas fa-home me-1"></i> الرئيسية
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('appointmentForm').onsubmit = function() {
        let btn = this.querySelector('.btn-submit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';
        btn.style.opacity = '0.7';
    };
</script>

</body>
</html>
