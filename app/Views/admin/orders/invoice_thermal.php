<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Receipt - <?= e($order['order_number']) ?></title>
  <style>
    @page { margin: 0; size: 80mm auto; }
    body {
      font-family: 'Courier New', Courier, monospace;
      font-size: 11px;
      line-height: 1.25;
      width: 74mm;
      margin: 0 auto;
      padding: 3mm;
      color: #000;
      background: #fff;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    .border-b { border-bottom: 1px dashed #000; padding-bottom: 3px; margin-bottom: 4px; }
    .border-t { border-top: 1px dashed #000; padding-top: 3px; margin-top: 4px; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th, td { padding: 2px 0; }
    @media print {
      .no-print { display: none; }
    }
  </style>
</head>
<body onload="window.print()">

  <div class="text-center border-b">
    <div class="font-bold" style="font-size: 14px;">PAK AUTO PARTS</div>
    <div>Auto Market, Montgomery Rd, Lahore</div>
    <div>Tel: <?= e(setting('contact_phone', '0300-1234567')) ?></div>
  </div>

  <div class="border-b" style="font-size: 10px;">
    <div><strong>Order:</strong> <?= e($order['order_number']) ?></div>
    <div><strong>Date:</strong> <?= date('d/m/Y h:i A', strtotime($order['created_at'])) ?></div>
    <div><strong>Customer:</strong> <?= e($order['customer_name']) ?></div>
    <div><strong>Phone:</strong> <?= e($order['customer_phone']) ?></div>
    <div><strong>City:</strong> <?= e($order['delivery_city']) ?></div>
    <div><strong>Courier:</strong> <?= e($order['courier_name'] ?: 'TCS Express') ?></div>
    <?php if ($order['tracking_number']): ?>
      <div><strong>Track #:</strong> <?= e($order['tracking_number']) ?></div>
    <?php endif; ?>
  </div>

  <table class="border-b">
    <thead>
      <tr>
        <th style="text-align: left;">Part</th>
        <th style="text-align: center;">Qty</th>
        <th style="text-align: right;">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $it): ?>
        <tr>
          <td>
            <div class="font-bold"><?= substr(e($it['product_name']), 0, 22) ?></div>
            <div style="font-size: 8px;">OEM: <?= e($it['oem_number']) ?></div>
          </td>
          <td style="text-align: center;"><?= (int)$it['quantity'] ?></td>
          <td style="text-align: right;"><?= number_format((float)$it['subtotal'], 0) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div style="font-size: 10px;">
    <div style="display: flex; justify-content: space-between;">
      <span>Subtotal:</span>
      <span>Rs. <?= number_format((float)$order['subtotal'], 0) ?></span>
    </div>
    <?php if ((float)$order['discount_amount'] > 0): ?>
      <div style="display: flex; justify-content: space-between;">
        <span>Discount:</span>
        <span>-Rs. <?= number_format((float)$order['discount_amount'], 0) ?></span>
      </div>
    <?php endif; ?>
    <div style="display: flex; justify-content: space-between;">
      <span>Delivery:</span>
      <span>Rs. <?= number_format((float)$order['shipping_fee'], 0) ?></span>
    </div>
    <?php if ((float)$order['cod_fee'] > 0): ?>
      <div style="display: flex; justify-content: space-between;">
        <span>COD Surcharge:</span>
        <span>Rs. <?= number_format((float)$order['cod_fee'], 0) ?></span>
      </div>
    <?php endif; ?>
    <div class="border-t font-bold" style="display: flex; justify-content: space-between; font-size: 12px; margin-top: 3px;">
      <span>TOTAL DUE:</span>
      <span>Rs. <?= number_format((float)$order['total'], 0) ?></span>
    </div>
  </div>

  <div class="text-center border-t" style="margin-top: 8px; font-size: 9px;">
    <div><strong>PAYMENT: <?= strtoupper($order['payment_method']) ?></strong></div>
    <div>7-Day Return &amp; Exchange Warranty</div>
    <div>Thank you for choosing PakAutoParts!</div>
  </div>

  <div class="no-print" style="margin-top: 15px; text-align: center;">
    <button onclick="window.print()" style="padding: 5px 15px; font-size: 12px; cursor: pointer;">Print Thermal</button>
  </div>

</body>
</html>

