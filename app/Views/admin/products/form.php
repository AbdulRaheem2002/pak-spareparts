<!-- Admin Product Create/Edit Form -->
<?php
  $isEdit = !empty($product);
  $action = $isEdit ? "/admin/products/update/{$product['id']}" : "/admin/products/store";
?>

<div class="space-y-6" x-data="productForm()">

  <div class="flex items-center justify-between border-b border-slate-200 pb-4">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">
        <?= $isEdit ? 'Edit Spare Part' : 'Add New Spare Part' ?>
      </h2>
      <p class="text-xs text-slate-500">Configure catalog information, fitment matrix, pricing tiers and images</p>
    </div>
    <a href="/admin/products" class="btn-outline text-xs">
      &larr; Back to Inventory
    </a>
  </div>

  <form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="space-y-8" id="product-form">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

      <!-- LEFT: MAIN SPECS & DESCRIPTION (8 Cols) -->
      <div class="lg:col-span-8 space-y-6">

        <!-- Basic Identifiers -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Part Identity &amp; Codes
          </h3>

          <div class="space-y-4">
            <div>
              <label class="form-label">Part Name *</label>
              <input type="text"
                     name="name"
                     value="<?= e($product['name'] ?? '') ?>"
                     required
                     placeholder="e.g. Atlas Honda Genuine Clutch Plate Set for CG125"
                     class="form-input text-xs font-semibold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label">SKU (Stock Keeping Unit) *</label>
                <input type="text"
                       name="sku"
                       value="<?= e($product['sku'] ?? '') ?>"
                       required
                       placeholder="AHL-CLT-125"
                       class="form-input text-xs font-mono uppercase">
              </div>

              <div>
                <label class="form-label">OEM / Manufacturer Part Number *</label>
                <input type="text"
                       name="oem_number"
                       value="<?= e($product['oem_number'] ?? '') ?>"
                       required
                       placeholder="22201-107-000"
                       class="form-input text-xs font-mono uppercase">
              </div>
            </div>

            <div>
              <label class="form-label">Cross-Reference / Alternate Numbers (Comma Separated)</label>
              <input type="text"
                     name="cross_references"
                     value="<?= e($product['cross_references'] ?? '') ?>"
                     placeholder="FCC-CG125, 22201-KCS-920"
                     class="form-input text-xs font-mono">
            </div>

            <?php if ($isEdit): ?>
              <div>
                <label class="form-label">URL Slug (Changing this automatically creates a 301 Redirect)</label>
                <input type="text" name="slug" value="<?= e($product['slug']) ?>" class="form-input text-xs font-mono">
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Description (Quill Rich-Text Editor) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Detailed Description &amp; Installation Notes
          </h3>

          <div>
            <label class="form-label">Short Summary (Shown in search snippets)</label>
            <input type="text"
                   name="short_description"
                   value="<?= e($product['short_description'] ?? '') ?>"
                   placeholder="Brief 1-line overview of materials and benefits"
                   class="form-input text-xs">
          </div>

          <div>
            <label class="form-label">Full Technical Description</label>
            <!-- Hidden input for form submission -->
            <input type="hidden" name="description" id="hidden-description" value="<?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div id="quill-editor" class="h-64 bg-white rounded-b-lg border border-slate-300">
              <?= $product['description'] ?? '' ?>
            </div>
          </div>
        </div>

        <!-- Vehicle Fitment Multi-Select Matrix -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900">
              Vehicle Compatibility Matrix (Check all vehicles that this part fits)
            </h3>
            <span class="text-[10px] text-slate-400">Total models in DB: <?= count($vehicles) ?></span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 max-h-64 overflow-y-auto p-1 border border-slate-100 rounded-lg">
            <?php foreach ($vehicles as $v): ?>
              <?php $checked = in_array((int)$v['id'], array_map('intval', $fitmentIds), true); ?>
              <label class="flex items-start gap-2 p-2 rounded border border-slate-200 hover:bg-slate-50 cursor-pointer text-xs">
                <input type="checkbox"
                       name="vehicle_ids[]"
                       value="<?= (int)$v['id'] ?>"
                       <?= $checked ? 'checked' : '' ?>
                       class="mt-0.5 rounded text-brand-500">
                <div class="min-w-0">
                  <div class="font-bold text-slate-900"><?= e($v['make']) ?> <?= e($v['model']) ?></div>
                  <div class="text-[10px] text-slate-500 font-mono"><?= (int)$v['year_start'] ?>-<?= (int)$v['year_end'] ?> (<?= e($v['engine']) ?>)</div>
                </div>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Wholesale Volume Price Tiers -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900">
              Wholesale Pricing Tiers (For Workshops &amp; Mechanics)
            </h3>
            <button type="button" @click="addTier()" class="btn-outline py-1 px-2.5 text-[11px]">
              + Add Tier
            </button>
          </div>

          <div class="space-y-2">
            <template x-for="(tier, idx) in tiers" :key="idx">
              <div class="flex items-center gap-3">
                <div class="w-1/2">
                  <label class="text-[10px] font-bold uppercase text-slate-500 font-condensed">Min Qty</label>
                  <input type="number" name="tier_min_qty[]" x-model="tier.min_qty" placeholder="e.g. 5" class="form-input text-xs py-1.5" min="2">
                </div>
                <div class="w-1/2">
                  <label class="text-[10px] font-bold uppercase text-slate-500 font-condensed">Unit Price (PKR)</label>
                  <input type="number" name="tier_price[]" x-model="tier.unit_price" placeholder="e.g. 1500" class="form-input text-xs py-1.5" min="1">
                </div>
                <button type="button" @click="removeTier(idx)" class="text-rose-500 hover:text-rose-700 text-lg font-bold self-end mb-1">&times;</button>
              </div>
            </template>
          </div>
        </div>

        <!-- SEO Box -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Search Engine Optimization (SEO)
          </h3>

          <div class="space-y-3">
            <div>
              <label class="form-label">Meta Title (Target: [part name] price in Pakistan)</label>
              <input type="text" name="meta_title" value="<?= e($product['meta_title'] ?? '') ?>" placeholder="e.g. Toyota Corolla Oil Filter 04152-YZZA6 Price in Pakistan | PakAutoParts" class="form-input text-xs">
            </div>

            <div>
              <label class="form-label">Meta Description</label>
              <textarea name="meta_description" rows="2" class="form-input text-xs"><?= e($product['meta_description'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

      </div>

      <!-- RIGHT: CATEGORIZATION, PRICING, STOCK & MEDIA (4 Cols) -->
      <div class="lg:col-span-4 space-y-6">

        <!-- Publish & Stock Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Publish &amp; Inventory Status
          </h3>

          <div class="space-y-3">
            <div>
              <label class="form-label">Current Stock Quantity *</label>
              <input type="number" name="stock_quantity" value="<?= (int)($product['stock_quantity'] ?? 10) ?>" required class="form-input text-xs font-mono font-bold">
            </div>

            <div>
              <label class="form-label">Low Stock Alert Threshold</label>
              <input type="number" name="low_stock_threshold" value="<?= (int)($product['low_stock_threshold'] ?? 3) ?>" class="form-input text-xs font-mono">
            </div>

            <div class="pt-2 space-y-2">
              <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" <?= (!isset($product) || !empty($product['is_active'])) ? 'checked' : '' ?> class="rounded text-brand-500">
                <span>Active on Storefront</span>
              </label>

              <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?> class="rounded text-brand-500">
                <span>Featured / Homepage Showcase</span>
              </label>
            </div>

            <div class="pt-4 border-t border-slate-100">
              <button type="submit" class="w-full btn-primary py-3 text-xs shadow-md">
                <?= $isEdit ? 'Save Changes' : 'Publish Part to Catalog' ?> &rarr;
              </button>
            </div>
          </div>
        </div>

        <!-- Pricing Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Pricing (PKR)
          </h3>

          <div class="space-y-3">
            <div>
              <label class="form-label">Retail Selling Price (PKR) *</label>
              <input type="number" step="1" name="price" value="<?= e($product['price'] ?? '') ?>" required class="form-input text-xs font-mono font-bold">
            </div>

            <div>
              <label class="form-label">Compare-at Price (For Strikethrough Discount)</label>
              <input type="number" step="1" name="compare_at_price" value="<?= e($product['compare_at_price'] ?? '') ?>" placeholder="e.g. 2500" class="form-input text-xs font-mono">
            </div>

            <div>
              <label class="form-label">Cost Price (Internal profit tracking)</label>
              <input type="number" step="1" name="cost_price" value="<?= e($product['cost_price'] ?? '') ?>" placeholder="e.g. 1400" class="form-input text-xs font-mono">
            </div>
          </div>
        </div>

        <!-- Category & Brand -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Category &amp; Brand
          </h3>

          <div class="space-y-3">
            <div>
              <label class="form-label">Category *</label>
              <select name="category_id" required class="form-input text-xs">
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= (int)$cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="form-label">Manufacturer Brand *</label>
              <select name="brand_id" required class="form-input text-xs">
                <?php foreach ($brands as $b): ?>
                  <option value="<?= (int)$b['id'] ?>" <?= ($product['brand_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                    <?= e($b['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="form-label">Part Condition</label>
              <select name="condition_type" class="form-input text-xs">
                <?php foreach (['Genuine', 'OEM', 'Aftermarket', 'Used'] as $cond): ?>
                  <option value="<?= e($cond) ?>" <?= ($product['condition_type'] ?? 'Genuine') === $cond ? 'selected' : '' ?>>
                    <?= e($cond) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="form-label">Warranty Description</label>
              <input type="text" name="warranty" value="<?= e($product['warranty'] ?? '7-Day Return / 1-Month Warranty') ?>" class="form-input text-xs">
            </div>

            <div>
              <label class="form-label">Weight in KG (For shipping calculator)</label>
              <input type="number" step="0.05" name="weight_kg" value="<?= e($product['weight_kg'] ?? '1.00') ?>" class="form-input text-xs font-mono">
            </div>
          </div>
        </div>

        <!-- Product Images & Uploader -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Product Images
          </h3>

          <div>
            <label class="form-label">Upload New Photos (JPG, PNG, WebP)</label>
            <input type="file" name="images[]" multiple accept="image/*" class="form-input text-xs py-1.5">
            <span class="text-[10px] text-slate-400 mt-1 block">Photos are automatically converted to optimized WebP format.</span>
          </div>

          <!-- Existing Images Gallery -->
          <?php if (!empty($images)): ?>
            <div class="pt-3 border-t border-slate-100 space-y-2">
              <label class="form-label">Existing Images</label>
              <div class="grid grid-cols-3 gap-2">
                <?php foreach ($images as $img): ?>
                  <div class="relative group aspect-square rounded-lg border border-slate-200 overflow-hidden bg-slate-50 p-1">
                    <img src="<?= e($img['thumbnail_url'] ?: $img['image_url']) ?>" alt="Thumbnail" class="w-full h-full object-contain">
                    <button type="submit"
                            formaction="/admin/products/delete-image/<?= (int)$img['id'] ?>"
                            onclick="return confirm('Delete this image?');"
                            class="absolute top-1 right-1 bg-rose-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                      &times;
                    </button>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>

      </div>

    </div>
  </form>

</div>

<script>
function productForm() {
  return {
    tiers: <?= json_encode(!empty($tiers) ? array_map(fn($t) => ['min_qty' => (int)$t['min_qty'], 'unit_price' => (float)$t['unit_price']], $tiers) : []) ?>,
    addTier: function() {
      this.tiers.push({ min_qty: 3, unit_price: '' });
    },
    removeTier: function(idx) {
      this.tiers.splice(idx, 1);
    }
  };
}

// Initialize Quill Editor
document.addEventListener('DOMContentLoaded', function() {
  const quill = new Quill('#quill-editor', {
    theme: 'snow',
    placeholder: 'Write detailed specifications, dimensions, materials, and vehicle installation instructions...',
    modules: {
      toolbar: [
        [{ 'header': [2, 3, 4, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link', 'clean']
      ]
    }
  });

  const form = document.getElementById('product-form');
  const hiddenDesc = document.getElementById('hidden-description');

  form.addEventListener('submit', function() {
    hiddenDesc.value = quill.root.innerHTML;
  });
});
</script>

