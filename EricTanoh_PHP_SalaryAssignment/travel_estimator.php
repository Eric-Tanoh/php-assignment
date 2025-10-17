<?php
// Purpose: Travel Cost Estimator

function clean($data) {
    return htmlspecialchars(trim($data));
}

$errors = [];
$distance = "";
$fuel_price = "";
$vehicle_type = "";
$fuel_needed = $total_cost = null;
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $distance = clean($_POST['distance'] ?? "");
    $fuel_price = clean($_POST['fuel_price'] ?? "");
    $vehicle_type = clean($_POST['vehicle_type'] ?? "");

    if ($distance === "") $errors[] = "Distance is required.";
    if ($fuel_price === "") $errors[] = "Fuel price is required.";
    if ($vehicle_type === "") $errors[] = "Please select a vehicle type.";

    if ($distance !== "" && !is_numeric($distance)) $errors[] = "Distance must be numeric.";
    if ($fuel_price !== "" && !is_numeric($fuel_price)) $errors[] = "Fuel price must be numeric.";

    if (empty($errors)) {
        $d = floatval($distance);
        $p = floatval($fuel_price);

        // Set vehicle consumption rate (km per litre)
        switch ($vehicle_type) {
            case "Car": $consumption = 12; break;
            case "Bus": $consumption = 6; break;
            case "Motorbike": $consumption = 25; break;
            default: $consumption = 10; // fallback
        }

        // Calculate fuel and total cost
        $fuel_needed = round($d / $consumption, 2);
        $total_cost = round($fuel_needed * $p, 2);

        $message = ($total_cost > 1000)
            ? "🚫 Expensive Trip — consider a cheaper option!"
            : "💰 Budget Friendly Trip — good choice!";

        // Save to travel_cost.txt
        $record = "Distance: {$d} km | Fuel Price: GHS {$p} | Vehicle: {$vehicle_type} | Total Cost: GHS {$total_cost} | Date: " . date("Y-m-d H:i:s") . PHP_EOL;
        file_put_contents(__DIR__ . "/travel_cost.txt", $record, FILE_APPEND);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Travel Cost Estimator</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body {
      font-family:'Segoe UI',Arial,sans-serif;
      background:linear-gradient(135deg,#4e73df,#1cc88a);
      min-height:100vh;margin:0;
      display:flex;align-items:center;justify-content:center;
    }
    .container {
      background:#fff;width:95%;max-width:850px;
      padding:30px 40px;border-radius:16px;
      box-shadow:0 8px 25px rgba(0,0,0,0.15);
    }
    h1{text-align:center;color:#333;margin-bottom:25px;text-transform:uppercase;}
    form{display:grid;grid-template-columns:1fr 1fr;gap:15px 25px;}
    label{font-weight:600;color:#444;}
    input,select{
      width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;
    }
    input:focus,select:focus{
      border-color:#1cc88a;outline:none;box-shadow:0 0 6px rgba(28,200,138,0.4);
    }
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
    <h1>PHP Journey Expense Calculator</h1>

    <?php if (!empty($errors)): ?>
      <div class="errors">
        <strong>⚠️ Please fix the following:</strong>
        <ul>
          <?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <label>Distance (km)</label>
      <input type="text" name="distance" value="<?php echo $distance; ?>">

      <label>Fuel Price (GHS per litre)</label>
      <input type="text" name="fuel_price" value="<?php echo $fuel_price; ?>">

      <label>Vehicle Type</label>
      <select name="vehicle_type">
        <option value="">-- Select Vehicle --</option>
        <option value="Car" <?php if($vehicle_type=="Car") echo "selected"; ?>>Car</option>
        <option value="Bus" <?php if($vehicle_type=="Bus") echo "selected"; ?>>Bus</option>
        <option value="Motorbike" <?php if($vehicle_type=="Motorbike") echo "selected"; ?>>Motorbike</option>
      </select>

      <button type="submit" class="btn">Estimate Cost</button>
    </form>

    <?php if ($total_cost !== null && empty($errors)): ?>
      <h2 style="margin-top:30px;color:#333;">Trip Summary</h2>
      <table>
        <tr><th>Distance</th><td><?php echo $distance; ?> km</td></tr>
        <tr><th>Vehicle Type</th><td><?php echo $vehicle_type; ?></td></tr>
        <tr><th>Fuel Price</th><td>GHS <?php echo number_format($fuel_price,2); ?></td></tr>
        <tr><th>Fuel Needed</th><td><?php echo $fuel_needed; ?> litres</td></tr>
        <tr><th>Total Cost</th><td><strong>GHS <?php echo number_format($total_cost,2); ?></strong></td></tr>
      </table>
      <div class="note"><?php echo $message; ?></div>
      <p><a href="view_trips.php" class="link">📘 View All Trips</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
