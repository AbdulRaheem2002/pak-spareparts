<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Page Not Found | PakAutoParts') ?></title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">
</head>
<body class="bg-slate-50 min-h-screen flex flex-col justify-between antialiased">
  <?= \App\Core\View::partial('partials/header') ?>

  <div class="max-w-xl mx-auto px-4 py-20 text-center">
    <div class="text-7xl font-extrabold text-brand-500 font-condensed tracking-tight mb-2">
      <?= e($code ?? 404) ?>
    </div>
    <h1 class="text-2xl font-bold text-slate-900 mb-3 font-condensed uppercase tracking-wider">
      <?= e($heading ?? 'Part or Page Not Found') ?>
    </h1>
    <p class="text-sm text-slate-600 mb-8 leading-relaxed">
      <?= e($message ?? 'The spare part or page you requested could not be found. It may have been relocated or renamed.') ?>
    </p>

    <!-- Search Box -->
    <form action="/parts" method="GET" class="mb-8">
      <div class="flex items-center gap-2 max-w-md mx-auto">
        <input type="text"
               name="q"
               value="<?= e($query ?? '') ?>"
               placeholder="Search by Part Name or OEM #..."
               class="form-input">
        <button type="submit" class="btn-primary">Search</button>
      </div>
    </form>

    <div class="flex items-center justify-center gap-4 text-xs font-semibold">
      <a href="/" class="btn-secondary">&larr; Return to Homepage</a>
      <a href="/request-part" class="btn-outline">Can't Find Your Part? Request It</a>
    </div>
  </div>

  <?= \App\Core\View::partial('partials/footer') ?>
</body>
</html>

