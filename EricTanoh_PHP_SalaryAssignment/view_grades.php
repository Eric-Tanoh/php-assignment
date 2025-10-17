<?php
//Purpose: Display all grades and class statistics
$file_path = __DIR__ . "/grades.txt";
$lines = file_exists($file_path) ? file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

$averages = [];
foreach ($lines as $line) {
    if (preg_match('/Average:\s*([0-9.]+)/', $line, $matches)) {
        $averages[] = floatval($matches[1]);
    }
}

$highest = $averages ? max($averages) : 0;
$lowest  = $averages ? min($averages) : 0;
$class_avg = $averages ? round(array_sum($averages) / count($averages), 2) : 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Class Results</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:linear-gradient(135deg,#4e73df,#1cc88a);
      min-height:100vh;margin:0;display:flex;align-items:center;justify-content:center;}
    .container{background:#fff;width:95%;max-width:850px;padding:30px 40px;border-radius:16px;
      box-shadow:0 8px 25px rgba(0,0,0,0.15);}
    h1{text-align:center;color:#333;margin-bottom:25px;text-transform:uppercase;}
    table{border-collapse:collapse;width:100%;margin-top:10px;}
    th,td{border:1px solid #ddd;padding:10px;text-align:left;}
    th{background:#f8f9fc;}
    tr:nth-child(even){background:#f9f9f9;}
    .btn{display:inline-block;margin-top:20px;padding:10px 18px;background:#1cc88a;color:#fff;
      text-decoration:none;border-radius:8px;font-weight:600;transition:0.3s ease;}
    .btn:hover{background:#17a673;}
    .stats{margin-top:20px;background:#eafaf1;padding:12px;border-left:5px solid #1cc88a;
      border-radius:8px;font-weight:500;color:#155724;}
    .empty{background:#fff3cd;border-left:5px solid #ffeeba;padding:15px;border-radius:8px;
      color:#856404;font-weight:500;}
    .footer-note { text-align: center; font-size: 0.9em; color: #555; margin-top: 20px; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Class Results</h1>

    <?php if (empty($lines)): ?>
      <div class="empty">⚠️ No student records found. Please submit at least one result first.</div>
    <?php else: ?>
      <table>
        <thead><tr><th>#</th><th>Student Record</th></tr></thead>
        <tbody>
          <?php foreach ($lines as $i => $line): ?>
            <tr><td><?php echo $i + 1; ?></td><td><?php echo htmlspecialchars($line); ?></td></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="stats">
        📈 <strong>Class Statistics:</strong><br>
        Highest Average: <?php echo $highest; ?><br>
        Lowest Average: <?php echo $lowest; ?><br>
        Class Average: <?php echo $class_avg; ?>
      </div>
    <?php endif; ?>
    <a href="student_grades.php" class="btn">← Back to Grade Form</a>

    <p class="footer-note">
      © <?php echo date("Y"); ?> LYOIDEV Student Grade Analyzer | All Rights Reserved
    </p>
  </div>
</body>
</html>
