<?php 
include 'db.php'; 

// 1. معالجة الحذف والتحديث
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: view_appointments.php");
    exit();
}

if (isset($_POST['update'])) {
    $sql = "UPDATE appointments SET subject=?, notes=?, appointment_date=? WHERE id=?";
    $pdo->prepare($sql)->execute([$_POST['subject'], $_POST['notes'], $_POST['appointment_date'], $_POST['id']]);
    header("Location: view_appointments.php");
    exit();
}

// جلب البيانات
$stmt = $pdo->query("SELECT * FROM appointments ORDER BY appointment_date ASC");
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>سجل المواعيد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-blue: #0056b3; --bg-gray: #f4f7f9; }
        body { background: var(--bg-gray); font-family: 'Segoe UI', Tahoma, sans-serif; padding-bottom: 30px; transition: opacity 0.3s; }
        
        /* منع التفاعل مع الصفحة أثناء المعالجة */
        .is-loading { pointer-events: none; opacity: 0.6; cursor: wait; }

        .header-section { background: white; padding: 15px 20px; border-bottom: 1px solid #e0e0e0; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .search-box { position: relative; margin: 10px 0; }
        .search-box input { border-radius: 12px; padding: 10px 35px 10px 15px; border: 1.5px solid #eee; background: #fdfdfd; width: 100%; outline: none; }
        .search-box i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #aaa; }

        .filter-tabs { display: flex; gap: 8px; overflow-x: auto; padding: 5px 0; scrollbar-width: none; }
        .filter-btn { white-space: nowrap; padding: 6px 18px; border-radius: 20px; border: none; background: #e9ecef; color: #555; font-size: 0.85rem; font-weight: 600; transition: 0.3s; }
        .filter-btn.active { background: var(--primary-blue); color: white; box-shadow: 0 4px 10px rgba(0,86,179,0.2); }

        .appointment-card { background: white; border: none; border-radius: 16px; margin-bottom: 15px; border-right: 5px solid #ccc; transition: 0.3s; }
        .active-border { border-right-color: #28a745; }
        .expired-border { border-right-color: #6c757d; opacity: 0.85; }
        
        .card-body { padding: 16px; }
        .subject-text { font-size: 1.05rem; font-weight: 700; color: #222; }
        .notes-text { font-size: 0.9rem; color: #666; margin-bottom: 10px; }
        .time-info { font-size: 0.8rem; font-weight: 600; color: var(--primary-blue); margin-bottom: 5px; }

        .card-actions { display: flex; gap: 8px; border-top: 1px solid #f0f0f0; padding-top: 10px; }
        .btn-action { flex: 1; border-radius: 10px; padding: 7px; font-size: 0.8rem; font-weight: 600; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 4px; border: none; }
        .btn-edit { background: #eef4ff; color: #0056b3; }
        .btn-delete { background: #fff5f5; color: #dc3545; }

        .hidden-item { display: none !important; }

        /* تنسيق الحقول المتمددة تلقائياً */
        .auto-expand { resize: none; overflow: hidden; min-height: 45px; transition: border-color 0.3s; }
    </style>
</head>
<body>

<div class="header-section text-center">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <a href="index.php" class="text-muted"><i class="fas fa-home fa-lg"></i></a>
        <h6 class="fw-bold m-0">سجل المواعيد</h6>
        <div class="d-flex gap-2">
            <a href="backup_sql.php" class="btn btn-outline-dark btn-sm rounded-pill px-3" title="سحب نسخة احتياطية">
                <i class="fas fa-database"></i>
            </a>
            <a href="add_appointment.php" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="fas fa-plus"></i>
            </a>
        </div>
    </div>

    <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="smartSearch" placeholder="ابحث هنا..." onkeyup="applyFilters()">
    </div>

    <div class="filter-tabs">
        <button class="filter-btn active" onclick="setFilter('all', this)">الكل</button>
        <button class="filter-btn" onclick="setFilter('active', this)">القادمة</button>
        <button class="filter-btn" onclick="setFilter('expired', this)">المنتهية</button>
    </div>
</div>

<div class="container mt-3">
    <div class="row" id="appointmentsList">
        <?php foreach ($appointments as $row): 
            $apptDate = new DateTime($row['appointment_date']);
            $isExpired = ($apptDate < new DateTime());
            $status = $isExpired ? 'expired' : 'active';
        ?>
        <div class="col-12 col-md-6 app-item" data-status="<?= $status ?>">
            <div class="card appointment-card <?= $isExpired ? 'expired-border' : 'active-border' ?> shadow-sm">
                <div class="card-body">
                    <div class="time-info">
                        <i class="far fa-calendar-alt"></i> <?= $apptDate->format('Y-m-d | h:i A') ?>
                    </div>
                    <div class="subject-text"><?= htmlspecialchars($row['subject']) ?></div>
                    <div class="notes-text"><?= nl2br(htmlspecialchars($row['notes'])) ?></div>
                    
                    <div class="card-actions">
                        <button class="btn-action btn-edit" onclick='openEditModal(<?= json_encode($row) ?>)'>
                            <i class="fas fa-pen"></i> تعديل
                        </button>
                        <!-- زر الحذف المطور مع الحماية -->
                        <a href="?delete=<?= $row['id'] ?>" class="btn-action btn-delete delete-link" onclick="return confirmDelete(event, this)">
                            <i class="fas fa-trash"></i> حذف
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- مودال التعديل -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-3">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الموضوع</label>
                        <textarea name="subject" id="edit_subject" class="form-control rounded-3 auto-expand" rows="1" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الملاحظات</label>
                        <textarea name="notes" id="edit_notes" class="form-control rounded-3 auto-expand" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">التاريخ</label>
                        <input type="datetime-local" name="appointment_date" id="edit_date" class="form-control rounded-3" required>
                    </div>
                    
                    <button type="submit" name="update" class="btn btn-primary w-100 rounded-pill py-2 fw-bold mt-2">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// 1. وظيفة التمدد التلقائي للحقول
function adjustHeight(el) {
    el.style.height = 'auto';
    el.style.height = (el.scrollHeight) + 'px';
}

document.querySelectorAll('.auto-expand').forEach(el => {
    el.addEventListener('input', () => adjustHeight(el));
});

// 2. حماية الحذف من النقر المزدوج
function confirmDelete(event, element) {
    if (!confirm('هل أنت متأكد من حذف هذا الموعد؟')) {
        event.preventDefault();
        return false;
    }
    // تفعيل وضع التحميل ومنع التفاعل
    document.body.classList.add('is-loading');
    element.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // تعطيل كافة روابط الحذف الأخرى
    document.querySelectorAll('.delete-link').forEach(link => {
        link.style.pointerEvents = 'none';
    });
    return true;
}

// 3. نظام الفلترة والبحث
let currentStatusFilter = 'all';

function setFilter(status, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentStatusFilter = status;
    applyFilters();
}

function applyFilters() {
    let searchText = document.getElementById('smartSearch').value.toLowerCase();
    let items = document.getElementsByClassName('app-item');

    for (let item of items) {
        let subject = item.querySelector('.subject-text').innerText.toLowerCase();
        let notes = item.querySelector('.notes-text').innerText.toLowerCase();
        let status = item.getAttribute('data-status');

        let matchSearch = subject.includes(searchText) || notes.includes(searchText);
        let matchStatus = (currentStatusFilter === 'all') || (status === currentStatusFilter);

        if (matchSearch && matchStatus) {
            item.classList.remove('hidden-item');
        } else {
            item.classList.add('hidden-item');
        }
    }
}

// 4. فتح المودال مع تعديل الحجم
function openEditModal(data) {
    document.getElementById('edit_id').value = data.id;
    const subjectField = document.getElementById('edit_subject');
    const notesField = document.getElementById('edit_notes');
    
    subjectField.value = data.subject;
    notesField.value = data.notes;
    
    if(data.appointment_date) {
        document.getElementById('edit_date').value = data.appointment_date.replace(" ", "T").substring(0, 16);
    }

    const modalEl = document.getElementById('editModal');
    const modal = new bootstrap.Modal(modalEl);
    
    modalEl.addEventListener('shown.bs.modal', function () {
        adjustHeight(subjectField);
        adjustHeight(notesField);
    }, { once: true });

    modal.show();
}
</script>
</body>
</html>
