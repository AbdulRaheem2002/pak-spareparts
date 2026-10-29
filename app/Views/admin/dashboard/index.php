<!-- Admin Dashboard View -->
<div class="space-y-8">

  <!-- ============================================================================
       KPI STATS CARDS
       ============================================================================ -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
    
    <!-- Total Revenue -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-condensed">Total Realized Revenue</span>
        <div class="text-2xl font-extrabold text-slate-900 font-condensed mt-1">
          <?= format_pkr($totalRevenue) ?>
        </div>
        <span class="text-[10px] text-emerald-600 font-bold">✓ Excludes cancelled</span>
      </div>
      <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl border border-brand-200">
        💰
      </div>
    </div>

    <!-- Total Orders -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-condensed">Total Orders Placed</span>
        <div class="text-2xl font-extrabold text-slate-900 font-condensed mt-1">
          <?= (int)$totalOrders ?>
        </div>
        <span class="text-[10px] text-slate-500 font-medium">Nationwide customer orders</span>
      </div>
      <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-700 flex items-center justify-center text-xl border border-slate-200">
        📑
      </div>
    </div>

    <!-- Pending Dispatch -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-condensed">Pending Orders</span>
        <div class="text-2xl font-extrabold text-amber-600 font-condensed mt-1">
          <?= (int)$pendingOrders ?>
        </div>
        <span class="text-[10px] text-amber-700 font-semibold">Requires courier booking</span>
      </div>
      <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl border border-amber-200">
        ⏳
      </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
      <div>
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 font-condensed">Low Stock Items</span>
        <div class="text-2xl font-extrabold <?= $lowStockCount > 0 ? 'text-rose-600' : 'text-slate-900' ?> font-condensed mt-1">
          <?= (int)$lowStockCount ?>
        </div>
        <span class="text-[10px] text-rose-600 font-semibold">Below minimum threshold</span>
      </div>
      <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl border border-rose-200">
        ⚠️
      </div>
    </div>

  </div>

  <!-- ============================================================================
       CHART.JS VISUAL ANALYTICS
       ============================================================================ -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- 30-Day Revenue Trend (Line Chart) -->
    <div class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900">
          Daily Revenue Trend (Last 30 Days)
        </h3>
        <span class="text-[11px] text-slate-400">Values in PKR</span>
      </div>
      <div class="h-64">
        <canvas id="revenueChart"></canvas>
      </div>
    </div>

    <!-- Order Status Breakdown (Doughnut Chart) -->
    <div class="lg:col-span-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900">
          Order Status Ratio
        </h3>
        <span class="text-[11px] text-slate-400">All Time</span>
      </div>
      <div class="h-64 flex items-center justify-center">
        <canvas id="statusChart"></canvas>
      </div>
    </div>

  </div>

  <!-- ============================================================================
       TOP SELLING PRODUCTS & RECENT ORDERS
       ============================================================================ -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Top 5 Products Bar Chart -->
    <div class="lg:col-span-4 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900">
          Top 5 Best-Selling Parts
        </h3>
        <span class="text-[11px] text-slate-400">Units Sold</span>
      </div>
      <div class="h-64">
        <canvas id="topProductsChart"></canvas>
      </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900">
          Recent Customer Orders
        </h3>
        <a href="/admin/orders" class="text-xs font-bold text-brand-600 hover:text-brand-700 uppercase">
          View All Orders &rarr;
        </a>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
            <tr>
              <th class="py-2.5 px-3">Order ID</th>
              <th class="py-2.5 px-3">Customer</th>
              <th class="py-2.5 px-3">City</th>
              <th class="py-2.5 px-3">Amount</th>
              <th class="py-2.5 px-3">Status</th>
              <th class="py-2.5 px-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php foreach ($recentOrders as $ro): ?>
              <tr class="hover:bg-slate-50">
                <td class="py-2.5 px-3 font-mono font-bold text-brand-600">
                  <a href="/admin/orders/show/<?= (int)$ro['id'] ?>" class="hover:underline">
                    <?= e($ro['order_number']) ?>
                  </a>
                </td>
                <td class="py-2.5 px-3 font-semibold text-slate-900"><?= e($ro['customer_name']) ?></td>
                <td class="py-2.5 px-3 text-slate-600"><?= e($ro['delivery_city']) ?></td>
                <td class="py-2.5 px-3 font-bold text-slate-900"><?= format_pkr($ro['total']) ?></td>
                <td class="py-2.5 px-3">
                  <?= \App\Helpers\Formatter::orderStatusBadge($ro['status']) ?>
                </td>
                <td class="py-2.5 px-3 text-right">
                  <a href="/admin/orders/show/<?= (int)$ro['id'] ?>" class="text-slate-600 hover:text-brand-600 font-bold">
                    Manage &rarr;
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</div>

<!-- Render Chart.js Visualizations -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  
  // 1. Line Chart: 30-Day Revenue
  const revCtx = document.getElementById('revenueChart').getContext('2d');
  new Chart(revCtx, {
    type: 'line',
    data: {
      labels: <?= json_encode($chartDates) ?>,
      datasets: [{
        label: 'Daily Sales (PKR)',
        data: <?= json_encode($chartAmounts) ?>,
        borderColor: '#f97316',
        backgroundColor: 'rgba(249, 115, 22, 0.08)',
        borderWidth: 2,
        fill: true,
        tension: 0.35,
        pointRadius: 2,
        pointHoverRadius: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { font: { size: 10 } }
        },
        x: {
          ticks: { font: { size: 9 }, maxTicksLimit: 10 }
        }
      }
    }
  });

  // 2. Status Doughnut Chart
  <?php
    $statusLabels = array_column($statusDistribution, 'status');
    $statusCounts = array_column($statusDistribution, 'count');
    if (empty($statusLabels)) {
      $statusLabels = ['No Orders'];
      $statusCounts = [1];
    }
  ?>
  const statusCtx = document.getElementById('statusChart').getContext('2d');
  new Chart(statusCtx, {
    type: 'doughnut',
    data: {
      labels: <?= json_encode($statusLabels) ?>,
      datasets: [{
        data: <?= json_encode($statusCounts) ?>,
        backgroundColor: ['#f59e0b', '#3b82f6', '#8b5cf6', '#10b981', '#ef4444', '#64748b'],
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
      }
    }
  });

  // 3. Top Products Bar Chart
  <?php
    $topProdNames = array_column($topProducts, 'product_name');
    $topProdUnits = array_column($topProducts, 'units_sold');
    if (empty($topProdNames)) {
      $topProdNames = ['Demo Products'];
      $topProdUnits = [5];
    }
  ?>
  const topCtx = document.getElementById('topProductsChart').getContext('2d');
  new Chart(topCtx, {
    type: 'bar',
    data: {
      labels: <?= json_encode(array_map(fn($n) => substr($n, 0, 16) . '..', $topProdNames)) ?>,
      datasets: [{
        label: 'Units Sold',
        data: <?= json_encode($topProdUnits) ?>,
        backgroundColor: '#0f172a',
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { font: { size: 10 } } },
        x: { ticks: { font: { size: 9 } } }
      }
    }
  });

});
</script>

