<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Admin Dashboard | PakAutoParts') ?></title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#0F172A">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Tailwind CSS Engine -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: { 500: '#f97316', 600: '#ea580c', 700: '#c2410c' },
            chassis: { 800: '#1e293b', 900: '#0f172a', 950: '#090d16' }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            condensed: ['Chakra Petch', 'sans-serif']
          }
        }
      }
    }
  </script>

  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">

  <!-- Quill 2 Editor (pinned CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

  <!-- Chart.js 4 (pinned CDN) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

  <!-- Alpine.js 3 -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
</head>
<body class="h-full flex antialiased text-slate-800">

  <!-- Admin Sidebar -->
  <?= \App\Core\View::partial('partials/admin_sidebar') ?>

  <!-- Main Content Wrapper -->
  <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
    
    <!-- Top Header Bar -->
    <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 sticky top-0 z-20 shadow-sm no-print">
      <div class="flex items-center gap-3">
        <h1 class="text-base font-bold text-slate-900 font-condensed uppercase tracking-wider">
          <?= e($page_title ?? 'Control Panel') ?>
        </h1>
      </div>

      <div class="flex items-center gap-4">
        <!-- Storefront Link -->
        <a href="/" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors">
          <span>🌐</span> View Storefront &rarr;
        </a>
      </div>
    </header>

    <!-- Flash Messages -->
    <?= \App\Core\View::partial('partials/flash') ?>

    <!-- Sub-view content -->
    <main class="flex-1 p-6 md:p-8">
      <?= $content ?>
    </main>

  </div>

</body>
</html>

