<!-- Vehicle Landing Page (SEO /parts/{make}/{model}/{year}) -->
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

  <!-- Vehicle Hero Showcase Banner -->
  <div class="bg-gradient-to-r from-chassis-950 to-chassis-900 text-white rounded-2xl p-6 sm:p-8 mb-8 border border-slate-800 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
    <div class="space-y-2 text-center md:text-left">
      <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-brand-500/20 text-brand-400 border border-brand-500/30 rounded-full text-xs font-bold uppercase font-condensed">
        <span>🚗</span> 100% Fitment Guaranteed
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold uppercase font-condensed tracking-tight">
        <?= e($make) ?> <?= e($model) ?> (<?= e($year) ?>) Spare Parts Price in Pakistan
      </h1>
      <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
        Browse certified genuine and OEM replacement components specifically matched to your <?= e($make) ?> <?= e($model) ?>. Sourced from authorized Japanese &amp; OEM distributors with nationwide Cash on Delivery.
      </p>
    </div>

    <!-- Quick "Save to Garage" Button -->
    <?php if ($vehicle): ?>
      <button type="button"
              onclick="Garage.set(<?= htmlspecialchars(json_encode($vehicle), ENT_QUOTES, 'UTF-8') ?>); window.showToast('Vehicle saved to My Garage!', 'success');"
              class="btn-primary py-3 px-6 whitespace-nowrap text-xs shadow-lg flex-shrink-0">
        <span>💾</span> Save <?= e($model) ?> to My Garage
      </button>
    <?php endif; ?>
  </div>

  <!-- Product Results -->
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-bold uppercase tracking-wider font-condensed text-slate-900">
        Compatible Parts Available (<?= count($catalog['data']) ?>)
      </h2>
      <a href="/parts" class="text-xs font-semibold text-brand-600 hover:text-brand-700 uppercase">
        &larr; View Full Catalog
      </a>
    </div>

    <?php if (!empty($catalog['data'])): ?>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
        <?php foreach ($catalog['data'] as $part): ?>
          <?= \App\Core\View::partial('partials/product_card', ['product' => $part]) ?>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-xl border border-dashed border-slate-300 p-12 text-center space-y-4">
        <div class="text-4xl">🔧</div>
        <h3 class="text-base font-bold uppercase font-condensed text-slate-900">No Catalog Parts Uploaded Yet for this Year</h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto">
          Need brake pads, filters, or suspension components for this vehicle? Our workshop sourcing network can locate them for you in 2 hours.
        </p>
        <a href="/request-part?vehicle_info=<?= urlencode("{$make} {$model} {$year}") ?>" class="btn-primary text-xs">
          Request <?= e($make) ?> <?= e($model) ?> Parts &rarr;
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- Vehicle Technical Specifications & Maintenance Guide -->
  <div class="mt-12 bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
    <h3 class="text-base font-bold uppercase tracking-wider font-condensed text-slate-900 mb-3 border-b border-slate-100 pb-2">
      Vehicle Fitment Notes for <?= e($make) ?> <?= e($model) ?> (<?= e($year) ?>)
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs text-slate-600 leading-relaxed">
      <div>
        <h4 class="font-bold text-slate-900 uppercase font-condensed mb-1">Recommended Oil &amp; Filter</h4>
        <p>Ensure regular engine oil element replacement every 5,000 km. Use API SP or SN synthetic motor oil suited for extreme Pakistani ambient temperatures.</p>
      </div>
      <div>
        <h4 class="font-bold text-slate-900 uppercase font-condensed mb-1">Braking &amp; Suspension</h4>
        <p>Ceramic brake pads deliver dust-free, silent performance without rotor gouging. Inspect strut mounts and tie rod ends periodically on rough roads.</p>
      </div>
      <div>
        <h4 class="font-bold text-slate-900 uppercase font-condensed mb-1">Ignition &amp; Mileage</h4>
        <p>Iridium spark plugs last up to 60,000 km while restoring peak throttle responsiveness and fuel efficiency on local fuel grades.</p>
      </div>
    </div>
  </div>

</div>

