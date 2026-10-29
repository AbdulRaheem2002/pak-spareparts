<!-- Catalog Listing Page -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

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

  <!-- Active "My Garage" Filter Banner -->
  <?php if (!empty($activeVehicle)): ?>
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between shadow-sm">
      <div class="flex items-center gap-3">
        <span class="text-2xl text-emerald-600">🚗</span>
        <div>
          <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 font-condensed">Filtering for Your Vehicle</div>
          <div class="text-sm font-bold text-slate-900">
            <?= e($activeVehicle['make']) ?> <?= e($activeVehicle['model']) ?> (<?= (int)$activeVehicle['year_start'] ?>-<?= (int)$activeVehicle['year_end'] ?>) <?= e($activeVehicle['engine']) ?>
          </div>
        </div>
      </div>
      <button type="button" onclick="Garage.clear(); window.location.href = window.location.pathname;" class="text-xs font-semibold text-rose-600 hover:text-rose-800 underline">
        Clear Filter &times;
      </button>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

    <!-- ==========================================================================
         FILTER SIDEBAR (Desktop & Mobile Drawer)
         ========================================================================== -->
    <aside class="lg:col-span-3 bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-6">
      
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-sm font-bold uppercase tracking-wider font-condensed text-slate-900">Filters</h3>
        <a href="/parts" class="text-xs text-brand-600 hover:text-brand-800 font-semibold">Reset All</a>
      </div>

      <form action="/parts" method="GET" id="catalog-filter-form">
        <!-- Preserve Search Query -->
        <?php if (!empty($filters['q'])): ?>
          <input type="hidden" name="q" value="<?= e($filters['q']) ?>">
        <?php endif; ?>
        <?php if (!empty($filters['vehicle_id'])): ?>
          <input type="hidden" name="vehicle_id" value="<?= (int)$filters['vehicle_id'] ?>">
        <?php endif; ?>

        <!-- Categories -->
        <div class="border-b border-slate-100 pb-5 mb-5">
          <label class="form-label mb-2.5">Category</label>
          <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
            <?php foreach ($categories as $cat): ?>
              <label class="flex items-center justify-between text-xs text-slate-700 hover:text-brand-600 cursor-pointer py-0.5">
                <span class="flex items-center gap-2">
                  <input type="radio"
                         name="category_id"
                         value="<?= (int)$cat['id'] ?>"
                         <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'checked' : '' ?>
                         onchange="this.form.submit()"
                         class="text-brand-500 focus:ring-brand-500">
                  <span><?= e($cat['name']) ?></span>
                </span>
                <span class="text-[10px] text-slate-400 font-mono"><?= (int)$cat['product_count'] ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Brands -->
        <div class="border-b border-slate-100 pb-5 mb-5">
          <label class="form-label mb-2.5">Brand</label>
          <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
            <?php foreach ($brands as $b): ?>
              <label class="flex items-center justify-between text-xs text-slate-700 hover:text-brand-600 cursor-pointer py-0.5">
                <span class="flex items-center gap-2">
                  <input type="radio"
                         name="brand_id"
                         value="<?= (int)$b['id'] ?>"
                         <?= ($filters['brand_id'] ?? '') == $b['id'] ? 'checked' : '' ?>
                         onchange="this.form.submit()"
                         class="text-brand-500 focus:ring-brand-500">
                  <span><?= e($b['name']) ?></span>
                </span>
                <span class="text-[10px] text-slate-400 font-mono"><?= (int)$b['product_count'] ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Condition -->
        <div class="border-b border-slate-100 pb-5 mb-5">
          <label class="form-label mb-2.5">Condition</label>
          <div class="space-y-1.5">
            <?php foreach (['Genuine', 'OEM', 'Aftermarket', 'Used'] as $cond): ?>
              <label class="flex items-center gap-2 text-xs text-slate-700 hover:text-brand-600 cursor-pointer py-0.5">
                <input type="radio"
                       name="condition"
                       value="<?= e($cond) ?>"
                       <?= ($filters['condition'] ?? '') === $cond ? 'checked' : '' ?>
                       onchange="this.form.submit()"
                       class="text-brand-500 focus:ring-brand-500">
                <span><?= e($cond) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Price Range -->
        <div class="border-b border-slate-100 pb-5 mb-5">
          <label class="form-label mb-2.5">Price Range (PKR)</label>
          <div class="grid grid-cols-2 gap-2 mb-3">
            <input type="number"
                   name="min_price"
                   value="<?= e($filters['min_price'] ?? '') ?>"
                   placeholder="Min"
                   class="form-input text-xs py-1.5">
            <input type="number"
                   name="max_price"
                   value="<?= e($filters['max_price'] ?? '') ?>"
                   placeholder="Max"
                   class="form-input text-xs py-1.5">
          </div>
          <button type="submit" class="w-full btn-outline text-xs py-1.5">Apply Price</button>
        </div>

        <!-- In-Stock Only -->
        <div>
          <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
            <input type="checkbox"
                   name="in_stock"
                   value="1"
                   <?= !empty($filters['in_stock']) ? 'checked' : '' ?>
                   onchange="this.form.submit()"
                   class="rounded text-brand-500 focus:ring-brand-500">
            <span>In-Stock Only</span>
          </label>
        </div>

      </form>
    </aside>

    <!-- ==========================================================================
         CATALOG PRODUCT GRID
         ========================================================================== -->
    <div class="lg:col-span-9 space-y-6">

      <!-- Top Toolbar: Count & Sort -->
      <div class="bg-white p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
        <div class="text-xs text-slate-600 font-medium">
          Showing <span class="font-bold text-slate-900"><?= (int)$catalog['total'] ?></span> parts matching your criteria
        </div>

        <!-- Sorting Selector -->
        <div class="flex items-center gap-2">
          <label for="catalog-sort" class="text-xs font-semibold uppercase text-slate-500 font-condensed">Sort By:</label>
          <select id="catalog-sort"
                  onchange="location.href = updateQueryParam('sort', this.value)"
                  class="form-input text-xs py-1.5 pr-8">
            <option value="default" <?= ($filters['sort'] ?? '') === 'default' ? 'selected' : '' ?>>Featured / Best Match</option>
            <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="newest" <?= ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
          </select>
        </div>
      </div>

      <!-- Products Grid -->
      <?php if (!empty($catalog['data'])): ?>
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-5">
          <?php foreach ($catalog['data'] as $part): ?>
            <?= \App\Core\View::partial('partials/product_card', ['product' => $part]) ?>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($catalog['last_page'] > 1): ?>
          <div class="flex items-center justify-center gap-2 pt-8">
            <?php for ($i = 1; $i <= $catalog['last_page']; $i++): ?>
              <a href="<?= updateQueryParam('page', (string)$i) ?>"
                 class="px-3.5 py-2 rounded-lg text-xs font-bold font-condensed transition-colors <?= $i === $catalog['current_page'] ? 'bg-brand-500 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' ?>">
                <?= $i ?>
              </a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>

      <?php else: ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl border border-dashed border-slate-300 p-12 text-center space-y-4">
          <div class="text-5xl">🔍</div>
          <h3 class="text-base font-bold uppercase tracking-wider font-condensed text-slate-900">
            No Compatible Parts Found
          </h3>
          <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
            We couldn't find any spare parts matching your exact filters or vehicle model.
          </p>
          <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="/parts" class="btn-outline text-xs">Clear All Filters</a>
            <a href="/request-part<?= !empty($filters['q']) ? '?part_name=' . urlencode($filters['q']) : '' ?>" class="btn-primary text-xs">
              Can't Find Your Part? Request Sourcing &rarr;
            </a>
          </div>
        </div>
      <?php endif; ?>

    </div>

  </div>
</div>

<script>
function updateQueryParam(param, value) {
  const url = new URL(window.location.href);
  url.searchParams.set(param, value);
  return url.toString();
}
</script>

