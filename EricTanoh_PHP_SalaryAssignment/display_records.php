<?php
/*
Purpose:       Read and display entries from salary_records.txt
*/

$file_path = __DIR__ . DIRECTORY_SEPARATOR . "salary_records.txt";
$lines = [];

if (file_exists($file_path)) {
    $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Saved Salary Records</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, #4e73df, #1cc88a);
      min-height: 100vh;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .container {
      background: #fff;
      width: 95%;
      max-width: 850px;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      margin: 40px auto;
    }

    h1 {
      text-align: center;
      color: #333;
      font-size: 28px;
      margin-bottom: 25px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 10px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px 12px;
      text-align: left;
    }

    th {
      background: #f8f9fc;
      color: #333;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    .empty {
      background: #fff3cd;
      border-left: 5px solid #ffeeba;
      padding: 15px;
      border-radius: 8px;
      color: #856404;
      font-weight: 500;
    }

    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 18px;
      background: #1cc88a;
      color: #fff;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: 0.3s ease;
    }

    .btn:hover {
      background: #17a673;
    }

    .footer-note {
      text-align: center;
      font-size: 0.9em;
      color: #555;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Saved Salary Records</h1>

    <?php if (empty($lines)): ?>
      <div class="empty">
        ⚠️ No salary records found. Please generate at least one salary slip first.
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Record</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lines as $i => $line): ?>
            <tr>
              <td><?php echo $i + 1; ?></td>
              <td><?php echo htmlspecialchars($line); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <a class="btn" href="salary.php">← Back to Salary Form</a>

    <p class="footer-note">
      © <?php echo date("Y"); ?> LYOIDEV Employee Salary Slip Generator | All Rights Reserved
    </p>
  </div>
</body>
</html>
