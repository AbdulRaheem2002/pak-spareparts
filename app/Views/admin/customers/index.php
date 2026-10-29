<!-- Admin Customer Directory View -->
<div class="space-y-6">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">Customer Directory</h2>
      <p class="text-xs text-slate-500">Aggregated buyer history, contact details, and total spend</p>
    </div>
    <a href="/admin/customers/export" class="btn-outline text-xs py-2">
      📥 Export Customers CSV
    </a>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Customer</th>
            <th class="py-3 px-4">Phone / WhatsApp</th>
            <th class="py-3 px-4">City</th>
            <th class="py-3 px-4 text-center">Orders</th>
            <th class="py-3 px-4 text-right">Total Spent (PKR)</th>
            <th class="py-3 px-4 text-right">Last Order</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($customers['data'])): ?>
            <?php foreach ($customers['data'] as $c): ?>
              <tr class="hover:bg-slate-50">
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900"><?= e($c['customer_name']) ?></div>
                  <div class="text-[11px] text-slate-400"><?= e($c['customer_email'] ?: 'No email') ?></div>
                </td>
                <td class="py-3 px-4 font-mono font-bold text-brand-600">
                  <a href="https://wa.me/<?= e(whatsapp_clean_phone($c['customer_phone'])) ?>" target="_blank" class="hover:underline">
                    <?= e($c['customer_phone']) ?>
                  </a>
                </td>
                <td class="py-3 px-4 text-slate-700"><?= e($c['delivery_city']) ?></td>
                <td class="py-3 px-4 text-center font-mono font-bold"><?= (int)$c['total_orders'] ?></td>
                <td class="py-3 px-4 text-right font-bold text-slate-900 font-condensed">
                  <?= format_pkr($c['total_spent']) ?>
                </td>
                <td class="py-3 px-4 text-right text-slate-500 font-mono">
                  <?= \App\Helpers\Formatter::date($c['last_order_date'], 'd M Y') ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="py-8 text-center text-slate-400">No customer records found yet.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

