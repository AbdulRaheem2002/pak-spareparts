<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Sign In | PakAutoParts</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">
</head>
<body class="h-full flex items-center justify-center p-4 antialiased">

  <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl border border-slate-700 overflow-hidden">
    
    <!-- Top Brand Header -->
    <div class="bg-chassis-950 p-6 text-center border-b border-slate-800">
      <div class="w-12 h-12 bg-brand-500/10 text-brand-500 rounded-xl flex items-center justify-center text-2xl mx-auto mb-3 border border-brand-500/20">
        ⚙️
      </div>
      <h1 class="text-lg font-bold uppercase font-condensed tracking-wider text-white">
        PakAutoParts Control Panel
      </h1>
      <p class="text-xs text-slate-400 mt-1">Authorized Store Staff &amp; Management Access</p>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($flash_error)): ?>
      <div class="p-3 bg-rose-50 border-b border-rose-200 text-rose-700 text-xs font-semibold px-6">
        ⚠ <?= e($flash_error) ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($flash_info)): ?>
      <div class="p-3 bg-sky-50 border-b border-sky-200 text-sky-700 text-xs font-semibold px-6">
        ℹ <?= e($flash_info) ?>
      </div>
    <?php endif; ?>

    <div class="p-6 sm:p-8">
      <form action="/admin/login" method="POST" class="space-y-4">
        <?= csrf_field() ?>

        <div>
          <label class="form-label">Admin Email Address</label>
          <input type="email"
                 name="email"
                 required
                 autofocus
                 placeholder="admin@pakautoparts.pk"
                 class="form-input text-xs">
        </div>

        <div>
          <label class="form-label">Password</label>
          <input type="password"
                 name="password"
                 required
                 placeholder="••••••••••••"
                 class="form-input text-xs">
        </div>

        <button type="submit" class="w-full btn-primary py-2.5 text-xs shadow-md mt-2">
          Sign In to Control Panel &rarr;
        </button>
      </form>

      <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-400">
        Default Seed Login: <strong class="text-slate-600">admin@pakautoparts.pk</strong> / <strong class="text-slate-600">Admin@12345</strong>
      </div>
    </div>

  </div>

</body>
</html>

