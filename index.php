<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المواعيد | الرئيسية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; height: 100vh; display: flex; align-items: center; }
        .main-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 800px; margin: auto; }
        .menu-btn { padding: 30px; border-radius: 15px; border: 2px solid #eee; transition: 0.3s; text-decoration: none; color: #333; display: block; text-align: center; }
        .menu-btn:hover { border-color: #0056b3; transform: translateY(-5px); background: #f8f9ff; }
        .icon-circle { width: 70px; height: 70px; background: #0056b3; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 28px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-card">
            <h2 class="text-center mb-5 fw-bold" style="color: #0056b3;">نظام المواعيد</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <a href="add_appointment.php" class="menu-btn">
                        <div class="icon-circle"><i class="fas fa-plus"></i></div>
                        <h4>تسجيل موعد</h4>
                        <p class="text-muted small">إضافة موعد جديد إلى الجدول</p>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="view_appointments.php" class="menu-btn">
                        <div class="icon-circle" style="background: #495057;"><i class="fas fa-calendar-check"></i></div>
                        <h4>معاينة المواعيد</h4>
                        <p class="text-muted small">عرض، بحث وتعديل المواعيد</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
