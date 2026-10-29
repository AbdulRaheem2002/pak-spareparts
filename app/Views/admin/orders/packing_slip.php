<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Packing Slip - <?= e($order['order_number']) ?></title>
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
</head>
<body class="bg-white p-8 antialiased text-slate-900" onload="window.print()">

  <div class="max-w-3xl mx-auto border-2 border-slate-900 p-8 space-y-6">
    <div class="flex justify-between items-center border-b-2 border-slate-900 pb-4">
      <div>
        <h1 class="text-2xl font-black uppercase font-condensed tracking-wider">WAREHOUSE PACKING SLIP</h1>
        <p class="text-xs font-mono">PakAutoParts Logistics Fulfillment</p>
      </div>
      <div class="text-right">
        <div class="text-xl font-bold font-mono"><?= e($order['order_number']) ?></div>
        <div class="text-xs"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
      </div>
    </div>

    <!-- Ship To Address Box -->
    <div class="border border-slate-900 p-4 rounded-lg bg-slate-50 text-xs space-y-1">
      <div class="font-black uppercase tracking-wider text-slate-500 font-condensed">SHIP TO / RECIPIENT:</div>
      <div class="text-sm font-bold"><?= e($order['customer_name']) ?></div>
      <div class="text-xs"><?= e($order['delivery_address']) ?></div>
      <div class="text-sm font-bold uppercase"><?= e($order['delivery_city']) ?></div>
      <div class="font-mono font-bold mt-1">TEL: <?= e($order['customer_phone']) ?></div>
    </div>

    <!-- Picking Checklist -->
    <div>
      <h3 class="text-xs font-black uppercase font-condensed tracking-wider mb-2">PARCEL CONTENTS (CHECKLIST)</h3>
      <table class="w-full text-left text-xs border border-slate-900">
        <thead class="bg-slate-200 border-b border-slate-900 font-bold uppercase">
          <tr>
            <th class="p-2 border-r border-slate-900 w-12 text-center">CHECK</th>
            <th class="p-2 border-r border-slate-900">ITEM DESCRIPTION</th>
            <th class="p-2 border-r border-slate-900">SKU / OEM #</th>
            <th class="p-2 text-center w-16">QTY</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-300">
          <?php foreach ($items as $it): ?>
            <tr>
              <td class="p-2 border-r border-slate-900 text-center text-lg font-bold">[ &nbsp; ]</td>
              <td class="p-2 border-r border-slate-900 font-bold"><?= e($it['product_name']) ?></td>
              <td class="p-2 border-r border-slate-900 font-mono"><?= e($it['sku']) ?> / <?= e($it['oem_number']) ?></td>
              <td class="p-2 text-center font-bold text-sm font-mono"><?= (int)$it['quantity'] ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Signatures -->
    <div class="grid grid-cols-2 gap-8 pt-8 text-xs border-t border-slate-300">
      <div>
        <div class="border-b border-slate-400 h-8"></div>
        <div class="mt-1 text-slate-500 font-bold uppercase font-condensed">Picker / Packer Signature</div>
      </div>
      <div>
        <div class="border-b border-slate-400 h-8"></div>
        <div class="mt-1 text-slate-500 font-bold uppercase font-condensed">Courier Handover Rider Signature</div>
      </div>
    </div>
  </div>

</body>
</html>

