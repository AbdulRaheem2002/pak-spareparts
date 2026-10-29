<!-- Admin Order Details & Status Transition View -->
<div class="space-y-6">

  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-4 border-b border-slate-200 gap-4">
    <div>
      <div class="flex items-center gap-3">
        <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">
          Order #<?= e($order['order_number']) ?>
        </h2>
        <?= \App\Helpers\Formatter::orderStatusBadge($order['status']) ?>
      </div>
      <p class="text-xs text-slate-500 mt-0.5">Placed on <?= \App\Helpers\Formatter::date($order['created_at']) ?></p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <!-- Direct WhatsApp Customer Message -->
      <?php
        $waCustomerMsg = "Salam {$order['customer_name']}! Your PakAutoParts order #{$order['order_number']} status is: *" . strtoupper($order['status']) . "*."
                       . ($order['tracking_number'] ? "\nCourier: {$order['courier_name']}\nTracking Number: {$order['tracking_number']}" : "");
        $cleanPhone = whatsapp_clean_phone($order['customer_phone']);
      ?>
      <a href="https://wa.me/<?= e($cleanPhone) ?>?text=<?= urlencode($waCustomerMsg) ?>"
         target="_blank"
         rel="noopener"
         class="btn-whatsapp text-xs py-2">
        <span>💬</span> Message Customer
      </a>

      <!-- Printables -->
      <a href="/admin/orders/invoice/<?= (int)$order['id'] ?>" target="_blank" class="btn-outline text-xs py-2">
        📄 A4 Invoice
      </a>
      <a href="/admin/orders/thermal/<?= (int)$order['id'] ?>" target="_blank" class="btn-outline text-xs py-2">
        🧾 80mm Slip
      </a>
      <a href="/admin/orders/packing-slip/<?= (int)$order['id'] ?>" target="_blank" class="btn-outline text-xs py-2">
        📦 Packing Slip
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

    <!-- LEFT: ITEMS & TIMELINE (8 Cols) -->
    <div class="lg:col-span-8 space-y-6">
      
      <!-- Order Items Table -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-800">
            Order Items (<?= count($items) ?> Parts)
          </h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-condensed font-bold">
              <tr>
                <th class="py-2.5 px-4">Part Description</th>
                <th class="py-2.5 px-4">OEM &amp; SKU</th>
                <th class="py-2.5 px-4 text-center">Qty</th>
                <th class="py-2.5 px-4 text-right">Unit Price</th>
                <th class="py-2.5 px-4 text-right">Subtotal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($items as $it): ?>
                <tr>
                  <td class="py-3 px-4 font-bold text-slate-900"><?= e($it['product_name']) ?></td>
                  <td class="py-3 px-4 font-mono text-slate-500">
                    <div>OEM: <strong class="text-brand-600"><?= e($it['oem_number']) ?></strong></div>
                    <div class="text-[10px]">SKU: <?= e($it['sku']) ?></div>
                  </td>
                  <td class="py-3 px-4 text-center font-bold font-mono"><?= (int)$it['quantity'] ?></td>
                  <td class="py-3 px-4 text-right text-slate-600"><?= format_pkr($it['unit_price']) ?></td>
                  <td class="py-3 px-4 text-right font-bold text-slate-900"><?= format_pkr($it['subtotal']) ?></td>
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
                  <td colspan="4" class="py-1 px-4 text-right text-emerald-600 font-semibold">Discount (<?= e($order['coupon_code']) ?>):</td>
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
                <td colspan="4" class="py-3 px-4 text-right uppercase">Grand Total (PKR):</td>
                <td class="py-3 px-4 text-right text-brand-600"><?= format_pkr($order['total']) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Status Audit History Timeline -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
          Order Status Audit Trail
        </h3>
        <div class="space-y-3 border-l-2 border-slate-200 pl-4 text-xs">
          <?php foreach ($history as $h): ?>
            <div class="relative">
              <span class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-brand-500"></span>
              <div class="flex items-center gap-2">
                <span class="font-bold text-slate-900"><?= e($h['status']) ?></span>
                <span class="text-slate-400 font-mono text-[11px]"><?= \App\Helpers\Formatter::date($h['created_at']) ?></span>
                <span class="text-slate-500 text-[10px] font-semibold bg-slate-100 px-1.5 rounded">By <?= e($h['created_by']) ?></span>
              </div>
              <?php if (!empty($h['comment'])): ?>
                <p class="text-slate-600 mt-0.5"><?= e($h['comment']) ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <!-- RIGHT: STATUS ACTIONS & CUSTOMER INFO (4 Cols) -->
    <div class="lg:col-span-4 space-y-6">

      <!-- Status Update Form -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
          Update Order &amp; Courier Status
        </h3>

        <form action="/admin/orders/status/<?= (int)$order['id'] ?>" method="POST" class="space-y-4">
          <?= csrf_field() ?>

          <div>
            <label class="form-label">Order Status</label>
            <select name="status" class="form-input text-xs font-bold font-condensed">
              <?php foreach (['Pending', 'Confirmed', 'Dispatched', 'Delivered', 'Cancelled', 'Returned'] as $st): ?>
                <option value="<?= $st ?>" <?= $order['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="form-label">Courier Service</label>
            <select name="courier_name" class="form-input text-xs">
              <option value="">-- Choose Courier --</option>
              <option value="TCS Express" <?= $order['courier_name'] === 'TCS Express' ? 'selected' : '' ?>>TCS Express</option>
              <option value="Leopards Courier" <?= $order['courier_name'] === 'Leopards Courier' ? 'selected' : '' ?>>Leopards Courier</option>
              <option value="Trax Logistics" <?= $order['courier_name'] === 'Trax Logistics' ? 'selected' : '' ?>>Trax Logistics</option>
              <option value="Daewoo Fastex" <?= $order['courier_name'] === 'Daewoo Fastex' ? 'selected' : '' ?>>Daewoo Fastex</option>
              <option value="Pakistan Post" <?= $order['courier_name'] === 'Pakistan Post' ? 'selected' : '' ?>>Pakistan Post (EMS)</option>
            </select>
          </div>

          <div>
            <label class="form-label">Tracking / Consignment #</label>
            <input type="text"
                   name="tracking_number"
                   value="<?= e($order['tracking_number'] ?? '') ?>"
                   placeholder="e.g. 1290381023"
                   class="form-input text-xs font-mono font-bold">
          </div>

          <div>
            <label class="form-label">Internal Status Note</label>
            <textarea name="comment" rows="2" class="form-input text-xs" placeholder="e.g. Dispatched via TCS Gulberg branch..."></textarea>
          </div>

          <button type="submit" class="w-full btn-primary text-xs py-2.5">
            Update Status &amp; Save
          </button>
        </form>
      </div>

      <!-- Customer & Shipping Details -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3 text-xs">
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
          Customer &amp; Delivery Info
        </h3>

        <div>
          <span class="text-[10px] text-slate-400 font-bold uppercase font-condensed">Name:</span>
          <div class="font-bold text-slate-900"><?= e($order['customer_name']) ?></div>
        </div>

        <div>
          <span class="text-[10px] text-slate-400 font-bold uppercase font-condensed">Phone Number:</span>
          <div class="font-mono font-bold text-brand-600">
            <a href="tel:<?= e($order['customer_phone']) ?>"><?= e($order['customer_phone']) ?></a>
          </div>
        </div>

        <?php if (!empty($order['customer_email'])): ?>
          <div>
            <span class="text-[10px] text-slate-400 font-bold uppercase font-condensed">Email:</span>
            <div class="text-slate-700"><?= e($order['customer_email']) ?></div>
          </div>
        <?php endif; ?>

        <div>
          <span class="text-[10px] text-slate-400 font-bold uppercase font-condensed">Delivery Destination:</span>
          <div class="text-slate-800 leading-relaxed font-semibold"><?= e($order['delivery_address']) ?></div>
          <div class="text-slate-900 font-bold mt-0.5"><?= e($order['delivery_city']) ?></div>
        </div>

        <?php if (!empty($order['notes'])): ?>
          <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-amber-900">
            <strong>Order Notes:</strong> <?= e($order['notes']) ?>
          </div>
        <?php endif; ?>

        <!-- Payment Proof Screenshot Preview -->
        <?php if (!empty($order['payment_proof_image'])): ?>
          <div class="pt-3 border-t border-slate-100">
            <span class="text-[10px] text-slate-400 font-bold uppercase font-condensed">Payment Screenshot</span>
            <a href="<?= e($order['payment_proof_image']) ?>" target="_blank" class="block mt-1 aspect-video bg-slate-50 border border-slate-200 rounded-lg overflow-hidden">
              <img src="<?= e($order['payment_proof_image']) ?>" alt="Payment Proof" class="w-full h-full object-cover">
            </a>
          </div>
        <?php endif; ?>
      </div>

    </div>

  </div>

</div>

