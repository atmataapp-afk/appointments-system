<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المواعيد - الرئيسية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Tajawal', sans-serif; background: #f4f7f6; height: 100vh; display: flex; align-items: center; }
        .main-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 40px; width: 100%; max-width: 500px; margin: auto; text-align: center; }
        .btn-custom { padding: 20px; font-size: 1.2rem; border-radius: 15px; transition: 0.3s; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .btn-register { background-color: #002366; color: white; } /* Royal Blue */
        .btn-view { background-color: #708090; color: white; } /* Slate Gray */
        .btn-custom:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); color: white; }
        .btn-custom i { margin-left: 15px; font-size: 1.5rem; }
    </style>
</head>
<body>
<div class="container text-center">
    <div class="main-card">
        <h2 class="mb-5 fw-bold text-dark">نظام المواعيد الذكي</h2>
        <a href="index.php" class="btn-custom btn-register w-100">
            <i class="fas fa-plus-circle"></i> تسجيل موعد جديد
        </a>
        <a href="view_appointments.php" class="btn-custom btn-view w-100">
            <i class="fas fa-calendar-alt"></i> معاينة المواعيد
        </a>
    </div>
</div>
</body>
</html>