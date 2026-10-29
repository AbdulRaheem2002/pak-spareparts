<!-- Admin Inventory Valuation & Low Stock Report -->
<div class="space-y-8">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">
        Inventory &amp; Stock Reports
      </h2>
      <p class="text-xs text-slate-500">Inventory asset valuation, re-order alerts, and fast-moving component analysis</p>
    </div>
  </div>

  <!-- Inventory Valuation Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <span class="text-[10px] font-bold uppercase text-slate-400 font-condensed">Catalog SKUs</span>
      <div class="text-2xl font-extrabold text-slate-900 font-condensed mt-1">
        <?= (int)($valuation['total_skus'] ?? 0) ?>
      </div>
      <span class="text-[10px] text-slate-500">Active product catalog items</span>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <span class="text-[10px] font-bold uppercase text-slate-400 font-condensed">Total Physical Units</span>
      <div class="text-2xl font-extrabold text-slate-900 font-condensed mt-1">
        <?= number_format((float)($valuation['total_units'] ?? 0)) ?>
      </div>
      <span class="text-[10px] text-slate-500">In Lahore &amp; Karachi stockrooms</span>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <span class="text-[10px] font-bold uppercase text-slate-400 font-condensed">Retail Asset Valuation</span>
      <div class="text-2xl font-extrabold text-brand-600 font-condensed mt-1">
        <?= format_pkr($valuation['retail_valuation'] ?? 0) ?>
      </div>
      <span class="text-[10px] text-slate-500">Based on active selling prices</span>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
      <span class="text-[10px] font-bold uppercase text-slate-400 font-condensed">Estimated Cost Value</span>
      <div class="text-2xl font-extrabold text-slate-900 font-condensed mt-1">
        <?= format_pkr($valuation['estimated_cost_valuation'] ?? 0) ?>
      </div>
      <span class="text-[10px] text-emerald-600 font-bold">Estimated procurement capital</span>
    </div>

  </div>

  <!-- Low Stock Alert Re-order Table -->
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-4 bg-rose-50 border-b border-rose-200 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-rose-600 text-lg">⚠️</span>
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-rose-900">
          Urgent Low Stock Re-Order List (<?= count($lowStockParts) ?> Items)
        </h3>
      </div>
      <span class="text-[10px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded">Action Required</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Part Description</th>
            <th class="py-3 px-4">OEM &amp; SKU</th>
            <th class="py-3 px-4">Category</th>
            <th class="py-3 px-4 text-center">Remaining</th>
            <th class="py-3 px-4 text-center">Threshold</th>
            <th class="py-3 px-4 text-right">Price</th>
            <th class="py-3 px-4 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($lowStockParts)): ?>
            <?php foreach ($lowStockParts as $lsp): ?>
              <tr class="hover:bg-slate-50">
                <td class="py-3 px-4 font-bold text-slate-900"><?= e($lsp['name']) ?></td>
                <td class="py-3 px-4 font-mono text-slate-500">
                  <span class="text-brand-600 font-bold"><?= e($lsp['oem_number']) ?></span> | <?= e($lsp['sku']) ?>
                </td>
                <td class="py-3 px-4 text-slate-600"><?= e($lsp['category_name']) ?></td>
                <td class="py-3 px-4 text-center">
                  <span class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded font-mono font-bold text-xs">
                    <?= (int)$lsp['stock_quantity'] ?> left
                  </span>
                </td>
                <td class="py-3 px-4 text-center font-mono text-slate-500"><?= (int)$lsp['low_stock_threshold'] ?></td>
                <td class="py-3 px-4 text-right font-bold text-slate-900"><?= format_pkr($lsp['price']) ?></td>
                <td class="py-3 px-4 text-right">
                  <a href="/admin/products/edit/<?= (int)$lsp['id'] ?>" class="text-xs font-bold text-brand-600 hover:underline">
                    Restock &rarr;
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="py-6 text-center text-slate-400">All inventory items are currently above safety thresholds.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Fast Moving Parts -->
  <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
    <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
      Fast-Moving High-Demand Components (Top 10)
    </h3>
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-2.5 px-3">Product Name</th>
            <th class="py-2.5 px-3">OEM / SKU</th>
            <th class="py-2.5 px-3 text-center">Units Sold</th>
            <th class="py-2.5 px-3 text-right">Total Generated (PKR)</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($fastMoving as $fm): ?>
            <tr>
              <td class="py-2.5 px-3 font-bold text-slate-900"><?= e($fm['product_name']) ?></td>
              <td class="py-2.5 px-3 font-mono text-slate-500"><?= e($fm['oem_number']) ?> / <?= e($fm['sku']) ?></td>
              <td class="py-2.5 px-3 text-center font-bold font-mono"><?= (int)$fm['total_qty_sold'] ?></td>
              <td class="py-2.5 px-3 text-right font-bold text-slate-900"><?= format_pkr($fm['total_revenue']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

