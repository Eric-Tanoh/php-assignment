<?php
//Purpose:       PHP Shopping Cart Price Calculator
function clean($data) {
    return htmlspecialchars(trim($data));
}

$errors = [];
$product = "";
$quantity = "";
$unit_price = "";
$subtotal = $vat = $discount = $total = null;
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product = clean($_POST['product'] ?? "");
    $quantity = clean($_POST['quantity'] ?? "");
    $unit_price = clean($_POST['unit_price'] ?? "");

    if ($product === "")   $errors[] = "Product name is required.";
    if ($quantity === "")  $errors[] = "Quantity is required.";
    if ($unit_price === "")$errors[] = "Unit price is required.";

    if ($quantity !== "" && !is_numeric($quantity)) $errors[] = "Quantity must be numeric.";
    if ($unit_price !== "" && !is_numeric($unit_price)) $errors[] = "Unit price must be numeric.";

    if (empty($errors)) {
        $q = floatval($quantity);
        $u = floatval($unit_price);

        $subtotal = $q * $u;
        $vat = 0.15 * $subtotal;

        // Apply 10% discount if quantity > 10
        $discount = ($q > 10) ? 0.10 * $subtotal : 0;
        $total = ($subtotal - $discount) + $vat;

        $message = ($discount > 0)
            ? "🎉 You received a 10% discount!"
            : "✅ No discount applied.";

        // Save record to orders.txt
        $record = "Product: $product | Qty: $q | Unit Price: GHS $u | Total: GHS "
                . number_format($total, 2) . " | Date: " . date("Y-m-d H:i:s") . PHP_EOL;

        $file_path = __DIR__ . DIRECTORY_SEPARATOR . "orders.txt";
        file_put_contents($file_path, $record, FILE_APPEND);
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PHP Shopping Cart Price Calculator</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, #4e73df, #1cc88a);
      min-height: 100vh; margin: 0;
      display: flex; align-items: center; justify-content: center;
    }
    .container {
      background: #fff;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      width: 95%; max-width: 850px;
    }
    h1 { text-align: center; color:#333; margin-bottom:25px; text-transform:uppercase; }
    form { display:grid; grid-template-columns:1fr 1fr; gap:15px 25px; }
    label { font-weight:600; color:#444; }
    input[type="text"], input[type="number"] {
      width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;
    }
    input:focus { border-color:#1cc88a; outline:none; box-shadow:0 0 6px rgba(28,200,138,0.4); }
    .btn {
      grid-column: span 2;
      padding: 12px 20px;
      background: #1cc88a; color: #fff;
      border: none; border-radius: 8px;
      font-size: 16px; cursor: pointer;
      transition: 0.3s ease;
    }
    .btn:hover { background:#17a673; }
    .errors {
      background:#ffe6e6; border-left:5px solid #e74a3b;
      padding:15px; border-radius:8px; margin-bottom:15px;
    }
    table { border-collapse:collapse; width:100%; margin-top:25px; }
    th,td { border:1px solid #ddd; padding:10px; text-align:left; }
    th { background:#f8f9fc; }
    .note {
      margin-top:12px; background:#eafaf1;
      padding:12px; border-left:5px solid #1cc88a;
      border-radius:8px; font-weight:500; color:#155724;
    }
    .link { text-decoration:none; color:#4e73df; font-weight:600; }
    .link:hover { text-decoration:none; }
  </style>
</head>
<body>
  <div class="container">
    <h1>PHP Shopping Cart Price Calculator</h1>

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
      <label>Product Name</label>
      <input type="text" name="product" value="<?php echo $product; ?>">

      <label>Quantity</label>
      <input type="number" name="quantity" min="1" value="<?php echo $quantity; ?>">

      <label>Unit Price (GHS)</label>
      <input type="text" name="unit_price" value="<?php echo $unit_price; ?>">

      <button type="submit" class="btn">Calculate Total</button>
    </form>

    <?php if ($total !== null && empty($errors)): ?>
      <h2 style="margin-top:30px;color:#333;">Order Summary</h2>
      <table>
        <tr><th>Product</th><td><?php echo $product; ?></td></tr>
        <tr><th>Quantity</th><td><?php echo $quantity; ?></td></tr>
        <tr><th>Unit Price</th><td>GHS <?php echo number_format($unit_price,2); ?></td></tr>
        <tr><th>Subtotal</th><td>GHS <?php echo number_format($subtotal,2); ?></td></tr>
        <tr><th>Discount (10% if >10)</th><td>GHS <?php echo number_format($discount,2); ?></td></tr>
        <tr><th>VAT (15%)</th><td>GHS <?php echo number_format($vat,2); ?></td></tr>
        <tr><th><strong>Total</strong></th><td><strong>GHS <?php echo number_format($total,2); ?></strong></td></tr>
      </table>
      <div class="note"><?php echo $message; ?></div>
      <p><a href="view_orders.php" class="link">📄 View All Orders</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
