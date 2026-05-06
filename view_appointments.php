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

// 3. جلب كافة المواعيد (سنتحكم في الفلترة عبر JavaScript لسرعة البحث)
$query = "SELECT * FROM appointments ORDER BY appointment_date ASC";
$stmt = $pdo->query($query);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>سجل المواعيد الذكي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #0056b3; --bg-gray: #f4f7f9; }
        body { background: var(--bg-gray); font-family: 'Segoe UI', Tahoma, sans-serif; padding-bottom: 30px; }
        
        /* الهيدر العلوي */
        .header-section { background: white; padding: 15px 20px; border-bottom: 1px solid #e0e0e0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        
        /* حقل البحث الذكي */
        .search-box { position: relative; margin: 15px 0; }
        .search-box input { border-radius: 12px; padding: 12px 40px 12px 15px; border: 1.5px solid #eee; background: #fdfdfd; font-size: 0.95rem; width: 100%; transition: 0.3s; }
        .search-box input:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(0,86,179,0.1); outline: none; }
        .search-box i { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #aaa; }

        /* البطاقات */
        .appointment-card { background: white; border: none; border-radius: 16px; margin-bottom: 15px; border-right: 5px solid #ccc; transition: 0.3s; }
        .active-border { border-right-color: #28a745; }
        .expired-border { border-right-color: #6c757d; opacity: 0.8; }
        
        .card-body { padding: 16px; }
        .subject-text { font-size: 1.1rem; font-weight: 700; color: #222; margin-bottom: 4px; }
        .notes-text { font-size: 0.9rem; color: #666; line-height: 1.5; margin-bottom: 12px; }
        .time-info { font-size: 0.85rem; font-weight: 600; color: var(--primary-blue); display: flex; align-items: center; gap: 6px; }

        /* أزرار الإجراءات */
        .card-actions { display: flex; gap: 10px; border-top: 1px solid #f0f0f0; padding-top: 12px; }
        .btn-action { flex: 1; border-radius: 10px; padding: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 5px; border: none; }
        .btn-edit { background: #eef4ff; color: #0056b3; }
        .btn-delete { background: #fff5f5; color: #dc3545; }

        /* أنيميشن البحث */
        .hidden-card { display: none !important; }
    </style>
</head>
<body>

<div class="header-section">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <a href="index.php" class="text-muted"><i class="fas fa-home fa-lg"></i></a>
        <h5 class="fw-bold m-0 text-dark">سجل المواعيد</h5>
        <a href="add_appointment.php" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fas fa-plus"></i> إضافة</a>
    </div>

    <!-- البحث الذكي اللحظي -->
    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="smartSearch" placeholder="ابحث في الموضوع أو الملاحظات..." onkeyup="runSmartSearch()">
    </div>
</div>

<div class="container mt-3">
    <div class="row" id="appointmentsList">
        <?php if (count($appointments) > 0): ?>
            <?php foreach ($appointments as $row): 
                $apptDate = new DateTime($row['appointment_date']);
                $isExpired = ($apptDate < new DateTime());
            ?>
            <div class="col-12 col-md-6 app-item">
                <div class="card appointment-card <?= $isExpired ? 'expired-border' : 'active-border' ?> shadow-sm">
                    <div class="card-body">
                        <div class="time-info mb-2">
                            <i class="far fa-calendar-alt"></i>
                            <?= $apptDate->format('Y-m-d | h:i A') ?>
                        </div>
                        <div class="subject-text"><?= htmlspecialchars($row['subject']) ?></div>
                        <div class="notes-text"><?= nl2br(htmlspecialchars($row['notes'])) ?></div>
                        
                        <div class="card-actions">
                            <button class="btn-action btn-edit" onclick='openEditModal(<?= json_encode($row) ?>)'>
                                <i class="fas fa-pen"></i> تعديل
                            </button>
                            <a href="?delete=<?= $row['id'] ?>" class="btn-action btn-delete" onclick="return confirm('هل تريد حذف الموعد فعلاً؟')">
                                <i class="fas fa-trash"></i> حذف
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center mt-5 text-muted">
                <i class="fas fa-folder-open fa-3x mb-3"></i>
                <p>لا يوجد مواعيد مسجلة حالياً</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- مودال التعديل -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0">
                <h6 class="fw-bold m-0">تعديل بيانات الموعد</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label small text-muted">الموضوع</label>
                        <input type="text" name="subject" id="edit_subject" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">الملاحظات</label>
                        <textarea name="notes" id="edit_notes" class="form-control rounded-3" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">التاريخ والوقت</label>
                        <input type="datetime-local" name="appointment_date" id="edit_date" class="form-control rounded-3" required>
                    </div>
                </div>
                <div class="p-3">
                    <button type="submit" name="update" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// وظيفة البحث الذكي اللحظي
function runSmartSearch() {
    let input = document.getElementById('smartSearch').value.toLowerCase();
    let items = document.getElementsByClassName('app-item');

    for (let i = 0; i < items.length; i++) {
        let subject = items[i].querySelector('.subject-text').innerText.toLowerCase();
        let notes = items[i].querySelector('.notes-text').innerText.toLowerCase();
        
        if (subject.includes(input) || notes.includes(input)) {
            items[i].classList.remove('hidden-card');
        } else {
            items[i].classList.add('hidden-card');
        }
    }
}

// فتح مودال التعديل وتعبئة البيانات
function openEditModal(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_subject').value = data.subject;
    document.getElementById('edit_notes').value = data.notes;
    
    if(data.appointment_date) {
        let dateVal = data.appointment_date.replace(" ", "T").substring(0, 16);
        document.getElementById('edit_date').value = dateVal;
    }

    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>
</body>
</html>
