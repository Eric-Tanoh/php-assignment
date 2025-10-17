<?php
// Purpose: Employee Salary Slip Generator
function clean($data) {
    return htmlspecialchars(trim($data));
}

$errors = [];
$employee_name = "";
$employee_id = "";
$basic_salary = "";
$transport = "";
$housing = "";
$medical = "";
$tax = "";
$pension = "";
$net_salary = null;
$gross_salary = null;
$total_deductions = null;
$conditional_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employee_name = clean($_POST['employee_name'] ?? "");
    $employee_id   = clean($_POST['employee_id'] ?? "");
    $basic_salary  = clean($_POST['basic_salary'] ?? "");
    $transport     = clean($_POST['transport'] ?? "");
    $housing       = clean($_POST['housing'] ?? "");
    $medical       = clean($_POST['medical'] ?? "");
    $tax           = clean($_POST['tax'] ?? "");
    $pension       = clean($_POST['pension'] ?? "");

    if ($employee_name === "")  $errors[] = "Employee Name is required.";
    if ($employee_id === "")    $errors[] = "Employee ID is required.";
    if ($basic_salary === "")   $errors[] = "Basic Salary is required.";
    if ($transport === "")      $errors[] = "Transport allowance is required.";
    if ($housing === "")        $errors[] = "Housing allowance is required.";
    if ($medical === "")        $errors[] = "Medical allowance is required.";
    if ($tax === "")            $errors[] = "Tax deduction is required.";
    if ($pension === "")        $errors[] = "Pension deduction is required.";

    $numeric_fields = [
        'Basic Salary' => $basic_salary,
        'Transport'    => $transport,
        'Housing'      => $housing,
        'Medical'      => $medical,
        'Tax'          => $tax,
        'Pension'      => $pension
    ];
    foreach ($numeric_fields as $label => $val) {
        if ($val !== "" && !is_numeric($val)) {
            $errors[] = "$label must be a number.";
        } elseif (is_numeric($val) && floatval($val) < 0) {
            $errors[] = "$label cannot be negative.";
        }
    }

    if (empty($errors)) {
        $basic = floatval($basic_salary);
        $trans = floatval($transport);
        $house = floatval($housing);
        $med   = floatval($medical);
        $tax_v = floatval($tax);
        $pen   = floatval($pension);

        $gross_salary = $basic + $trans + $house + $med;
        $total_deductions = $tax_v + $pen;
        $net_salary = $gross_salary - $total_deductions;

        if ($net_salary > 5000) {
            $conditional_message = "🎉 Eligible for Bonus";
        } elseif ($net_salary < 2000) {
            $conditional_message = "⚠️ Needs Review";
        } else {
            $conditional_message = "✅ Standard Payment";
        }

        $record_line = "Employee ID: " . $employee_id
                     . " | Name: " . $employee_name
                     . " | Net Salary: GHS " . number_format($net_salary, 2)
                     . " | Date: " . date("Y-m-d H:i:s") . PHP_EOL;

        $file_path = __DIR__ . DIRECTORY_SEPARATOR . "salary_records.txt";
        $fp = fopen($file_path, 'a');
        if ($fp) {
            fwrite($fp, $record_line);
            fclose($fp);
        } else {
            $errors[] = "Could not open salary_records.txt for writing. Check folder permissions.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Employee Salary Slip Generator</title>
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
      margin-bottom: 20px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px 25px;
    }

    label {
      font-weight: 600;
      color: #444;
    }

    input[type="text"], input[type="number"] {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      transition: all 0.2s ease;
    }

    input:focus {
      border-color: #1cc88a;
      outline: none;
      box-shadow: 0 0 6px rgba(28,200,138,0.4);
    }

    .btn {
      grid-column: span 2;
      padding: 12px 20px;
      background: #1cc88a;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btn:hover {
      background: #17a673;
    }

    .errors {
      background: #ffe6e6;
      border-left: 5px solid #e74a3b;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 25px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    th {
      background: #f8f9fc;
      color: #333;
    }

    td strong {
      color: #4e73df;
    }

    .note {
      margin-top: 12px;
      background: #eafaf1;
      padding: 12px;
      border-left: 5px solid #1cc88a;
      border-radius: 8px;
      font-weight: 500;
      color: #155724;
    }

    .link {
      display: inline-block;
      margin-top: 10px;
      text-decoration: none;
      color: #4e73df;
      font-weight: 600;
    }

    .link:hover {
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Employee Salary Slip Generator</h1>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <strong>⚠️ Please fix the following:</strong>
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?php echo $err; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <label for="employee_name">Employee Name</label>
      <input type="text" id="employee_name" name="employee_name" value="<?php echo $employee_name; ?>">

      <label for="employee_id">Employee ID</label>
      <input type="text" id="employee_id" name="employee_id" value="<?php echo $employee_id; ?>">

      <label for="basic_salary">Basic Salary</label>
      <input type="text" id="basic_salary" name="basic_salary" value="<?php echo $basic_salary; ?>">

      <label for="transport">Transport Allowance</label>
      <input type="text" id="transport" name="transport" value="<?php echo $transport; ?>">

      <label for="housing">Housing Allowance</label>
      <input type="text" id="housing" name="housing" value="<?php echo $housing; ?>">

      <label for="medical">Medical Allowance</label>
      <input type="text" id="medical" name="medical" value="<?php echo $medical; ?>">

      <label for="tax">Tax Deduction</label>
      <input type="text" id="tax" name="tax" value="<?php echo $tax; ?>">

      <label for="pension">Pension Deduction</label>
      <input type="text" id="pension" name="pension" value="<?php echo $pension; ?>">

      <button type="submit" class="btn">Generate Salary Slip</button>
    </form>

    <?php if ($net_salary !== null && empty($errors)): ?>
      <h2 style="margin-top:30px;color:#333;">Salary Slip</h2>
      <table>
        <tr><th>Employee Name</th><td><?php echo htmlspecialchars($employee_name); ?></td></tr>
        <tr><th>Employee ID</th><td><?php echo htmlspecialchars($employee_id); ?></td></tr>
        <tr><th>Basic Salary</th><td>GHS <?php echo number_format($basic, 2); ?></td></tr>
        <tr><th>Transport</th><td>GHS <?php echo number_format($trans, 2); ?></td></tr>
        <tr><th>Housing</th><td>GHS <?php echo number_format($house, 2); ?></td></tr>
        <tr><th>Medical</th><td>GHS <?php echo number_format($med, 2); ?></td></tr>
        <tr><th>Gross Salary</th><td><strong>GHS <?php echo number_format($gross_salary, 2); ?></strong></td></tr>
        <tr><th>Total Deductions</th><td>GHS <?php echo number_format($total_deductions, 2); ?></td></tr>
        <tr><th>Net Salary</th><td><strong>GHS <?php echo number_format($net_salary, 2); ?></strong></td></tr>
      </table>

      <div class="note"><?php echo $conditional_message; ?></div>
      <a href="display_records.php" class="link">📁 View All Saved Salary Records</a>
    <?php endif; ?>
  </div>
</body>
</html>
