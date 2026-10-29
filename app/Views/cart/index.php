<!-- Shopping Cart Page -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="cartView()" x-init="initCart()">

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
    Your Shopping Cart
  </h1>

  <!-- Loading State -->
  <div x-show="loading" class="py-16 text-center space-y-3">
    <div class="inline-block w-8 h-8 border-4 border-brand-500 border-t-transparent rounded-full animate-spin"></div>
    <div class="text-xs font-semibold text-slate-500">Recalculating real-time stock &amp; tier pricing...</div>
  </div>

  <!-- Cart with Items -->
  <div x-show="!loading && items.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" x-cloak>
    
    <!-- Items Table -->
    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      
      <!-- Free Shipping Meter -->
      <div class="p-4 bg-slate-50 border-b border-slate-200">
        <template x-if="freeShippingEligible">
          <div class="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
            <span>🎉</span> You qualify for <strong>FREE Nationwide Shipping</strong>!
          </div>
        </template>
        <template x-if="!freeShippingEligible">
          <div class="text-xs text-slate-600">
            Add <strong class="text-slate-900">Rs. <span x-text="Number(freeShippingMin - subtotal).toLocaleString()"></span></strong> more to get <strong>Free Nationwide Shipping</strong>!
          </div>
        </template>
      </div>

      <div class="divide-y divide-slate-100">
        <template x-for="item in items" :key="item.product_id">
          <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-center gap-4">
            
            <img :src="item.image" :alt="item.name" class="w-20 h-20 object-contain p-1 bg-slate-50 rounded-lg border border-slate-200 flex-shrink-0">

            <div class="flex-1 min-w-0 text-center sm:text-left space-y-1">
              <div class="text-[11px] font-mono font-bold text-brand-600">
                OEM: <span x-text="item.oem_number"></span> | SKU: <span x-text="item.sku"></span>
              </div>
              <h3 class="text-sm font-bold text-slate-900 leading-snug">
                <a :href="'/product/' + item.slug" class="hover:text-brand-600" x-text="item.name"></a>
              </h3>
              <div class="text-xs text-slate-500">
                Brand: <strong class="text-slate-700" x-text="item.brand_name"></strong>
              </div>
              <template x-if="item.has_tier_discount">
                <span class="inline-block px-1.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded">
                  Wholesale Tier Applied!
                </span>
              </template>
            </div>

            <!-- Qty Counter -->
            <div class="flex items-center border border-slate-300 rounded-lg bg-white overflow-hidden">
              <button type="button" @click="changeQty(item.product_id, item.qty - 1)" class="px-3 py-1.5 text-slate-600 hover:bg-slate-100 font-bold">&minus;</button>
              <span class="w-10 text-center text-xs font-bold" x-text="item.qty"></span>
              <button type="button" @click="changeQty(item.product_id, item.qty + 1)" class="px-3 py-1.5 text-slate-600 hover:bg-slate-100 font-bold">&plus;</button>
            </div>

            <!-- Price -->
            <div class="text-right sm:min-w-[100px]">
              <div class="text-sm font-extrabold text-slate-900 font-condensed">
                Rs. <span x-text="Number(item.subtotal).toLocaleString()"></span>
              </div>
              <div class="text-[11px] text-slate-400">
                Rs. <span x-text="Number(item.price).toLocaleString()"></span> each
              </div>
            </div>

            <!-- Remove Button -->
            <button type="button" @click="removeItem(item.product_id)" class="text-slate-400 hover:text-rose-600 text-lg p-1" title="Remove Item">
              &times;
            </button>

          </div>
        </template>
      </div>

      <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center text-xs">
        <a href="/parts" class="font-bold text-brand-600 hover:text-brand-800 uppercase">&larr; Continue Shopping</a>
        <button type="button" @click="Cart.clear(); initCart();" class="text-slate-500 hover:text-rose-600">Clear Cart</button>
      </div>

    </div>

    <!-- Order Summary Sidebar -->
    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
      <h3 class="text-sm font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-3">
        Order Summary
      </h3>

      <div class="space-y-3 text-xs text-slate-600">
        <div class="flex justify-between">
          <span>Items Subtotal</span>
          <span class="font-bold text-slate-900">Rs. <span x-text="Number(subtotal).toLocaleString()"></span></span>
        </div>

        <template x-if="discount > 0">
          <div class="flex justify-between text-emerald-600 font-semibold">
            <span>Coupon Discount</span>
            <span>-Rs. <span x-text="Number(discount).toLocaleString()"></span></span>
          </div>
        </template>

        <div class="flex justify-between">
          <span>Estimated Shipping</span>
          <span class="font-bold" :class="shipping === 0 ? 'text-emerald-600' : 'text-slate-900'">
            <span x-text="shipping === 0 ? 'FREE' : 'Rs. ' + Number(shipping).toLocaleString()"></span>
          </span>
        </div>

        <div class="border-t border-slate-100 pt-3 flex justify-between text-base font-extrabold text-slate-900 font-condensed">
          <span>Total (PKR)</span>
          <span class="text-brand-600">Rs. <span x-text="Number(total).toLocaleString()"></span></span>
        </div>
      </div>

      <!-- Coupon Form -->
      <div class="pt-2">
        <div class="flex gap-2">
          <input type="text" x-model="couponCode" placeholder="Promo / Coupon Code" class="form-input text-xs uppercase" :disabled="couponApplied">
          <button type="button" @click="applyCouponCode()" class="btn-outline text-xs whitespace-nowrap" :disabled="couponApplied">
            <span x-text="couponApplied ? 'Applied' : 'Apply'"></span>
          </button>
        </div>
        <p x-show="couponMessage" x-text="couponMessage" class="text-[11px] mt-1.5 font-semibold" :class="couponApplied ? 'text-emerald-600' : 'text-rose-600'"></p>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3 pt-2">
        <a href="/checkout" class="w-full btn-primary py-3 text-sm text-center block shadow-md">
          Proceed to Checkout &rarr;
        </a>
      </div>

    </div>

  </div>

  <!-- Empty Cart State -->
  <div x-show="!loading && items.length === 0" class="bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center space-y-4 max-w-lg mx-auto" x-cloak>
    <div class="text-6xl">🛒</div>
    <h2 class="text-lg font-bold uppercase tracking-wider font-condensed text-slate-900">Your Cart is Currently Empty</h2>
    <p class="text-xs text-slate-500 leading-relaxed">
      Browse our extensive inventory of genuine Japanese, German, and aftermarket spare parts to get started.
    </p>
    <div class="pt-2">
      <a href="/parts" class="btn-primary text-xs py-2.5 px-6">Explore Auto Parts Catalog &rarr;</a>
    </div>
  </div>

