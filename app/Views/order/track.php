<!-- Order Tracking View -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
    <?php foreach ($breadcrumbs as $idx => $bc): ?>
      <?php if ($idx > 0): ?>
        <span>/</span>
      <?php endif; ?>
      <?php if (!empty($bc['url'])): ?>
        <a href="<?= e($bc['url']) ?>" class="hover:text-brand-600 transition-colors"><?= e($bc['title']) ?></a>
      <?php else: ?>
        <span class="text-slate-900 font-bold"><?= e($bc['title']) ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>

  <div class="text-center max-w-xl mx-auto mb-8 space-y-2">
    <h1 class="text-2xl sm:text-3xl font-extrabold uppercase font-condensed tracking-tight text-slate-900">
      Track Your Auto Spare Parts Order
    </h1>
    <p class="text-xs text-slate-500">
      Enter your Order ID (format: SP-YYYY-XXXXXX) and registered Pakistani mobile number to check real-time courier shipment status.
    </p>
  </div>

  <!-- Search Card -->
  <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm max-w-2xl mx-auto mb-10">
    <form action="/order/track" method="POST" class="space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="form-label">Order Number *</label>
          <input type="text"
                 name="order_number"
                 value="<?= e($_GET['order_number'] ?? $searched_num ?? '') ?>"
                 required
                 placeholder="e.g. SP-2026-000001"
                 class="form-input text-xs font-mono uppercase">
        </div>

        <div>
          <label class="form-label">Customer Mobile Number *</label>
          <input type="tel"
                 name="phone"
                 value="<?= e($_GET['phone'] ?? $searched_phone ?? '') ?>"
                 required
                 placeholder="0300-1234567"
                 class="form-input text-xs">
        </div>
      </div>

      <button type="submit" class="w-full btn-primary py-2.5 text-xs shadow-md">
        Track Package Status &rarr;
      </button>
    </form>
  </div>

  <!-- Tracking Details Card (Shown if order loaded) -->
  <?php if (!empty($order)): ?>
    <?php
      $statuses = ['Pending', 'Confirmed', 'Dispatched', 'Delivered'];
      $currentStatus = $order['status'] ?? 'Pending';
      $currentIndex = array_search($currentStatus, $statuses);
      if ($currentIndex === false) $currentIndex = 0;
    ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-8">
      
      <!-- Order Header -->
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b border-slate-100 gap-3">
        <div>
          <div class="text-[11px] font-bold text-slate-400 uppercase font-condensed">Tracking Order</div>
          <div class="text-xl font-extrabold text-slate-900 font-mono"><?= e($order['order_number']) ?></div>
          <div class="text-xs text-slate-500 mt-0.5">Placed on <?= \App\Helpers\Formatter::date($order['created_at']) ?></div>
        </div>

        <div>
          <?= \App\Helpers\Formatter::orderStatusBadge($order['status']) ?>
        </div>
      </div>

      <!-- Courier Dispatch Information -->
      <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div>
          <span class="text-[10px] uppercase font-bold text-slate-400 font-condensed">Courier Partner</span>
          <div class="font-bold text-slate-900 mt-0.5"><?= e($order['courier_name'] ?: 'TCS / Leopards Courier (Processing)') ?></div>
        </div>
        <div>
          <span class="text-[10px] uppercase font-bold text-slate-400 font-condensed">Consignment / Tracking #</span>
          <div class="font-mono font-bold text-brand-600 mt-0.5"><?= e($order['tracking_number'] ?: 'Awaiting Dispatch') ?></div>
        </div>
        <div>
          <span class="text-[10px] uppercase font-bold text-slate-400 font-condensed">Delivery City</span>
          <div class="font-bold text-slate-900 mt-0.5"><?= e($order['delivery_city']) ?></div>
        </div>
      </div>

      <!-- Visual Step Progress Bar -->
      <div class="py-4">
        <div class="relative flex items-center justify-between">
          <!-- Track line -->
          <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-1 bg-slate-200 z-0"></div>
          <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-brand-500 transition-all duration-500 z-0"
               style="width: <?= ($currentIndex / (count($statuses) - 1)) * 100 ?>%;"></div>

          <?php foreach ($statuses as $idx => $st): ?>
            <?php $isReached = $idx <= $currentIndex; ?>
            <div class="relative z-10 flex flex-col items-center">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-colors <?= $isReached ? 'bg-brand-500 text-white ring-4 ring-brand-100' : 'bg-slate-200 text-slate-500' ?>">
                <?= $idx + 1 ?>
              </div>
              <span class="mt-2 text-xs font-bold uppercase font-condensed <?= $isReached ? 'text-slate-900' : 'text-slate-400' ?>">
                <?= e($st) ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Status History Timeline -->
      <?php if (!empty($order['history'])): ?>
        <div>
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-700 mb-3">Status Timeline</h3>
          <div class="space-y-3 border-l-2 border-slate-200 pl-4 text-xs">
            <?php foreach ($order['history'] as $hist): ?>
              <div class="relative">
                <span class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-brand-500"></span>
                <div class="font-bold text-slate-900"><?= e($hist['status']) ?> - <span class="text-slate-400 font-normal"><?= \App\Helpers\Formatter::date($hist['created_at']) ?></span></div>
                <p class="text-slate-600 mt-0.5"><?= e($hist['comment']) ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Order Items Summary Table -->
      <div>
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-700 mb-3">Consignment Contents</h3>
        <div class="border border-slate-200 rounded-lg overflow-hidden text-xs">
          <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-condensed">
              <tr>
                <th class="py-2 px-3">Part</th>
                <th class="py-2 px-3">OEM #</th>
                <th class="py-2 px-3 text-center">Qty</th>
                <th class="py-2 px-3 text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($order['items'] as $it): ?>
                <tr>
                  <td class="py-2.5 px-3 font-bold text-slate-800"><?= e($it['product_name']) ?></td>
                  <td class="py-2.5 px-3 font-mono text-slate-500"><?= e($it['oem_number']) ?></td>
                  <td class="py-2.5 px-3 text-center font-bold"><?= (int)$it['quantity'] ?></td>
                  <td class="py-2.5 px-3 text-right font-bold"><?= format_pkr($it['subtotal']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  <?php endif; ?>

</div>

