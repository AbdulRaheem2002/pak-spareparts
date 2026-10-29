<!-- Checkout View -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="checkoutView()" x-init="initCheckout()">

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

  <h1 class="text-2xl font-bold uppercase tracking-wider font-condensed text-slate-900 mb-8">
    Checkout &amp; Delivery Details
  </h1>

  <form action="/checkout/process" method="POST" enctype="multipart/form-data" id="checkout-form" @submit="handleSubmit($event)">
    <?= csrf_field() ?>
    <input type="hidden" name="cart_data" :value="JSON.stringify(rawCartItems)">
    <input type="hidden" name="coupon_code" :value="couponCode">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

      <!-- ========================================================================
           LEFT: CONTACT, DELIVERY & PAYMENT SELECTION
           ======================================================================== -->
      <div class="lg:col-span-7 space-y-6">

        <!-- 1. Customer Information -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h2 class="text-sm font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            1. Customer Contact
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="form-label">Full Name *</label>
              <input type="text" name="customer_name" class="form-input text-xs" required placeholder="e.g. Tariq Mehmood">
            </div>

            <div>
              <label class="form-label">Pakistani Mobile Number *</label>
              <input type="tel" name="customer_phone" class="form-input text-xs" required placeholder="0300-1234567" pattern="^((\+92)|(0092))-{0,1}3\d{2}-{0,1}\d{7}$|^03\d{2}-{0,1}\d{7}$">
              <span class="text-[10px] text-slate-400 mt-1 block">For courier delivery &amp; WhatsApp updates</span>
            </div>

            <div class="sm:col-span-2">
              <label class="form-label">Email Address (Optional)</label>
              <input type="email" name="customer_email" class="form-input text-xs" placeholder="name@domain.com (for PDF invoice)">
            </div>
          </div>
        </div>

        <!-- 2. Delivery Address -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h2 class="text-sm font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            2. Shipping Destination
          </h2>

          <div class="space-y-4">
            <div>
              <label class="form-label">Destination City *</label>
              <select name="delivery_city" x-model="selectedCity" @change="updateShippingRate()" class="form-input text-xs" required>
                <option value="">-- Select Your City in Pakistan --</option>
                <?php foreach ($shippingRates as $rate): ?>
                  <option value="<?= e($rate['city']) ?>"
                          data-standard="<?= (float)$rate['standard_rate'] ?>"
                          data-cod="<?= (float)$rate['cod_fee'] ?>"
                          data-free="<?= (float)$rate['free_threshold'] ?>"
                          data-days="<?= e($rate['delivery_days']) ?>">
                    <?= e($rate['city']) ?> (<?= e($rate['province']) ?> - Est: <?= e($rate['delivery_days']) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="form-label">Complete Street Address / Workshop Name *</label>
              <textarea name="delivery_address" rows="2" class="form-input text-xs" required placeholder="House/Shop #, Street, Sector or Landmark..."></textarea>
            </div>

            <div>
              <label class="form-label">Order Notes / Vehicle VIN / Part Instructions (Optional)</label>
              <input type="text" name="notes" class="form-input text-xs" placeholder="e.g. Call before delivery, Corolla 2017 manual transmission">
            </div>
          </div>
        </div>

        <!-- 3. Payment Method -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h2 class="text-sm font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            3. Choose Payment Method
          </h2>

          <div class="space-y-3">
            
            <!-- Cash on Delivery (COD) -->
            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer bg-slate-50 transition-colors">
              <input type="radio" name="payment_method" value="cod" x-model="paymentMethod" class="mt-1 text-brand-500 focus:ring-brand-500">
              <div class="flex-1 text-xs">
                <div class="font-bold text-slate-900 uppercase font-condensed">Cash on Delivery (Nationwide COD)</div>
                <div class="text-slate-500 mt-0.5">Pay the TCS / Leopards courier rider in cash when the package arrives at your door.</div>
              </div>
            </label>

            <!-- Meezan Bank Transfer -->
            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer bg-slate-50 transition-colors">
              <input type="radio" name="payment_method" value="bank_transfer" x-model="paymentMethod" class="mt-1 text-brand-500 focus:ring-brand-500">
              <div class="flex-1 text-xs">
                <div class="font-bold text-slate-900 uppercase font-condensed">Direct Bank Transfer (Meezan Bank)</div>
                <div class="text-slate-500 mt-0.5">Transfer directly via Meezan Internet Banking / 1Link. Upload transaction screenshot below.</div>
                
                <!-- Bank Info Box -->
                <div x-show="paymentMethod === 'bank_transfer'" class="mt-3 p-3 bg-white rounded-lg border border-slate-200 text-xs space-y-1 font-mono" x-cloak>
                  <div><strong>Bank:</strong> <?= e(setting('bank_name', 'Meezan Bank Limited')) ?></div>
                  <div><strong>Account Title:</strong> <?= e(setting('bank_title', 'PAK AUTO PARTS TRADERS')) ?></div>
                  <div><strong>Account No:</strong> <?= e(setting('bank_account_no', '02010108923412')) ?></div>
                  <div><strong>IBAN:</strong> <?= e(setting('bank_iban', 'PK45MEZN0002010108923412')) ?></div>
                </div>
              </div>
            </label>

            <!-- JazzCash Manual -->
            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer bg-slate-50 transition-colors">
              <input type="radio" name="payment_method" value="jazzcash" x-model="paymentMethod" class="mt-1 text-brand-500 focus:ring-brand-500">
              <div class="flex-1 text-xs">
                <div class="font-bold text-slate-900 uppercase font-condensed">JazzCash Mobile Wallet</div>
                <div class="text-slate-500 mt-0.5">Send payment via JazzCash App or *786#. Account: <strong><?= e(setting('jazzcash_no', '03001234567')) ?></strong></div>
              </div>
            </label>

            <!-- Easypaisa Manual -->
            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer bg-slate-50 transition-colors">
              <input type="radio" name="payment_method" value="easypaisa" x-model="paymentMethod" class="mt-1 text-brand-500 focus:ring-brand-500">
              <div class="flex-1 text-xs">
                <div class="font-bold text-slate-900 uppercase font-condensed">Easypaisa Mobile Wallet</div>
                <div class="text-slate-500 mt-0.5">Send payment to Easypaisa Account: <strong><?= e(setting('easypaisa_no', '03001234567')) ?></strong></div>
              </div>
            </label>

            <!-- Instant WhatsApp Checkout -->
            <label class="flex items-start gap-3 p-3.5 rounded-xl border border-emerald-300 hover:border-emerald-500 cursor-pointer bg-emerald-50 transition-colors">
              <input type="radio" name="payment_method" value="whatsapp" x-model="paymentMethod" class="mt-1 text-emerald-600 focus:ring-emerald-500">
              <div class="flex-1 text-xs">
                <div class="font-bold text-emerald-950 uppercase font-condensed flex items-center gap-1.5">
                  <span>💬</span> Instant WhatsApp Direct Order
                </div>
                <div class="text-emerald-800 mt-0.5">Save order in system and open WhatsApp with itemized bill to confirm with our staff directly.</div>
              </div>
            </label>

          </div>

          <!-- Screenshot upload for manual transfers -->
          <div x-show="['bank_transfer', 'jazzcash', 'easypaisa'].includes(paymentMethod)" class="pt-2" x-cloak>
            <label class="form-label">Upload Payment Screenshot / Receipt</label>
            <input type="file" name="payment_proof" accept="image/*" class="form-input text-xs py-1.5">
            <span class="text-[10px] text-slate-400 mt-1 block">JPG, PNG, or WebP up to 5MB</span>
          </div>

        </div>

      </div>

      <!-- ========================================================================
           RIGHT: ORDER SUMMARY
           ======================================================================== -->
      <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <h3 class="text-sm font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-3">
          Order Review (<span x-text="items.length"></span> Parts)
        </h3>

        <!-- Item list preview -->
        <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 pr-1">
          <template x-for="item in items" :key="item.product_id">
            <div class="py-2.5 flex items-center justify-between text-xs gap-3">
              <div class="flex-1 min-w-0">
                <div class="font-bold text-slate-900 truncate" x-text="item.name"></div>
                <div class="text-[11px] text-slate-400 font-mono">
                  OEM: <span x-text="item.oem_number"></span> | Qty: <span x-text="item.qty"></span>x
                </div>
              </div>
              <div class="font-bold text-slate-900 text-right whitespace-nowrap">
                Rs. <span x-text="Number(item.subtotal).toLocaleString()"></span>
              </div>
            </div>
          </template>
        </div>

        <!-- Financial Breakdown -->
        <div class="border-t border-slate-100 pt-4 space-y-2.5 text-xs text-slate-600">
          <div class="flex justify-between">
            <span>Subtotal</span>
            <span class="font-bold text-slate-900">Rs. <span x-text="Number(subtotal).toLocaleString()"></span></span>
          </div>

          <template x-if="discount > 0">
            <div class="flex justify-between text-emerald-600 font-semibold">
              <span>Coupon Discount (<span x-text="couponCode"></span>)</span>
              <span>-Rs. <span x-text="Number(discount).toLocaleString()"></span></span>
            </div>
          </template>

          <div class="flex justify-between">
            <span>Delivery Fee</span>
            <span class="font-bold" :class="shippingFee === 0 ? 'text-emerald-600' : 'text-slate-900'">
              <span x-text="shippingFee === 0 ? 'FREE' : 'Rs. ' + Number(shippingFee).toLocaleString()"></span>
            </span>
          </div>

          <template x-if="paymentMethod === 'cod' && codFee > 0">
            <div class="flex justify-between">
              <span>Cash on Delivery Handling</span>
              <span class="font-bold text-slate-900">Rs. <span x-text="Number(codFee).toLocaleString()"></span></span>
            </div>
          </template>

          <div class="border-t border-slate-200 pt-3 flex justify-between text-lg font-extrabold text-slate-900 font-condensed">
            <span>Total Payable</span>
            <span class="text-brand-600">Rs. <span x-text="Number(calculateGrandTotal()).toLocaleString()"></span></span>
          </div>
        </div>

        <!-- Place Order Button -->
        <button type="submit" class="w-full btn-primary py-3.5 text-sm shadow-xl flex items-center justify-center gap-2">
          <span x-text="paymentMethod === 'whatsapp' ? 'Proceed to WhatsApp Order' : 'Confirm Order & Deliver'"></span> &rarr;
        </button>

        <p class="text-[11px] text-slate-400 text-center leading-relaxed">
          🔒 By confirming, you agree to our 7-Day Return and Genuine Parts Guarantee. No advance payment needed for Cash on Delivery.
        </p>

      </div>

    </div>
  </form>
</div>

<script>
function checkoutView() {
  return {
    rawCartItems: [],
    items: [],
    subtotal: 0,
    discount: 0,
    shippingFee: 250,
    codFee: 50,
    selectedCity: '',
    paymentMethod: 'cod',
    couponCode: '',

    initCheckout: function() {
      this.rawCartItems = Cart.getItems();
      if (this.rawCartItems.length === 0) {
        window.location.href = '/cart';
        return;
      }

      // Sync items to get authoritative figures
      fetch('/cart/sync', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items: this.rawCartItems })
      })
      .then(r => r.json())
      .then(d => {
        this.items = d.items;
        this.subtotal = d.subtotal;
        this.discount = d.discount;
        this.couponCode = d.coupon ? d.coupon.code : '';
      });
    },

    updateShippingRate: function() {
      const select = document.querySelector('select[name="delivery_city"]');
      const opt = select.options[select.selectedIndex];
      if (!opt || !opt.dataset.standard) return;

      const standard = parseFloat(opt.dataset.standard);
      const free = parseFloat(opt.dataset.free);
      this.codFee = parseFloat(opt.dataset.cod || 50);

      this.shippingFee = (this.subtotal >= free) ? 0 : standard;
    },

    calculateGrandTotal: function() {
      let t = this.subtotal - this.discount + this.shippingFee;
      if (this.paymentMethod === 'cod') {
        t += this.codFee;
      }
      return Math.max(0, t);
    },

    handleSubmit: function(e) {
      if (this.items.length === 0) {
        e.preventDefault();
        window.showToast("Your cart is empty.", "error");
        return;
      }
      // Clean cart on local storage after submitting
      setTimeout(() => {
        Cart.clear();
      }, 500);
    }
  };
}
</script>

