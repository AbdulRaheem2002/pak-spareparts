<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice - <?= e($order['order_number']) ?> | PakAutoParts</title>
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">
  <style>
    @media print {
      body { margin: 0; padding: 0; }
      .a4-container { width: 100% !important; max-width: 100% !important; padding: 0 !important; box-shadow: none !important; }
    }
  </style>
</head>
<body class="bg-slate-100 py-10 antialiased text-slate-800">

  <div class="max-w-4xl mx-auto bg-white p-10 rounded-2xl shadow-lg border border-slate-200 a4-container space-y-8">
    
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-6">
      <div>
        <h1 class="text-3xl font-extrabold uppercase font-condensed tracking-wider text-slate-900">
          PAK<span class="text-brand-500">AUTO</span>PARTS
        </h1>
        <p class="text-xs text-slate-500 font-semibold tracking-wide">GENUINE &amp; OEM AUTO PARTS NETWORK</p>
        <div class="text-xs text-slate-600 mt-2">
          <?= e(setting('contact_address', 'Montgomery Road Auto Market, Lahore, Pakistan')) ?><br>
          Phone: <?= e(setting('contact_phone', '0300-1234567')) ?> | NTN: 8923412-7
        </div>
      </div>

      <div class="text-right">
        <div class="text-xl font-bold uppercase font-condensed text-brand-600">TAX INVOICE</div>
        <div class="text-base font-mono font-bold text-slate-900 mt-1"><?= e($order['order_number']) ?></div>
        <div class="text-xs text-slate-500 mt-0.5">Date: <?= date('d M Y', strtotime($order['created_at'])) ?></div>
      </div>
    </div>

    <!-- Bill To & Courier Destination -->
    <div class="grid grid-cols-2 gap-8 text-xs">
      <div>
        <h3 class="font-bold text-slate-400 uppercase font-condensed mb-1">INVOICED TO:</h3>
        <div class="font-bold text-slate-900 text-sm"><?= e($order['customer_name']) ?></div>
        <div class="text-slate-700 leading-relaxed"><?= e($order['delivery_address']) ?></div>
        <div class="text-slate-900 font-bold"><?= e($order['delivery_city']) ?>, Pakistan</div>
        <div class="text-slate-600 font-mono mt-1">Phone: <?= e($order['customer_phone']) ?></div>
      </div>

      <div class="text-right">
        <h3 class="font-bold text-slate-400 uppercase font-condensed mb-1">PAYMENT &amp; LOGISTICS:</h3>
        <div>Payment Mode: <strong class="uppercase font-mono"><?= e($order['payment_method']) ?></strong></div>
        <div>Payment Status: <strong class="uppercase text-emerald-600"><?= e($order['payment_status']) ?></strong></div>
        <div class="mt-2">Courier: <strong><?= e($order['courier_name'] ?: 'TCS Express') ?></strong></div>
        <div>Tracking #: <strong class="font-mono text-brand-600"><?= e($order['tracking_number'] ?: 'Awaiting') ?></strong></div>
      </div>
    </div>

    <!-- Items Table -->
    <div class="border border-slate-200 rounded-lg overflow-hidden text-xs">
      <table class="w-full text-left">
        <thead class="bg-slate-50 border-b border-slate-200 uppercase font-condensed font-bold text-slate-700">
          <tr>
            <th class="py-2.5 px-4">#</th>
            <th class="py-2.5 px-4">Part Description</th>
            <th class="py-2.5 px-4">OEM Part Number</th>
            <th class="py-2.5 px-4 text-center">Qty</th>
            <th class="py-2.5 px-4 text-right">Unit Price</th>
            <th class="py-2.5 px-4 text-right">Subtotal</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($items as $idx => $it): ?>
            <tr>
              <td class="py-2.5 px-4 text-slate-400"><?= $idx + 1 ?></td>
              <td class="py-2.5 px-4 font-bold text-slate-900"><?= e($it['product_name']) ?></td>
              <td class="py-2.5 px-4 font-mono text-slate-600"><?= e($it['oem_number']) ?></td>
              <td class="py-2.5 px-4 text-center font-bold font-mono"><?= (int)$it['quantity'] ?></td>
              <td class="py-2.5 px-4 text-right text-slate-700"><?= format_pkr($it['unit_price']) ?></td>
              <td class="py-2.5 px-4 text-right font-bold text-slate-900"><?= format_pkr($it['subtotal']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot class="bg-slate-50 border-t border-slate-200 text-xs">
          <tr>
            <td colspan="5" class="py-2 px-4 text-right font-semibold text-slate-600">Subtotal:</td>
            <td class="py-2 px-4 text-right font-bold text-slate-900"><?= format_pkr($order['subtotal']) ?></td>
          </tr>
          <?php if ((float)$order['discount_amount'] > 0): ?>
            <tr>
              <td colspan="5" class="py-1 px-4 text-right text-emerald-600 font-semibold">Discount:</td>
              <td class="py-1 px-4 text-right text-emerald-600 font-bold">-<?= format_pkr($order['discount_amount']) ?></td>
            </tr>
          <?php endif; ?>
          <tr>
            <td colspan="5" class="py-1 px-4 text-right text-slate-600">Shipping Delivery Fee:</td>
            <td class="py-1 px-4 text-right font-semibold"><?= format_pkr($order['shipping_fee']) ?></td>
          </tr>
          <?php if ((float)$order['cod_fee'] > 0): ?>
            <tr>
              <td colspan="5" class="py-1 px-4 text-right text-slate-600">Cash on Delivery Surcharge:</td>
              <td class="py-1 px-4 text-right font-semibold"><?= format_pkr($order['cod_fee']) ?></td>
            </tr>
          <?php endif; ?>
          <tr class="text-sm font-extrabold text-slate-900 border-t border-slate-200 font-condensed">
            <td colspan="5" class="py-3 px-4 text-right uppercase">Total Amount (PKR):</td>
            <td class="py-3 px-4 text-right text-brand-600"><?= format_pkr($order['total']) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Terms & Return Policy -->
    <div class="border-t border-slate-200 pt-6 text-[11px] text-slate-500 space-y-1">
      <div class="font-bold text-slate-700 uppercase font-condensed">Warranty &amp; Return Terms:</div>
      <p>1. Parts are guaranteed 100% genuine &amp; OEM. Inspect goods upon courier delivery.</p>
      <p>2. 7-day hassle-free return/exchange applies provided part is uninstalled and in original packaging.</p>
      <p>3. For technical fitment queries, message our support desk at <?= e(setting('contact_phone', '0300-1234567')) ?>.</p>
    </div>

    <div class="flex justify-between items-center text-xs text-slate-400 pt-4 border-t border-slate-100 no-print">
      <span>This is a computer-generated tax invoice.</span>
      <button type="button" onclick="window.print()" class="btn-primary py-2 px-5 text-xs">
        Print Invoice
      </button>
    </div>

  </div>

</body>
</html>

