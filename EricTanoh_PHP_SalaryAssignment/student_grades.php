<?php
//Purpose: Student Result Processor
function clean($data) {
    return htmlspecialchars(trim($data));
}

$errors = [];
$name = "";
$course = "";
$test1 = "";
$test2 = "";
$test3 = "";
$average = null;
$grade = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = clean($_POST['name'] ?? "");
    $course = clean($_POST['course'] ?? "");
    $test1 = clean($_POST['test1'] ?? "");
    $test2 = clean($_POST['test2'] ?? "");
    $test3 = clean($_POST['test3'] ?? "");

    if ($name === "") $errors[] = "Student name is required.";
    if ($course === "") $errors[] = "Course code is required.";
    if ($test1 === "") $errors[] = "Test 1 score is required.";
    if ($test2 === "") $errors[] = "Test 2 score is required.";
    if ($test3 === "") $errors[] = "Test 3 score is required.";

    // Numeric validation
    foreach (['Test 1' => $test1, 'Test 2' => $test2, 'Test 3' => $test3] as $label => $val) {
        if ($val !== "" && !is_numeric($val)) $errors[] = "$label must be a number.";
        elseif (is_numeric($val) && ($val < 0 || $val > 100)) $errors[] = "$label must be between 0 and 100.";
    }

    if (empty($errors)) {
        $t1 = floatval($test1);
        $t2 = floatval($test2);
        $t3 = floatval($test3);
        $average = round(($t1 + $t2 + $t3) / 3, 2);

        // Determine grade
        if ($average >= 80) $grade = "A";
        elseif ($average >= 70) $grade = "B";
        elseif ($average >= 60) $grade = "C";
        elseif ($average >= 50) $grade = "D";
        else $grade = "F";

        $message = match ($grade) {
            "A" => "🌟 Excellent Performance!",
            "B" => "👏 Very Good!",
            "C" => "🙂 Good Effort.",
            "D" => "⚠️ Needs Improvement.",
            "F" => "❌ Failed — Study Harder Next Time.",
            default => ""
        };

        // Save record to grades.txt
        $record = "Name: $name | Course: $course | Average: $average | Grade: $grade | Date: " . date("Y-m-d H:i:s") . PHP_EOL;
        file_put_contents(__DIR__ . "/grades.txt", $record, FILE_APPEND);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Result Processor</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body {
      font-family:'Segoe UI',Arial,sans-serif;
      background:linear-gradient(135deg,#4e73df,#1cc88a);
      min-height:100vh;display:flex;align-items:center;justify-content:center;margin:0;
    }
    .container {
      background:#fff;width:95%;max-width:850px;
      padding:30px 40px;border-radius:16px;
      box-shadow:0 8px 25px rgba(0,0,0,0.15);
    }
    h1{text-align:center;color:#333;margin-bottom:25px;text-transform:uppercase;}
    form{display:grid;grid-template-columns:1fr 1fr;gap:15px 25px;}
    label{font-weight:600;color:#444;}
    input[type="text"],input[type="number"]{
      width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;
    }
    input:focus{border-color:#1cc88a;outline:none;box-shadow:0 0 6px rgba(28,200,138,0.4);}
    .btn{
      grid-column:span 2;padding:12px 20px;background:#1cc88a;color:#fff;
      border:none;border-radius:8px;font-size:16px;cursor:pointer;transition:0.3s ease;
    }
    .btn:hover{background:#17a673;}
    .errors{background:#ffe6e6;border-left:5px solid #e74a3b;padding:15px;
      border-radius:8px;margin-bottom:15px;}
    table{border-collapse:collapse;width:100%;margin-top:25px;}
    th,td{border:1px solid #ddd;padding:10px;text-align:left;}
    th{background:#f8f9fc;}
    .note{margin-top:12px;background:#eafaf1;padding:12px;border-left:5px solid #1cc88a;
      border-radius:8px;font-weight:500;color:#155724;}
    .link{text-decoration:none;color:#4e73df;font-weight:600;}
    .link:hover{text-decoration:none;}
  </style>
</head>
<body>
  <div class="container">
    <h1>PHP Student Result Processor</h1>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <strong>⚠️ Please fix the following:</strong>
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?php echo $e; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <label>Student Name</label>
      <input type="text" name="name" value="<?php echo $name; ?>">

      <label>Course Code</label>
      <input type="text" name="course" value="<?php echo $course; ?>">

      <label>Test 1 Score</label>
      <input type="number" name="test1" min="0" max="100" value="<?php echo $test1; ?>">

      <label>Test 2 Score</label>
      <input type="number" name="test2" min="0" max="100" value="<?php echo $test2; ?>">

      <label>Test 3 Score</label>
      <input type="number" name="test3" min="0" max="100" value="<?php echo $test3; ?>">

      <button type="submit" class="btn">Process Result</button>
    </form>

    <?php if ($average !== null && empty($errors)): ?>
      <h2 style="margin-top:30px;color:#333;">Result Summary</h2>
      <table>
        <tr><th>Student Name</th><td><?php echo $name; ?></td></tr>
        <tr><th>Course Code</th><td><?php echo $course; ?></td></tr>
        <tr><th>Average</th><td><?php echo $average; ?></td></tr>
        <tr><th>Grade</th><td><strong><?php echo $grade; ?></strong></td></tr>
      </table>
      <div class="note"><?php echo $message; ?></div>
      <p><a href="view_grades.php" class="link">📊 View All Class Results</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
