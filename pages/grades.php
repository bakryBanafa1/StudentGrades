<?php
session_start();
require_once '../config/db.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$student_name = isset($_SESSION['student_name']) ? $_SESSION['student_name'] : $student_id;

// استعلام إضافي للحصول على معلومات الطالب
$student_info_sql = "SELECT 
    s.level_id,
    l.name_ar AS level_name,
    s.college_id,
    c.name_ar AS college_name,
    s.major_id,
    m.name_ar AS major_name
FROM students s
JOIN levels l ON s.level_id = l.id
JOIN colleges c ON s.college_id = c.id
JOIN majors m ON s.major_id = m.id
WHERE s.student_id = ?";

$stmt_info = $conn->prepare($student_info_sql);
$stmt_info->bind_param("s", $student_id);
$stmt_info->execute();
$student_info_result = $stmt_info->get_result();

if ($student_info = $student_info_result->fetch_assoc()) {
    $level_name = $student_info['level_name'];
    $college_name = $student_info['college_name'];
    $major_name = $student_info['major_name'];
} else {
    $level_name = "غير محدد";
    $college_name = "غير محدد";
    $major_name = "غير محدد";
}
$stmt_info->close();

// Fetch grades with Academic Year info
$sql = "SELECT 
            g.gsemster, 
            g.gexam, 
            g.gfinal, 
            g.gpa,
            g.letter_grade, 
            g.result,
            g.note, 
            c.name_ar AS course_ar, 
            c.name_en AS course_en,
            s.name_ar AS semester_name,
            ay.label AS academic_year,
            ay.id AS year_id,
            s.id AS semester_id
        FROM grades g
        JOIN courses c ON g.course_id = c.id
        JOIN semesters s ON g.semester_id = s.id
        JOIN academic_years ay ON g.academic_year_id = ay.id
        WHERE g.student_id = ?
        ORDER BY ay.id DESC, s.id DESC, c.name_ar ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Organize data into hierarchical structure
$transcript = [];
while ($row = $result->fetch_assoc()) {
    $year = $row['academic_year'];
    $semester = $row['semester_name'];
    
    if (!isset($transcript[$year])) {
        $transcript[$year] = [];
    }
    if (!isset($transcript[$year][$semester])) {
        $transcript[$year][$semester] = [];
    }
    
    $transcript[$year][$semester][] = $row;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتائج الامتحانات | <?php echo htmlspecialchars($student_name); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="grades-page">

    <header class="main-header">
        <div class="header-container">
            <div class="logo-wrapper">
                 <img src="../assets/images/11.jpg" alt="University Logo" class="header-logo">
            </div>
            <div class="student-info">
                <h2>كشف الدرجات</h2>
                <p>الطالب: <strong><?php echo htmlspecialchars($student_name); ?></strong></p>
                <p>رقم القيد: <strong><?php echo htmlspecialchars($student_id); ?></strong></p>
                <p>الكلية: <strong><?php echo htmlspecialchars($college_name); ?></strong></p>
                <p>التخصص: <strong><?php echo htmlspecialchars($major_name); ?></strong></p>
                <p>المستوى: <strong><?php echo htmlspecialchars($level_name); ?></strong></p>
            </div>      
            <div class="logout-wrapper">
                <a href="../auth/logout.php" class="btn-logout">تسجيل الخروج</a>
            </div>
        </div>
    </header>

    <main class="content-container">
        
        <?php if (!empty($transcript)): ?>
            
            <?php foreach ($transcript as $year => $semesters): ?>
                <div class="academic-year-block">
                    <h3 class="year-title">العام الجامعي: <?php echo htmlspecialchars($year); ?>م</h3>
                    
                    <?php foreach ($semesters as $semester => $grades): ?>
                        <div class="semester-block">
                            <h4 class="semester-title">الفصل الدراسي: <?php echo htmlspecialchars($semester); ?></h4>
                            
                            <div class="table-responsive">
                                <table class="grades-table">
                                    <thead>
                                        <tr>
                                            <th>م</th>
                                            <th class="course-cell">اسم المادة</th>
                                            <th>الفصل</th>
                                            <th>الامتحان</th>
                                            <th>الإجمالي</th>
                                            <th>النقاط</th>
                                            <th>التقدير</th>
                                            <th class="col-note">ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $counter = 1;
                                        foreach ($grades as $row): 
                                        ?>
                                            <tr>
                                                <td class="col-id"><?php echo $counter++; ?></td>
                                                <td class="course-cell">
                                                    <div class="course-ar"><?php echo htmlspecialchars($row['course_ar']); ?></div>
                                                    <div class="course-en"><?php echo htmlspecialchars($row['course_en']); ?></div>
                                                </td>
                                                <td class="score-val"><?php echo htmlspecialchars($row['gsemster']); ?></td>
                                                <td class="score-val"><?php echo htmlspecialchars($row['gexam']); ?></td>
                                                <td class="score-val"><?php echo htmlspecialchars($row['gfinal']); ?></td>
                                                <td class="score-val"><?php echo htmlspecialchars($row['gpa']); ?></td>
                                                <td><span class="grade-text <?php echo ($row['result'] == 1) ? 'pass' : 'fail'; ?>"><?php echo htmlspecialchars($row['letter_grade']); ?></span></td>
                                                <td class="col-note-content"><?php echo htmlspecialchars($row['note']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="no-data">
                <p>لا توجد درجات مسجلة .</p>
            </div>
        <?php endif; ?>

    </main>

   

    <script src="../assets/js/script.js"></script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
ء