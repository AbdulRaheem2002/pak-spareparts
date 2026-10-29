<!-- Product Card Component -->
<?php
  /** @var array $product */
  $primaryImg = !empty($product['primary_image']) ? $product['primary_image'] : '/assets/images/placeholder.svg';
  $isOutOfStock = (int)$product['stock_quantity'] <= 0;
  $hasDiscount = !empty($product['compare_at_price']) && (float)$product['compare_at_price'] > (float)$product['price'];
  $discountPercent = $hasDiscount ? round((((float)$product['compare_at_price'] - (float)$product['price']) / (float)$product['compare_at_price']) * 100) : 0;
?>

<div class="product-card group relative bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
  
  <div>
    <!-- Card Top Badge Strip -->
    <div class="absolute top-2.5 left-2.5 right-2.5 z-10 flex items-center justify-between pointer-events-none">
      <div>
        <?= \App\Helpers\Formatter::conditionBadge($product['condition_type'] ?? 'Genuine') ?>
      </div>
      <?php if ($hasDiscount): ?>
        <span class="bg-rose-600 text-white text-[10px] font-black uppercase px-2 py-0.5 rounded tracking-wider shadow-sm font-condensed">
          -<?= $discountPercent ?>%
        </span>
      <?php endif; ?>
    </div>

    <!-- Product Image with Zoom Hover -->
    <a href="/product/<?= e($product['slug']) ?>" class="product-card-img-wrap block relative aspect-square bg-slate-50 overflow-hidden border-b border-slate-100 flex items-center justify-center p-4">
      <img src="<?= e($primaryImg) ?>"
           alt="<?= e($product['name']) ?>"
           loading="lazy"
           width="320"
           height="320"
           class="max-h-[175px] w-auto max-w-full object-contain group-hover:scale-105 transition-transform duration-300">
    </a>

    <!-- Details Body -->
    <div class="p-3.5 sm:p-4">
      <!-- Brand & SKU -->
      <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
        <span class="font-bold uppercase tracking-wider text-[11px] text-slate-700 font-condensed"><?= e($product['brand_name'] ?? 'Genuine OEM') ?></span>
        <span class="font-mono text-[10px] text-slate-400">SKU: <?= e($product['sku']) ?></span>
      </div>

      <!-- OEM Number -->
      <div class="text-[11px] font-mono font-bold text-orange-600 mb-1.5 truncate" title="OEM: <?= e($product['oem_number']) ?>">
        OEM: <?= e($product['oem_number']) ?>
      </div>

      <!-- Product Title -->
      <h3 class="text-sm font-bold text-slate-900 group-hover:text-orange-600 transition-colors line-clamp-2 leading-snug mb-2 min-h-[2.5rem]">
        <a href="/product/<?= e($product['slug']) ?>">
          <?= e($product['name']) ?>
        </a>
      </h3>

      <!-- Fitment Indicator Pill -->
      <?php if (!empty($product['fits_active_vehicle'])): ?>
        <div class="mb-2.5 inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded text-[11px] font-bold">
          <span style="font-size: 10px;">✓</span> Fits your vehicle
        </div>
      <?php else: ?>
        <div class="mb-2.5 inline-flex items-center gap-1.5 text-[11px] text-slate-500 font-medium">
          <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
          <span>In Stock — Fast Dispatch</span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Card Footer: Pricing & Action -->
  <div class="p-3.5 sm:p-4 pt-0">
    <div class="flex items-baseline gap-2 mb-3">
      <span class="text-lg font-black text-slate-900 font-condensed tracking-tight">
        <?= format_pkr($product['price']) ?>
      </span>
      <?php if ($hasDiscount): ?>
        <span class="text-xs text-slate-400 line-through">
          <?= format_pkr($product['compare_at_price']) ?>
        </span>
      <?php endif; ?>
    </div>

    <!-- Stock and Add to Cart -->
    <?php if ($isOutOfStock): ?>
      <button type="button"
              onclick="window.location.href='/product/<?= e($product['slug']) ?>#notify'"
              class="w-full py-2 bg-slate-100 text-slate-500 font-bold text-xs rounded-lg uppercase tracking-wider cursor-pointer hover:bg-slate-200 transition-colors font-condensed">
        Out of Stock (Notify Me)
      </button>
    <?php else: ?>
      <button type="button"
              onclick="Cart.add(<?= (int)$product['id'] ?>, 1)"
              class="w-full py-2.5 px-3 bg-[#f97316] hover:bg-[#ea580c] active:bg-[#c2410c] text-white font-condensed font-bold text-xs uppercase tracking-wider rounded-lg transition-all shadow-sm flex items-center justify-center gap-2"
              style="background-color: #f97316 !important; color: #ffffff !important;">
        <svg style="width: 15px !important; height: 15px !important; min-width: 15px !important; min-height: 15px !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Add to Cart</span>
      </button>
    <?php endif; ?>
  </div>

</div>
