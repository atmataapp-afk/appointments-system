<?php
include 'db.php';

// اسم الملف مع التاريخ لتمييز النسخة
$backup_file = 'appointments_backup_' . date('Y-m-d_H-i-s') . '.sql';

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . $backup_file . "\"");

echo "-- نسخة احتياطية لجدول المواعيد \n";
echo "-- التاريخ: " . date('Y-m-d H:i:s') . "\n\n";

// إضافة أمر إنشاء الجدول لضمان سهولة الاستعادة لاحقاً
echo "CREATE TABLE IF NOT EXISTS appointments (
    id SERIAL PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    notes TEXT,
    appointment_date TIMESTAMP NOT NULL,
    status VARCHAR(20) DEFAULT 'active'
);\n\n";

try {
    $stmt = $pdo->query("SELECT * FROM appointments");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $columns = array_keys($row);
        $values = array_map(function($value) use ($pdo) {
            return $value === null ? 'NULL' : $pdo->quote($value);
        }, array_values($row));

        echo "INSERT INTO appointments (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
    }
} catch (Exception $e) {
    echo "-- خطأ في توليد النسخة: " . $e->getMessage();
}
exit;
