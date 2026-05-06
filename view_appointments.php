<?php
include 'db.php';

// معالجة الحذف
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: view_appointments.php");
    exit();
}

// معالجة التحديث
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

$query = "SELECT * FROM appointments ORDER BY appointment_date ASC";
$stmt = $pdo->query($query);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سجل المواعيد - قطاع الهندسة الصحية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .card-table { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .auto-grow { resize: none; overflow: hidden; min-height: 45px; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark"><i class="fas fa-calendar-alt text-primary me-2"></i> سجل المواعيد العام</h2>
        <a href="index.php" class="btn btn-dark px-4 shadow-sm">الرئيسية <i class="fas fa-home ms-1"></i></a>
    </div>

    <div class="card card-table">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>الموضوع</th>
                        <th>الملاحظات</th>
                        <th class="text-center">التاريخ والوقت</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($appointments) > 0): ?>
                        <?php foreach ($appointments as $row): 
                            $apptDate = new DateTime($row['appointment_date']);
                        ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($row['subject']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($row['notes']) ?></td>
                            <td class="text-center">
                                <span class="d-block fw-bold"><?= $apptDate->format('Y-m-d') ?></span>
                                <span class="badge bg-light text-dark border"><?= $apptDate->format('h:i A') ?></span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick='openEditModal(<?= json_encode($row) ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('هل أنت متأكد؟')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted">لا توجد مواعيد حالياً</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- مودال التعديل -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white text-end">
                <h5 class="modal-title fw-bold w-100">تعديل الموعد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">الموضوع</label>
                        <textarea name="subject" id="edit_subject" class="form-control auto-grow" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الملاحظات</label>
                        <textarea name="notes" id="edit_notes" class="form-control auto-grow"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">التاريخ والوقت</label>
                        <input type="datetime-local" name="appointment_date" id="edit_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" name="update" class="btn btn-primary px-4 fw-bold">حفظ التغييرات</button>
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
    let dateVal = data.appointment_date.replace(" ", "T").substring(0, 16);
    document.getElementById('edit_date').value = dateVal;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
</body>
</html>
