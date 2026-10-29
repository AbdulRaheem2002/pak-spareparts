<!-- Public Blog Article Show View -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
    <?php foreach ($breadcrumbs as $idx => $bc): ?>
      <?php if ($idx > 0): ?>
        <span>/</span>
      <?php endif; ?>
      <?php if (!empty($bc['url'])): ?>
        <a href="<?= e($bc['url']) ?>" class="hover:text-brand-600 transition-colors"><?= e($bc['title']) ?></a>
      <?php else: ?>
        <span class="text-slate-900 font-bold line-clamp-1"><?= e($bc['title']) ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
    
    <!-- MAIN ARTICLE CONTENT (8 Cols) -->
    <article class="lg:col-span-8 bg-white p-6 sm:p-10 rounded-2xl border border-slate-200 shadow-sm space-y-8">
      
      <!-- Article Header -->
      <div class="space-y-4 border-b border-slate-100 pb-6">
        <div class="flex items-center gap-3 text-xs">
          <span class="px-2.5 py-1 bg-brand-50 text-brand-600 rounded font-bold uppercase font-condensed">
            <?= e($post['category_name'] ?? 'Automotive Guide') ?>
          </span>
          <span class="text-slate-400 font-mono"><?= \App\Helpers\Formatter::date($post['published_at'] ?? $post['created_at'], 'd M Y') ?></span>
          <span class="text-slate-400">•</span>
          <span class="text-slate-500 font-medium font-mono">⏱️ <?= (int)$post['reading_time_min'] ?> min read</span>
        </div>

        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold uppercase font-condensed tracking-tight text-slate-900 leading-tight">
          <?= e($post['title']) ?>
        </h1>

        <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed italic border-l-4 border-brand-500 pl-4 py-1">
          <?= e($post['excerpt']) ?>
        </p>
      </div>

      <!-- Featured Image -->
      <?php if (!empty($post['featured_image'])): ?>
        <div class="aspect-video rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
          <img src="<?= e($post['featured_image']) ?>" alt="<?= e($post['title']) ?>" class="w-full h-full object-cover">
        </div>
      <?php endif; ?>

      <!-- AUTOMATIC TABLE OF CONTENTS -->
      <?php if (!empty($toc)): ?>
        <div class="p-5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
          <h2 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 flex items-center gap-2">
            <span>📑</span> Table of Contents
          </h2>
          <ul class="space-y-1.5 text-xs text-slate-700">
            <?php foreach ($toc as $item): ?>
              <li class="<?= $item['tag'] === 'h3' ? 'ml-4' : 'font-semibold' ?>">
                <a href="#<?= e($item['anchor']) ?>" class="hover:text-brand-600 hover:underline">
                  <?= e($item['title']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Article Rich Content Body -->
      <div class="prose max-w-none text-slate-800 text-sm leading-relaxed space-y-4">
        <?= $content ?>
      </div>

      <!-- RELATED REPLACEMENT PARTS WIDGET -->
      <?php if (!empty($relatedProducts)): ?>
        <div class="mt-10 p-6 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 flex items-center gap-1.5">
              <span>🔧</span> Related Genuine &amp; OEM Parts
            </h3>
            <a href="/parts" class="text-xs font-bold text-brand-600 hover:underline">View All &rarr;</a>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <?php foreach (array_slice($relatedProducts, 0, 4) as $rp): ?>
              <a href="/product/<?= e($rp['slug']) ?>" class="bg-white p-3 rounded-lg border border-slate-200 hover:border-brand-500 transition-all text-center block">
                <img src="<?= e($rp['primary_image'] ?: '/assets/images/placeholder.svg') ?>" alt="<?= e($rp['name']) ?>" class="w-16 h-16 object-contain mx-auto mb-2">
                <div class="text-[11px] font-bold text-slate-900 truncate"><?= e($rp['name']) ?></div>
                <div class="text-xs font-bold text-brand-600 font-condensed mt-1"><?= format_pkr($rp['price']) ?></div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Share Buttons -->
      <div class="border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
        <div class="text-slate-500 font-bold uppercase font-condensed">Share this guide with your mechanic / friends:</div>
        <div class="flex items-center gap-2">
          <?php
            $currentUrl = urlencode(url("blog/{$post['slug']}"));
            $shareText = urlencode("Check out this auto guide: {$post['title']}");
          ?>
          <a href="https://wa.me/?text=<?= $shareText ?>%20<?= $currentUrl ?>" target="_blank" rel="noopener" class="btn-whatsapp py-1.5 px-3 text-xs">
            Share on WhatsApp
          </a>
          <a href="https://twitter.com/intent/tweet?text=<?= $shareText ?>&url=<?= $currentUrl ?>" target="_blank" rel="noopener" class="btn-outline py-1.5 px-3 text-xs">
            Share on X
          </a>
        </div>
      </div>

    </article>

    <!-- SIDEBAR: RECENT ARTICLES & QUICK WIDGETS (4 Cols) -->
    <aside class="lg:col-span-4 space-y-6">
      
      <!-- Recent Articles -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
          Latest Maintenance Guides
        </h3>

        <div class="space-y-4">
          <?php foreach ($recentPosts as $rec): ?>
            <a href="/blog/<?= e($rec['slug']) ?>" class="flex items-center gap-3 group">
              <img src="<?= e($rec['featured_image'] ?: '/assets/images/placeholder.svg') ?>" class="w-14 h-14 rounded-lg object-cover border border-slate-200 flex-shrink-0" alt="Article">
              <div class="flex-1 min-w-0">
                <h4 class="text-xs font-bold text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-2 leading-snug">
                  <?= e($rec['title']) ?>
                </h4>
                <span class="text-[10px] text-slate-400 font-mono"><?= \App\Helpers\Formatter::date($rec['published_at'], 'd M Y') ?></span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Quick Help Banner -->
      <div class="bg-chassis-950 text-white p-6 rounded-2xl border border-slate-800 shadow-sm space-y-3">
        <span class="text-brand-400 text-xs font-bold uppercase font-condensed">Mechanic Verified Support</span>
        <h4 class="text-base font-bold uppercase font-condensed">Unsure which part fits your engine?</h4>
        <p class="text-xs text-slate-300 leading-relaxed">
          Send a photo of your engine bay or vehicle chassis number to our WhatsApp desk.
        </p>
        <a href="https://wa.me/<?= e(setting('contact_whatsapp', '923001234567')) ?>" target="_blank" rel="noopener" class="btn-whatsapp py-2 text-xs w-full text-center block">
          Chat with Mechanic
        </a>
      </div>

    </aside>

  </div>

</div>

