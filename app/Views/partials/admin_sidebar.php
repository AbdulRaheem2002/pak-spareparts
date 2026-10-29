<!-- Admin Panel Sidebar Navigation -->
<?php
  $currentUri = $_SERVER['REQUEST_URI'] ?? '/admin';
  $user = \App\Core\Auth::user();
  $role = $user['role'] ?? 'staff';
?>
<aside class="w-64 bg-chassis-950 text-slate-300 flex flex-col flex-shrink-0 border-r border-slate-800 min-h-screen">
  
  <!-- Sidebar Header / Logo -->
  <div class="h-16 flex items-center px-6 bg-slate-900 border-b border-slate-800">
    <a href="/admin" class="flex items-center gap-2">
      <span class="text-xl">⚙️</span>
      <span class="font-bold text-white tracking-wider uppercase font-condensed text-base">PakAuto <span class="text-brand-500">Admin</span></span>
    </a>
  </div>

  <!-- Navigation Links -->
  <div class="flex-1 py-4 px-3 space-y-1 overflow-y-auto text-xs font-semibold">
    
    <div class="px-3 py-1.5 text-[10px] uppercase font-bold text-slate-500 font-condensed tracking-wider">Main</div>
    
    <a href="/admin" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= $currentUri === '/admin' || $currentUri === '/admin/dashboard' ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">📊</span> Dashboard
    </a>

    <div class="pt-3 px-3 py-1.5 text-[10px] uppercase font-bold text-slate-500 font-condensed tracking-wider">Catalog &amp; Inventory</div>

    <a href="/admin/products" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/products') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">📦</span> Products
    </a>

    <a href="/admin/categories" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/categories') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">📁</span> Categories
    </a>

    <a href="/admin/brands" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/brands') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">🏷️</span> Brands
    </a>

    <a href="/admin/vehicles" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/vehicles') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">🚗</span> Vehicle Fitments
    </a>

    <div class="pt-3 px-3 py-1.5 text-[10px] uppercase font-bold text-slate-500 font-condensed tracking-wider">Sales &amp; Orders</div>

    <a href="/admin/orders" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/orders') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">📑</span> Orders
    </a>

    <a href="/admin/coupons" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/coupons') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">🎟️</span> Coupons &amp; Promos
    </a>

    <a href="/admin/customers" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/customers') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">👥</span> Customers
    </a>

    <div class="pt-3 px-3 py-1.5 text-[10px] uppercase font-bold text-slate-500 font-condensed tracking-wider">Engagement &amp; SEO</div>

    <a href="/admin/part-requests" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/part-requests') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">🔍</span> Part Requests
    </a>

    <a href="/admin/reviews" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/reviews') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">⭐</span> Reviews Moderation
    </a>

    <a href="/admin/blog" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/blog') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">✍️</span> Blog &amp; Guides
    </a>

    <div class="pt-3 px-3 py-1.5 text-[10px] uppercase font-bold text-slate-500 font-condensed tracking-wider">Reports &amp; Config</div>

    <a href="/admin/reports" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/reports') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">📉</span> Inventory &amp; Stock Report
    </a>

    <a href="/admin/settings" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors <?= str_starts_with($currentUri, '/admin/settings') ? 'bg-brand-500 text-white font-bold' : 'hover:bg-slate-800 text-slate-300' ?>">
      <span class="text-base">⚙️</span> Store Settings
    </a>

    <?php if ($role === 'owner'): ?>
      <a href="/admin/backup-db" class="flex items-center gap-3 px-3 py-2 rounded-lg text-emerald-400 hover:bg-slate-800 transition-colors">
        <span class="text-base">💾</span> One-Click DB Backup
      </a>
    <?php endif; ?>

  </div>

  <!-- Bottom User Profile Strip -->
  <div class="p-4 bg-slate-900 border-t border-slate-800 flex items-center justify-between text-xs">
    <div>
      <div class="font-bold text-white"><?= e($user['name'] ?? 'Admin') ?></div>
      <div class="text-[10px] text-brand-400 uppercase font-condensed tracking-wider"><?= e($user['role'] ?? 'Staff') ?></div>
    </div>
    <form action="/admin/logout" method="POST">
      <?= csrf_field() ?>
      <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-400 transition-colors" title="Log Out">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
        </svg>
      </button>
    </form>
  </div>

</aside>