</div>

<script>
function cartView() {
  return {
    loading: true,
    items: [],
    subtotal: 0,
    discount: 0,
    shipping: 250,
    total: 0,
    freeShippingEligible: false,
    freeShippingMin: 5000,
    couponCode: '',
    couponApplied: false,
    couponMessage: '',

    initCart: function() {
      this.syncCart();
      window.addEventListener('cart:updated', () => this.syncCart());
    },

    syncCart: function() {
      const stored = Cart.getItems();
      if (stored.length === 0) {
        this.items = [];
        this.loading = false;
        return;
      }

      this.loading = true;
      fetch('/cart/sync', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items: stored, coupon_code: this.couponCode })
      })
      .then(res => res.json())
      .then(data => {
        this.items = data.items;
        this.subtotal = data.subtotal;
        this.discount = data.discount;
        this.shipping = data.shipping;
        this.total = data.total;
        this.freeShippingEligible = data.free_shipping_eligible;
        this.freeShippingMin = data.free_shipping_min;
        this.loading = false;
      })
      .catch(() => {
        this.loading = false;
      });
    },

    changeQty: function(productId, qty) {
      Cart.update(productId, qty);
    },

    removeItem: function(productId) {
      Cart.remove(productId);
    },

    applyCouponCode: function() {
      if (!this.couponCode.trim()) return;

      fetch('/cart/apply-coupon', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code: this.couponCode, subtotal: this.subtotal })
      })
      .then(r => r.json())
      .then(res => {
        this.couponMessage = res.message;
        if (res.valid) {
          this.couponApplied = true;
          this.syncCart();
        } else {
          this.couponApplied = false;
        }
      });
    }
  };
}
</script>

