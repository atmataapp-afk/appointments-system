<?php 
include 'db.php'; 

// 1. معالجة الحذف
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: view_appointments.php");
    exit();
}

// 2. معالجة التحديث (Update)
if (isset($_POST['update'])) {
    $sql = "UPDATE appointments SET subject=?, notes=?, appointment_date=? WHERE id=?";
    $pdo->prepare($sql)->execute([
        $_POST['subject'], 
        $_POST['notes'], 
        $_POST['appointment_date'], 
        $_POST['id']
    ]);
    header("Location: view_appointments.php");
    exit();
}

// 3. نظام الفلتر
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'active';
$query = "SELECT * FROM appointments";

if ($filter == 'active') {
    $query .= " WHERE appointment_date >= NOW()";
} elseif ($filter == 'expired') {
    $query .= " WHERE appointment_date < NOW()";
}
$query .= " ORDER BY appointment_date ASC";
$stmt = $pdo->query($query);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>إدارة المواعيد الذكية</title>
    <!-- Bootstrap RTL & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-color: #0056b3; --accent-color: #f0f2f5; }
        body { background: var(--accent-color); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding-bottom: 50px; }
        
        /* تصميم الهيدر للجوال */
        .page-header { background: white; padding: 20px; border-bottom: 1px solid #ddd; margin-bottom: 20px; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        /* تنسيق البطاقات للجوال */
        .appointment-card { background: white; border: none; border-radius: 15px; margin-bottom: 15px; transition: 0.3s; border-right: 6px solid #ccc; position: relative; }
        .active-border { border-right-color: #28a745; }
        .expired-border { border-right-color: #6c757d; opacity: 0.85; }
        
        .card-body { padding: 18px; }
        .subject-text { font-size: 1.1rem; font-weight: 700; color: #333; margin-bottom: 5px; }
        .notes-text { font-size: 0.9rem; color: #666; line-height: 1.4; }
        .time-box { font-size: 0.85rem; font-weight: 600; color: var(--primary-color); display: flex; align-items: center; gap: 5px; }
        
        /* أزرار التحكم السريعة */
        .action-btns { display: flex; gap: 10px; margin-top: 15px; border-top: 1px solid #eee; padding-top: 12px; }
        .btn-action { flex: 1; border-radius: 10px; padding: 8px; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 5px; }
        
        /* الفلاتر بشكل أزرار دائرية للجوال */
        .filter-nav { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; scrollbar-width: none; }
        .filter-nav::-webkit-scrollbar { display: none; }
        .nav-pill-custom { white-space: nowrap; padding: 8px 20px; border-radius: 20px; background: #e9ecef; color: #555; text-decoration: none; font-size: 0.9rem; transition: 0.3s; }
        .nav-pill-custom.active { background: var(--primary-color); color: white; box-shadow: 0 4px 10px rgba(0,86,179,0.3); }

        /* تعديل المودال للجوال */
        .modal-content { border-radius: 20px 20px 0 0; position: fixed; bottom: 0; width: 100%; margin: 0; }
        @media (min-width: 576px) { .modal-content { border-radius: 20px; position: relative; } }
    </style>
</head>
<body>

<div class="page-header text-center">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="index.php" class="btn btn-light rounded-circle shadow-sm"><i class="fas fa-arrow-right"></i></a>
        <h4 class="fw-bold m-0 text-dark">جدول المواعيد</h4>
        <a href="add_appointment.php" class="btn btn-primary rounded-pill btn-sm px-3 shadow"><i class="fas fa-plus"></i></a>
    </div>
    
    <!-- الفلاتر -->
    <div class="filter-nav">
        <a href="?filter=active" class="nav-pill-custom <?= $filter == 'active' ? 'active' : '' ?>">القادمة</a>
        <a href="?filter=expired" class="nav-pill-custom <?= $filter == 'expired' ? 'active' : '' ?>">المنتهية (الأرشيف)</a>
        <a href="?filter=all" class="nav-pill-custom <?= $filter == 'all' ? 'active' : '' ?>">الكل</a>
    </div>
</div>

<div class="container">
    <div class="row">
        <?php if (count($appointments) > 0): ?>
            <?php foreach ($appointments as $row): 
                $apptDate = new DateTime($row['appointment_date']);
                $isExpired = ($apptDate < new DateTime());
            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card appointment-card <?= $isExpired ? 'expired-border' : 'active-border' ?> shadow-sm">
                    <div class="card-body">
                        <div class="time-box mb-2">
                            <i class="far fa-clock"></i>
                            <?= $apptDate->format('Y-m-d | h:i A') ?>
                        </div>
                        <div class="subject-text"><?= htmlspecialchars($row['subject']) ?></div>
                        <div class="notes-text"><?= nl2br(htmlspecialchars($row['notes'])) ?></div>
                        
                        <div class="action-btns">
                            <button class="btn btn-light btn-action text-primary" onclick='openEditModal(<?= json_encode($row) ?>)'>
                                <i class="fas fa-edit"></i> تعديل
                            </button>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-light btn-action text-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')">
                                <i class="fas fa-trash-alt"></i> حذف
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-calendar-day fa-3x text-muted mb-3"></i>
                <p class="text-muted">لا توجد مواعيد في هذا القسم حالياً</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- مودال التعديل الاحترافي للجوال -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">تعديل الموعد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">الموضوع</label>
                        <textarea name="subject" id="edit_subject" class="form-control bg-light border-0" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">الملاحظات</label>
                        <textarea name="notes" id="edit_notes" class="form-control bg-light border-0" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">التاريخ والوقت</label>
                        <input type="datetime-local" name="appointment_date" id="edit_date" class="form-control bg-light border-0" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="update" class="btn btn-primary w-100 py-3 fw-bold rounded-pill">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEditModal(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_subject').value = data.subject;
    document.getElementById('edit_notes').value = data.notes;
    
    // تنسيق التاريخ للمدخل
    if(data.appointment_date) {
        let dateVal = data.appointment_date.replace(" ", "T").substring(0, 16);
        document.getElementById('edit_date').value = dateVal;
    }

    var myModal = new bootstrap.Modal(document.getElementById('editModal'));
    myModal.show();
}
</script>
</body>
</html>
