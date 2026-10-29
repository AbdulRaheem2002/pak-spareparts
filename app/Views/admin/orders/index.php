<!-- Admin Orders List View -->
<div class="space-y-6">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">Customer Orders</h2>
      <p class="text-xs text-slate-500">Manage fulfillment, courier tracking, and printable dispatch slips</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="/admin/orders/export" class="btn-outline text-xs py-2">
        📥 Export Orders CSV
      </a>
    </div>
  </div>

  <!-- Filters Toolbar -->
  <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
    <form action="/admin/orders" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
      <div class="sm:col-span-5">
        <input type="text"
               name="q"
               value="<?= e($q) ?>"
               placeholder="Search Order Number, Customer Name, or Phone..."
               class="form-input text-xs">
      </div>

      <div class="sm:col-span-3">
        <select name="status" class="form-input text-xs" onchange="this.form.submit()">
          <option value="">-- All Statuses --</option>
          <?php foreach (['Pending', 'Confirmed', 'Dispatched', 'Delivered', 'Cancelled', 'Returned'] as $st): ?>
            <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= $st ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="sm:col-span-3">
        <select name="payment_method" class="form-input text-xs" onchange="this.form.submit()">
          <option value="">-- All Payments --</option>
          <option value="cod" <?= $payment === 'cod' ? 'selected' : '' ?>>Cash on Delivery (COD)</option>
          <option value="bank_transfer" <?= $payment === 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
          <option value="jazzcash" <?= $payment === 'jazzcash' ? 'selected' : '' ?>>JazzCash</option>
          <option value="easypaisa" <?= $payment === 'easypaisa' ? 'selected' : '' ?>>Easypaisa</option>
          <option value="whatsapp" <?= $payment === 'whatsapp' ? 'selected' : '' ?>>WhatsApp Direct</option>
        </select>
      </div>

      <div class="sm:col-span-1">
        <button type="submit" class="w-full btn-primary text-xs py-2">Filter</button>
      </div>
    </form>
  </div>

  <!-- Orders Table -->
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Order ID</th>
            <th class="py-3 px-4">Date</th>
            <th class="py-3 px-4">Customer</th>
            <th class="py-3 px-4">City</th>
            <th class="py-3 px-4">Payment</th>
            <th class="py-3 px-4 text-right">Total (PKR)</th>
            <th class="py-3 px-4 text-center">Status</th>
            <th class="py-3 px-4 text-right">Print &amp; Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $o): ?>
              <tr class="hover:bg-slate-50">
                <td class="py-3 px-4 font-mono font-bold text-brand-600">
                  <a href="/admin/orders/show/<?= (int)$o['id'] ?>" class="hover:underline">
                    <?= e($o['order_number']) ?>
                  </a>
                </td>
                <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                  <?= \App\Helpers\Formatter::date($o['created_at'], 'd M, h:i A') ?>
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900"><?= e($o['customer_name']) ?></div>
                  <div class="font-mono text-[11px] text-slate-400"><?= e($o['customer_phone']) ?></div>
                </td>
                <td class="py-3 px-4 text-slate-700"><?= e($o['delivery_city']) ?></td>
                <td class="py-3 px-4">
                  <span class="uppercase font-mono text-[10px] font-bold bg-slate-100 text-slate-800 px-1.5 py-0.5 rounded">
                    <?= e($o['payment_method']) ?>
                  </span>
                </td>
                <td class="py-3 px-4 text-right font-bold text-slate-900 font-condensed whitespace-nowrap">
                  <?= format_pkr($o['total']) ?>
                </td>
                <td class="py-3 px-4 text-center">
                  <?= \App\Helpers\Formatter::orderStatusBadge($o['status']) ?>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="/admin/orders/show/<?= (int)$o['id'] ?>" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-bold" title="View Order">
                      View
                    </a>
                    <a href="/admin/orders/invoice/<?= (int)$o['id'] ?>" target="_blank" class="p-1 text-slate-500 hover:text-slate-900" title="A4 Invoice">
                      📄
                    </a>
                    <a href="/admin/orders/thermal/<?= (int)$o['id'] ?>" target="_blank" class="p-1 text-slate-500 hover:text-slate-900" title="80mm Thermal Slip">
                      🧾
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="py-8 text-center text-slate-400">No customer orders found matching this filter.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($lastPage > 1): ?>
      <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
        <div>Showing Page <?= $page ?> of <?= $lastPage ?> (<?= $total ?> orders total)</div>
        <div class="flex gap-1.5">
          <?php for ($i = 1; $i <= $lastPage; $i++): ?>
            <a href="?page=<?= $i ?>&status=<?= urlencode((string)$status) ?>&payment_method=<?= urlencode((string)$payment) ?>&q=<?= urlencode((string)$q) ?>"
               class="px-2.5 py-1 rounded border <?= $i === $page ? 'bg-brand-500 text-white font-bold border-brand-500' : 'bg-white text-slate-700 border-slate-200' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

</div>

