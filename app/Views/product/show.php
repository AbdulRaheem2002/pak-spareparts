<!-- Comprehensive Product Details View -->
<?php
  $images = $product['images'] ?: [['image_url' => '/assets/images/placeholder.svg', 'thumbnail_url' => '/assets/images/placeholder.svg']];
  $primaryImg = $images[0]['image_url'] ?? '/assets/images/placeholder.svg';
  $isOutOfStock = (int)$product['stock_quantity'] <= 0;
  $hasDiscount = !empty($product['compare_at_price']) && (float)$product['compare_at_price'] > (float)$product['price'];
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeImage: '<?= e($primaryImg) ?>', qty: 1 }">

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

  <!-- Main Product Display Grid -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm mb-12">

    <!-- ==========================================================================
         LEFT: MULTI-IMAGE GALLERY WITH ZOOM PREVIEW
         ========================================================================== -->
    <div class="lg:col-span-5 space-y-4">
      <!-- Main Featured Image -->
      <div class="relative aspect-square bg-slate-50 rounded-xl border border-slate-200 overflow-hidden flex items-center justify-center p-6">
        <img :src="activeImage"
             alt="<?= e($product['name']) ?>"
             width="500"
             height="500"
             class="w-full h-full object-contain transition-all duration-300">
        <div class="absolute top-3 left-3">
          <?= \App\Helpers\Formatter::conditionBadge($product['condition_type'] ?? 'Genuine') ?>
        </div>
      </div>

      <!-- Thumbnail Strip -->
      <?php if (count($images) > 1): ?>
        <div class="flex gap-3 overflow-x-auto pb-2">
          <?php foreach ($images as $img): ?>
            <button type="button"
                    @click="activeImage = '<?= e($img['image_url']) ?>'"
                    :class="activeImage === '<?= e($img['image_url']) ?>' ? 'border-brand-500 ring-2 ring-brand-500/20' : 'border-slate-200'"
                    class="w-16 h-16 flex-shrink-0 rounded-lg border bg-slate-50 overflow-hidden p-1 transition-all">
              <img src="<?= e($img['thumbnail_url'] ?? $img['image_url']) ?>" alt="Thumbnail" class="w-full h-full object-contain">
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- ==========================================================================
         RIGHT: PRODUCT SPECS, FITMENT CHECKER, PRICING & ACTIONS
         ========================================================================== -->
    <div class="lg:col-span-7 space-y-5">
      
      <!-- Brand & Part Identifiers -->
      <div class="flex flex-wrap items-center justify-between text-xs text-slate-500 gap-2 border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="font-bold text-slate-900 uppercase font-condensed tracking-wider">Brand: <?= e($product['brand_name'] ?? 'Genuine') ?></span>
          <span>|</span>
          <span class="font-mono">SKU: <strong class="text-slate-800"><?= e($product['sku']) ?></strong></span>
        </div>
        <div class="font-mono text-xs bg-slate-100 px-2.5 py-1 rounded text-slate-700">
          OEM: <strong class="text-brand-600 font-bold"><?= e($product['oem_number']) ?></strong>
        </div>
      </div>

      <!-- Product Title -->
      <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 uppercase font-condensed tracking-tight leading-snug">
        <?= e($product['name']) ?>
      </h1>

      <!-- Customer Reviews Star Summary -->
      <div class="flex items-center gap-2 text-xs">
        <div class="flex text-amber-400">
          <?php for ($i = 1; $i <= 5; $i++): ?>
            <span><?= $i <= round($ratingSummary['average']) ? '★' : '☆' ?></span>
          <?php endfor; ?>
        </div>
        <span class="font-bold text-slate-900"><?= number_format($ratingSummary['average'], 1) ?></span>
        <span class="text-slate-400">(<?= (int)$ratingSummary['count'] ?> verified reviews)</span>
        <span>•</span>
        <a href="#reviews" class="text-brand-600 hover:underline font-semibold">Read reviews</a>
      </div>

      <!-- VEHICLE COMPATIBILITY / "MY GARAGE" CHECKER BADGE -->
      <div class="p-4 rounded-xl border <?= $fitsGarage === true ? 'bg-emerald-50 border-emerald-300' : ($fitsGarage === false ? 'bg-amber-50 border-amber-300' : 'bg-slate-50 border-slate-200') ?>">
        <?php if ($fitsGarage === true && $activeVehicle): ?>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5 text-emerald-900">
              <span class="text-xl text-emerald-600 font-bold">✓</span>
              <div>
                <div class="text-xs font-bold uppercase font-condensed text-emerald-800">Guaranteed Fitment</div>
                <div class="text-xs text-emerald-900">
                  Fits your <strong><?= e($activeVehicle['make']) ?> <?= e($activeVehicle['model']) ?> (<?= (int)$activeVehicle['year_start'] ?>-<?= (int)$activeVehicle['year_end'] ?>)</strong>
                </div>
              </div>
            </div>
            <button type="button" onclick="document.getElementById('garage-modal').classList.remove('hidden')" class="text-[11px] text-emerald-700 underline font-semibold">Change</button>
          </div>
        <?php elseif ($fitsGarage === false && $activeVehicle): ?>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5 text-amber-900">
              <span class="text-xl text-amber-600 font-bold">⚠</span>
              <div>
                <div class="text-xs font-bold uppercase font-condensed text-amber-800">May Not Fit Your Saved Vehicle</div>
                <div class="text-xs text-amber-900">
                  Not confirmed for <?= e($activeVehicle['make']) ?> <?= e($activeVehicle['model']) ?>. Check compatibility list below.
                </div>
              </div>
            </div>
            <button type="button" onclick="document.getElementById('garage-modal').classList.remove('hidden')" class="text-[11px] text-amber-700 underline font-semibold">Change</button>
          </div>
        <?php else: ?>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-slate-700 text-xs">
              <span>🚗</span>
              <span>Does this part fit your car?</span>
            </div>
            <button type="button" onclick="document.getElementById('garage-modal').classList.remove('hidden')" class="btn-outline py-1 px-3 text-xs">
              Check Vehicle Fitment
            </button>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pricing Display -->
      <div class="flex items-baseline gap-3 pt-2">
        <span class="text-3xl font-extrabold text-slate-900 font-condensed tracking-tight">
          <?= format_pkr($product['price']) ?>
        </span>
        <?php if ($hasDiscount): ?>
          <span class="text-base text-slate-400 line-through">
            <?= format_pkr($product['compare_at_price']) ?>
          </span>
          <span class="text-xs font-bold text-rose-600 uppercase bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
            Save <?= format_pkr((float)$product['compare_at_price'] - (float)$product['price']) ?>
          </span>
        <?php endif; ?>
      </div>

      <!-- WHOLESALE PRICE TIERS (For Mechanics & Workshops) -->
      <?php if (!empty($product['tiers'])): ?>
        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2">
          <div class="flex items-center justify-between text-xs font-bold uppercase font-condensed text-slate-700">
            <span class="flex items-center gap-1.5"><span>🔧</span> Workshop &amp; Mechanic Wholesale Tiers</span>
            <span class="text-brand-600">Volume Discounts</span>
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-xs">
            <?php foreach ($product['tiers'] as $tier): ?>
              <div class="p-2 bg-white rounded border border-slate-200 text-center">
                <div class="text-[10px] text-slate-500 uppercase font-bold">Buy <?= (int)$tier['min_qty'] ?>+ Units</div>
                <div class="text-xs font-bold text-slate-900 font-condensed"><?= format_pkr($tier['unit_price']) ?> / ea</div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Warranty & Delivery Highlights -->
      <div class="grid grid-cols-2 gap-3 text-xs text-slate-600 pt-1">
        <div class="flex items-center gap-2">
          <span class="text-brand-500 text-base">🛡️</span>
          <span><strong>Warranty:</strong> <?= e($product['warranty']) ?></span>
        </div>
        <div class="flex items-center gap-2">
          <span class="text-brand-500 text-base">🚚</span>
          <span><strong>Delivery:</strong> 2-4 Days (COD Available)</span>
        </div>
      </div>

      <!-- Action Buttons (Add to Cart, Buy Now, WhatsApp) -->
      <?php if ($isOutOfStock): ?>
        <div id="notify" class="p-4 bg-slate-100 rounded-xl border border-slate-300 space-y-3">
          <div class="text-xs font-bold text-slate-700 uppercase font-condensed">Currently Out of Stock</div>
          <p class="text-xs text-slate-500">Leave your phone number to receive an alert when restocked.</p>
          <form action="/stock-notification" method="POST" class="flex gap-2">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <input type="text" name="customer_phone" placeholder="03XX-XXXXXXX" class="form-input text-xs" required>
            <button type="submit" class="btn-primary whitespace-nowrap text-xs">Notify Me</button>
          </form>
        </div>
      <?php else: ?>
        <div class="space-y-3 pt-3">
          <div class="flex items-center gap-3">
            <!-- Quantity Counter -->
            <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden bg-white">
              <button type="button" @click="qty = Math.max(1, qty - 1)" class="px-3 py-2 text-slate-600 hover:bg-slate-100 font-bold">&minus;</button>
              <input type="text" x-model.number="qty" readonly class="w-12 text-center text-sm font-bold border-0 focus:ring-0">
              <button type="button" @click="qty = qty + 1" class="px-3 py-2 text-slate-600 hover:bg-slate-100 font-bold">&plus;</button>
            </div>

            <!-- Add to Cart -->
            <button type="button"
                    @click="Cart.add(<?= (int)$product['id'] ?>, qty)"
                    class="flex-1 btn-primary py-3 text-sm shadow-md">
              Add to Shopping Cart
            </button>
          </div>

          <!-- Direct Order Buttons -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <button type="button"
                    onclick="Cart.add(<?= (int)$product['id'] ?>, 1); window.location.href = '/checkout';"
                    class="btn-secondary py-2.5 text-xs">
              Instant Checkout &rarr;
            </button>

            <!-- Buy via WhatsApp -->
            <a href="https://wa.me/<?= e(setting('contact_whatsapp', '923001234567')) ?>?text=<?= urlencode("Salam! I want to order this part:\n*Product:* {$product['name']}\n*OEM:* {$product['oem_number']}\n*SKU:* {$product['sku']}\n*Price:* Rs. " . number_format((float)$product['price'], 0)) ?>"
               target="_blank"
               rel="noopener"
               class="btn-whatsapp py-2.5 text-xs text-center flex items-center justify-center gap-1.5">
              <span>Order on WhatsApp</span>
            </a>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <!-- ============================================================================
       PRODUCT TABS: DESCRIPTION, VEHICLE FITMENT MATRIX & REVIEWS
       ============================================================================ -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-10 mb-12">
    
    <!-- Tab 1: Detailed Description -->
    <div>
      <h3 class="text-base font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-200 pb-2 mb-4">
        Product Description &amp; Technical Details
      </h3>
      <div class="prose max-w-none text-xs sm:text-sm text-slate-700 leading-relaxed space-y-3">
        <?= $product['description'] ?>
      </div>
    </div>

    <!-- Tab 2: Vehicle Compatibility Table -->
    <div>
      <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-4">
        <h3 class="text-base font-bold uppercase tracking-wider font-condensed text-slate-900">
          Confirmed Vehicle Compatibility (<?= count($product['fitment']) ?> Models)
        </h3>
        <span class="text-xs text-slate-500">100% Guaranteed Fitment</span>
      </div>

      <?php if (!empty($product['fitment'])): ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs border border-slate-200 rounded-lg overflow-hidden">
            <thead class="bg-slate-100 text-slate-700 uppercase font-condensed tracking-wider font-bold">
              <tr>
                <th class="py-2.5 px-4 border-b">Make</th>
                <th class="py-2.5 px-4 border-b">Model</th>
                <th class="py-2.5 px-4 border-b">Year Range</th>
                <th class="py-2.5 px-4 border-b">Engine / Fuel</th>
                <th class="py-2.5 px-4 border-b">Fitment Notes</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($product['fitment'] as $fit): ?>
                <tr class="hover:bg-slate-50">
                  <td class="py-2.5 px-4 font-bold text-slate-900"><?= e($fit['make']) ?></td>
                  <td class="py-2.5 px-4 text-slate-800"><?= e($fit['model']) ?></td>
                  <td class="py-2.5 px-4 font-mono"><?= (int)$fit['year_start'] ?> - <?= (int)$fit['year_end'] ?></td>
                  <td class="py-2.5 px-4 text-slate-600"><?= e($fit['engine']) ?> (<?= e($fit['fuel_type']) ?>)</td>
                  <td class="py-2.5 px-4 text-slate-500"><?= e($fit['notes'] ?? 'Direct factory fit') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-xs text-slate-500">Universal automotive fitment or check part OEM number with our WhatsApp team.</p>
      <?php endif; ?>
    </div>

    <!-- Tab 3: Cross References -->
    <?php if (!empty($product['cross_references'])): ?>
      <div>
        <h3 class="text-base font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-200 pb-2 mb-3">
          Cross-Reference &amp; Interchange Numbers
        </h3>
        <div class="flex flex-wrap gap-2 text-xs font-mono">
          <?php foreach (explode(',', (string)$product['cross_references']) as $code): ?>
            <span class="px-3 py-1 bg-slate-100 text-slate-800 rounded border border-slate-200"><?= e(trim($code)) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Tab 4: Customer Reviews -->
    <div id="reviews">
      <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-6">
        <h3 class="text-base font-bold uppercase tracking-wider font-condensed text-slate-900">
          Verified Customer Feedback (<?= count($reviews) ?>)
        </h3>
        <span class="text-xs font-bold text-amber-500">★ <?= number_format($ratingSummary['average'], 1) ?> / 5.0</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Reviews List -->
        <div class="md:col-span-7 space-y-4">
          <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $rev): ?>
              <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                <div class="flex items-center justify-between text-xs">
                  <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-900"><?= e($rev['customer_name']) ?></span>
                    <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.5 rounded">Verified Buyer</span>
                  </div>
                  <span class="text-slate-400"><?= \App\Helpers\Formatter::date($rev['created_at'], 'd M Y') ?></span>
                </div>
                <div class="text-amber-400 text-xs">
                  <?php for ($s = 1; $s <= 5; $s++): ?>
                    <span><?= $s <= (int)$rev['rating'] ? '★' : '☆' ?></span>
                  <?php endfor; ?>
                </div>
                <p class="text-xs text-slate-700 leading-relaxed"><?= e($rev['comment']) ?></p>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-xs text-slate-500">No reviews yet. Be the first mechanic or owner to review this part!</p>
          <?php endif; ?>
        </div>

        <!-- Write a Review Form -->
        <div class="md:col-span-5 bg-slate-50 p-5 rounded-xl border border-slate-200">
          <h4 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 mb-3">Leave a Review</h4>
          <form action="/reviews" method="POST" class="space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

            <div>
              <label class="form-label">Your Name</label>
              <input type="text" name="customer_name" class="form-input text-xs" required placeholder="e.g. Asad Khan">
            </div>

            <div>
              <label class="form-label">Mobile Number</label>
              <input type="text" name="customer_phone" class="form-input text-xs" placeholder="0300-1234567 (optional)">
            </div>

            <div>
              <label class="form-label">Rating</label>
              <select name="rating" class="form-input text-xs">
                <option value="5">★★★★★ (5 Stars - Excellent Fit)</option>
                <option value="4">★★★★☆ (4 Stars - Good Quality)</option>
                <option value="3">★★★☆☆ (3 Stars - Average)</option>
                <option value="2">★★☆☆☆ (2 Stars - Minor Issues)</option>
                <option value="1">★☆☆☆☆ (1 Star - Defective / Didn't Fit)</option>
              </select>
            </div>

            <div>
              <label class="form-label">Review Comment</label>
              <textarea name="comment" rows="3" class="form-input text-xs" required placeholder="Describe vehicle fitment, installation ease, and delivery experience..."></textarea>
            </div>

            <button type="submit" class="w-full btn-primary text-xs py-2">Submit Verified Review</button>
          </form>
        </div>
      </div>
    </div>

  </div>

  <!-- ============================================================================
       RELATED REPLACEMENT PARTS
       ============================================================================ -->
  <?php if (!empty($relatedItems)): ?>
    <section class="space-y-6">
      <h2 class="text-xl font-bold uppercase tracking-wider font-condensed text-slate-900">
        Related Replacement Parts
      </h2>
      <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-5">
        <?php foreach ($relatedItems as $item): ?>
          <?= \App\Core\View::partial('partials/product_card', ['product' => $item]) ?>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

</div>

