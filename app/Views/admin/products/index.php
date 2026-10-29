<!-- Admin Products List View -->
<div class="space-y-6">

  <!-- Header & Action Buttons -->
  <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">Spare Parts Inventory</h2>
      <p class="text-xs text-slate-500">Manage catalog, fitments, stock quantities, and wholesale pricing</p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <a href="/admin/products/export" class="btn-outline text-xs py-2">
        📥 Export CSV
      </a>
      <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')" class="btn-outline text-xs py-2">
        📤 Import CSV
      </button>
      <a href="/admin/products/create" class="btn-primary text-xs py-2">
        + Add New Part &rarr;
      </a>
    </div>
  </div>

  <!-- Search & Filter Toolbar -->
  <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
    <form action="/admin/products" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
      <div class="sm:col-span-6">
        <input type="text"
               name="q"
               value="<?= e($q) ?>"
               placeholder="Search Part Name, SKU, or OEM Number..."
               class="form-input text-xs">
      </div>

      <div class="sm:col-span-3">
        <select name="category_id" class="form-input text-xs" onchange="this.form.submit()">
          <option value="">-- All Categories --</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>" <?= $catId == $cat['id'] ? 'selected' : '' ?>>
              <?= e($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="sm:col-span-3 flex items-center gap-2">
        <label class="flex items-center gap-1.5 text-xs font-semibold text-slate-700 cursor-pointer">
          <input type="checkbox" name="low_stock" value="1" <?= $lowStock ? 'checked' : '' ?> onchange="this.form.submit()" class="rounded text-brand-500">
          <span>Low Stock Only</span>
        </label>
        <button type="submit" class="btn-primary text-xs py-2 px-3 ml-auto">Filter</button>
      </div>
    </form>
  </div>

  <!-- Product Inventory Table -->
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Part</th>
            <th class="py-3 px-4">OEM &amp; SKU</th>
            <th class="py-3 px-4">Category</th>
            <th class="py-3 px-4">Brand</th>
            <th class="py-3 px-4">Condition</th>
            <th class="py-3 px-4 text-right">Price (PKR)</th>
            <th class="py-3 px-4 text-center">Stock</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
              <?php $isLow = (int)$p['stock_quantity'] <= (int)$p['low_stock_threshold']; ?>
              <tr class="hover:bg-slate-50">
                <td class="py-3 px-4">
                  <div class="flex items-center gap-3">
                    <img src="<?= e($p['thumbnail'] ?: '/assets/images/placeholder.svg') ?>" class="w-10 h-10 object-contain rounded border border-slate-200 bg-slate-50 p-1 flex-shrink-0" alt="Part">
                    <div class="font-bold text-slate-900 line-clamp-1 max-w-[200px]" title="<?= e($p['name']) ?>">
                      <a href="/admin/products/edit/<?= (int)$p['id'] ?>" class="hover:text-brand-600">
                        <?= e($p['name']) ?>
                      </a>
                    </div>
                  </div>
                </td>
                <td class="py-3 px-4 font-mono">
                  <div class="font-bold text-brand-600"><?= e($p['oem_number']) ?></div>
                  <div class="text-[10px] text-slate-400">SKU: <?= e($p['sku']) ?></div>
                </td>
                <td class="py-3 px-4 text-slate-700"><?= e($p['category_name']) ?></td>
                <td class="py-3 px-4 font-semibold text-slate-800"><?= e($p['brand_name']) ?></td>
                <td class="py-3 px-4">
                  <?= \App\Helpers\Formatter::conditionBadge($p['condition_type'] ?? 'Genuine') ?>
                </td>
                <td class="py-3 px-4 text-right font-bold text-slate-900 font-condensed">
                  <?= format_pkr($p['price']) ?>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold font-mono <?= $isLow ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-slate-100 text-slate-800' ?>">
                    <?= (int)$p['stock_quantity'] ?>
                  </span>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-2">
                    <a href="/admin/products/edit/<?= (int)$p['id'] ?>" class="p-1 text-slate-600 hover:text-brand-600" title="Edit Part">
                      ✏️
                    </a>
                    <form action="/admin/products/delete/<?= (int)$p['id'] ?>" method="POST" onsubmit="return confirm('Permanently delete this spare part?');" class="inline">
                      <?= csrf_field() ?>
                      <button type="submit" class="p-1 text-slate-400 hover:text-rose-600" title="Delete Part">
                        🗑️
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="py-8 text-center text-slate-400">No parts found matching your criteria.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($lastPage > 1): ?>
      <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
        <div>Showing Page <?= $page ?> of <?= $lastPage ?> (<?= $total ?> items total)</div>
        <div class="flex gap-1.5">
          <?php for ($i = 1; $i <= $lastPage; $i++): ?>
            <a href="?page=<?= $i ?>&q=<?= urlencode($q) ?>&category_id=<?= urlencode((string)$catId) ?>&low_stock=<?= urlencode((string)$lowStock) ?>"
               class="px-2.5 py-1 rounded border <?= $i === $page ? 'bg-brand-500 text-white font-bold border-brand-500' : 'bg-white text-slate-700 border-slate-200' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

</div>

<!-- Bulk CSV Import Modal -->
<div id="import-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
  <div class="flex items-center justify-center min-h-screen px-4">
    <div class="fixed inset-0 bg-slate-900/60" onclick="document.getElementById('import-modal').classList.add('hidden')"></div>
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full relative z-10 border border-slate-200">
      <h3 class="text-sm font-bold uppercase tracking-wider font-condensed text-slate-900 mb-2">Import Spare Parts via CSV</h3>
      <p class="text-xs text-slate-500 mb-4">Upload a CSV file containing columns: ID, Name, SKU, OEM, CrossRefs, Brand, Category, Condition, Price, ComparePrice, CostPrice, Stock, Weight.</p>
      
      <form action="/admin/products/import" method="POST" enctype="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>
        <div>
          <input type="file" name="csv_file" accept=".csv" required class="form-input text-xs py-1.5">
        </div>
        <div class="flex justify-end gap-2">
          <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="btn-outline text-xs">Cancel</button>
          <button type="submit" class="btn-primary text-xs">Upload &amp; Import</button>
        </div>
      </form>
    </div>
  </div>
</div>

