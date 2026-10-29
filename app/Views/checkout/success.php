<!-- Order Confirmation Success Page -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 space-y-8">
    
    <!-- Header with Confirmation Badge -->
    <div class="text-center space-y-3 pb-6 border-b border-slate-100">
      <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-3xl mx-auto font-bold">
        ✓
      </div>
      <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 font-condensed">Order Placed Successfully!</span>
      <h1 class="text-2xl sm:text-3xl font-extrabold uppercase font-condensed text-slate-900 tracking-tight">
        Thank You, <?= e($order['customer_name']) ?>!
      </h1>
      <p class="text-xs text-slate-500 max-w-md mx-auto">
        Your order is being reviewed by our warehouse dispatch team. You will receive an SMS and WhatsApp tracking update upon dispatch.
      </p>

      <div class="inline-block mt-3 px-4 py-2 bg-slate-100 rounded-lg text-sm font-mono font-bold text-slate-900 border border-slate-200">
        Order ID: <span class="text-brand-600"><?= e($order['order_number']) ?></span>
      </div>
    </div>

    <!-- Order Metadata Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
      <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
        <span class="text-[10px] font-bold uppercase text-slate-400 font-condensed">Delivery Address</span>
        <div class="font-bold text-slate-900 mt-1"><?= e($order['customer_name']) ?></div>
        <div class="text-slate-600"><?= e($order['delivery_address']) ?></div>
        <div class="text-slate-600 font-semibold"><?= e($order['delivery_city']) ?></div>
        <div class="text-slate-600 font-mono mt-1"><?= e($order['customer_phone']) ?></div>
      </div>

      <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
        <span class="text-[10px] font-bold uppercase text-slate-400 font-condensed">Payment Method</span>
        <div class="font-bold text-slate-900 uppercase font-condensed mt-1"><?= e($order['payment_method']) ?></div>
        <div class="text-slate-500 mt-1">Status: <span class="font-bold uppercase text-brand-600"><?= e($order['payment_status']) ?></span></div>
        <div class="text-[11px] text-slate-400 mt-1">Payable in Cash upon Courier Delivery</div>
      </div>

      <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
        <span class="text-[10px] font-bold uppercase text-slate-400 font-condensed">Estimated Courier Delivery</span>
        <div class="font-bold text-slate-900 mt-1">2-3 Working Days</div>
        <div class="text-slate-500 mt-1">Courier: TCS / Leopards Courier</div>
        <a href="/order/track?order_number=<?= urlencode($order['order_number']) ?>&phone=<?= urlencode($order['customer_phone']) ?>"
           class="inline-block mt-2 text-brand-600 hover:underline font-bold">
          Track Package Online &rarr;
        </a>
      </div>
    </div>

    <!-- Items Table -->
    <div>
      <h3 class="text-sm font-bold uppercase tracking-wider font-condensed text-slate-900 mb-3">
        Purchased Auto Parts
      </h3>
      <div class="border border-slate-200 rounded-xl overflow-hidden text-xs">
        <table class="w-full text-left">
          <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
            <tr>
              <th class="py-2.5 px-4">Part Description</th>
              <th class="py-2.5 px-4">OEM Number</th>
              <th class="py-2.5 px-4 text-center">Qty</th>
              <th class="py-2.5 px-4 text-right">Price</th>
              <th class="py-2.5 px-4 text-right">Subtotal</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php foreach ($items as $item): ?>
              <tr>
                <td class="py-3 px-4 font-bold text-slate-900"><?= e($item['product_name']) ?></td>
                <td class="py-3 px-4 font-mono text-slate-500"><?= e($item['oem_number']) ?></td>
                <td class="py-3 px-4 text-center font-bold"><?= (int)$item['quantity'] ?></td>
                <td class="py-3 px-4 text-right text-slate-600"><?= format_pkr($item['unit_price']) ?></td>
                <td class="py-3 px-4 text-right font-bold text-slate-900"><?= format_pkr($item['subtotal']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot class="bg-slate-50 border-t border-slate-200 text-xs">
            <tr>
              <td colspan="4" class="py-2 px-4 text-right font-semibold text-slate-600">Subtotal:</td>
              <td class="py-2 px-4 text-right font-bold text-slate-900"><?= format_pkr($order['subtotal']) ?></td>
            </tr>
            <?php if ((float)$order['discount_amount'] > 0): ?>
              <tr>
                <td colspan="4" class="py-1 px-4 text-right text-emerald-600 font-semibold">Discount:</td>
                <td class="py-1 px-4 text-right text-emerald-600 font-bold">-<?= format_pkr($order['discount_amount']) ?></td>
              </tr>
            <?php endif; ?>
            <tr>
              <td colspan="4" class="py-1 px-4 text-right text-slate-600">Shipping Fee:</td>
              <td class="py-1 px-4 text-right font-semibold"><?= format_pkr($order['shipping_fee']) ?></td>
            </tr>
            <?php if ((float)$order['cod_fee'] > 0): ?>
              <tr>
                <td colspan="4" class="py-1 px-4 text-right text-slate-600">COD Fee:</td>
                <td class="py-1 px-4 text-right font-semibold"><?= format_pkr($order['cod_fee']) ?></td>
              </tr>
            <?php endif; ?>
            <tr class="text-sm font-extrabold text-slate-900 border-t border-slate-200 font-condensed">
              <td colspan="4" class="py-3 px-4 text-right uppercase">Total Amount:</td>
              <td class="py-3 px-4 text-right text-brand-600"><?= format_pkr($order['total']) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100 no-print">
      <button type="button" onclick="window.print()" class="btn-outline text-xs">
        🖨️ Print Receipt
      </button>

      <div class="flex items-center gap-3">
        <a href="https://wa.me/<?= e(setting('contact_whatsapp', '923001234567')) ?>?text=<?= urlencode("Salam! I placed Order {$order['order_number']}. Please confirm dispatch status.") ?>"
           target="_blank"
           rel="noopener"
           class="btn-whatsapp text-xs">
          Message on WhatsApp
        </a>
        <a href="/parts" class="btn-primary text-xs">
          Continue Shopping &rarr;
        </a>
      </div>
    </div>

  </div>
</div>

