<!-- Blog Magazine Listing View -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
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

  <!-- Magazine Hero Header -->
  <div class="text-center max-w-2xl mx-auto space-y-3">
    <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-50 text-brand-600 rounded-full text-xs font-bold uppercase font-condensed">
      <span>✍️</span> Expert Automotive Guides
    </div>
    <h1 class="text-3xl sm:text-4xl font-extrabold uppercase font-condensed tracking-tight text-slate-900">
      Auto Maintenance &amp; Parts Buying Guides
    </h1>
    <p class="text-xs text-slate-500 leading-relaxed">
      Learn how to avoid fake counterfeit spare parts, select optimal synthetic oils for Pakistani summer driving, and maintain your vehicle for maximum fuel economy.
    </p>
  </div>

  <!-- Articles Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php if (!empty($posts['data'])): ?>
      <?php foreach ($posts['data'] as $p): ?>
        <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300 flex flex-col justify-between group">
          <div>
            <!-- Featured Image -->
            <a href="/blog/<?= e($p['slug']) ?>" class="block aspect-video bg-slate-100 overflow-hidden relative">
              <img src="<?= e($p['featured_image'] ?: '/assets/images/placeholder.svg') ?>"
                   alt="<?= e($p['title']) ?>"
                   loading="lazy"
                   class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
              <span class="absolute top-3 left-3 bg-chassis-950/80 text-white text-[10px] uppercase font-bold px-2 py-0.5 rounded tracking-wider font-condensed backdrop-blur-sm">
                <?= e($p['category_name'] ?? 'Guide') ?>
              </span>
            </a>

            <!-- Content -->
            <div class="p-6 space-y-2">
              <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono">
                <span><?= \App\Helpers\Formatter::date($p['published_at'] ?? $p['created_at'], 'd M Y') ?></span>
                <span><?= (int)$p['reading_time_min'] ?> min read</span>
              </div>

              <h2 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition-colors leading-snug font-condensed uppercase">
                <a href="/blog/<?= e($p['slug']) ?>">
                  <?= e($p['title']) ?>
                </a>
              </h2>

              <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                <?= e($p['excerpt']) ?>
              </p>
            </div>
          </div>

          <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-400 font-mono text-[11px]">👁️ <?= number_format((float)$p['view_count']) ?> views</span>
            <a href="/blog/<?= e($p['slug']) ?>" class="font-bold text-brand-600 group-hover:text-brand-700 uppercase">
              Read Guide &rarr;
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-span-3 text-center py-12 text-slate-400 text-xs">
        No published articles found.
      </div>
    <?php endif; ?>
  </div>

  <!-- Pagination -->
  <?php if (!empty($posts['last_page']) && $posts['last_page'] > 1): ?>
    <div class="flex items-center justify-center gap-2 pt-6">
      <?php for ($i = 1; $i <= $posts['last_page']; $i++): ?>
        <a href="?page=<?= $i ?>"
           class="px-3.5 py-2 rounded-lg text-xs font-bold font-condensed transition-colors <?= $i === $posts['current_page'] ? 'bg-brand-500 text-white shadow-sm' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>

</div>

