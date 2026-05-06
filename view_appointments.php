<?php 
include 'db.php'; 

// نظام الفلتر
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$query = "SELECT * FROM appointments";

if ($filter == 'active') {
    $query .= " WHERE status = 'active' AND appointment_date >= NOW()";
} elseif ($filter == 'expired') {
    $query .= " WHERE status = 'expired' OR appointment_date < NOW()";
}
$query .= " ORDER BY appointment_date ASC";
$stmt = $pdo->query($query);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>معاينة المواعيد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-active { border-right: 5px solid #28a745; background-color: #f8fff9; }
        .status-expired { border-right: 5px solid #6c757d; background-color: #f1f1f1; opacity: 0.8; }
        .badge-active { background-color: #28a745; }
        .badge-expired { background-color: #6c757d; }
    </style>
</head>
<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-4 text-center">📅 سجل المواعيد الذكي</h2>
        
        <!-- أزرار الفلتر المتقدمة -->
        <div class="d-flex justify-content-center mb-4 gap-2">
            <a href="?filter=all" class="btn btn-outline-primary <?= $filter == 'all' ? 'active' : '' ?>">الكل</a>
            <a href="?filter=active" class="btn btn-outline-success <?= $filter == 'active' ? 'active' : '' ?>">القادمة فقط</a>
            <a href="?filter=expired" class="btn btn-outline-secondary <?= $filter == 'expired' ? 'active' : '' ?>">الأرشيف (المنتهية)</a>
        </div>

        <div class="row">
            <?php foreach ($appointments as $app): 
                $isExpired = (strtotime($app['appointment_date']) < time());
                $class = $isExpired ? 'status-expired' : 'status-active';
                $statusText = $isExpired ? 'منتهي' : 'قادم';
                $badgeClass = $isExpired ? 'badge-expired' : 'badge-active';
            ?>
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm <?= $class ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title"><?= htmlspecialchars($app['subject']) ?></h5>
                            <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                        </div>
                        <p class="card-text text-muted mb-1 small"><?= htmlspecialchars($app['notes']) ?></p>
                        <div class="text-primary fw-bold mt-2">
                            🕒 <?= date('Y-m-d H:i', strtotime($app['appointment_date'])) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
