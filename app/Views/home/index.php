<!-- Homepage View -->
<div class="space-y-12">

  <!-- ============================================================================
       HERO SECTION WITH MOTORCYCLE FITMENT WIDGET
       ============================================================================ -->
  <section class="hero-industrial-bg relative text-white py-12 md:py-20 border-b border-slate-800 overflow-hidden" style="background-color: #090d16 !important; color: #ffffff !important;">
    <!-- Subtle industrial grid background texture -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#f97316_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">

        <!-- Hero Headline & Trust Badges -->
        <div class="lg:col-span-7 space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/30 text-brand-400 text-xs font-bold uppercase tracking-wider font-condensed">
            <span>🏍️</span> Pakistan's #1 Dedicated Motorcycle Spare Parts Store
          </div>

          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold font-condensed uppercase tracking-tight text-white leading-tight">
            Guaranteed Fitment <br>
            <span class="text-brand-500">Genuine &amp; OEM Bike Parts</span>
          </h1>

          <p class="text-sm sm:text-base text-slate-300 max-w-xl leading-relaxed">
            Order authentic spare parts for Honda CD 70, CG 125, CB 150F, Yamaha YBR 125, Suzuki GS 150 &amp; Chinese 70cc bikes. Search by OEM part number, SKU, or select your exact bike model. Nationwide Cash on Delivery (COD).
          </p>

          <!-- Quick Feature Badges -->
          <div class="flex flex-wrap gap-4 text-xs font-semibold text-slate-300 pt-2">
            <span class="flex items-center gap-1.5"><span class="text-emerald-400">✓</span> 100% Genuine &amp; OEM Parts</span>
            <span class="flex items-center gap-1.5"><span class="text-emerald-400">✓</span> 7-Day Return Guarantee</span>
            <span class="flex items-center gap-1.5"><span class="text-emerald-400">✓</span> Ustaad / Mechanic Wholesale Pricing</span>
          </div>

          <!-- Quick Bike Model Filters -->
          <div class="pt-2">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 font-condensed mb-2">Popular Bikes in Pakistan:</div>
            <div class="flex flex-wrap gap-2">
              <a href="/parts?q=CD+70" class="px-2.5 py-1 bg-slate-800/80 hover:bg-brand-500 hover:text-white border border-slate-700 rounded text-xs font-bold font-condensed transition-colors">Honda CD 70</a>
              <a href="/parts?q=CG+125" class="px-2.5 py-1 bg-slate-800/80 hover:bg-brand-500 hover:text-white border border-slate-700 rounded text-xs font-bold font-condensed transition-colors">Honda CG 125</a>
              <a href="/parts?q=CB+150F" class="px-2.5 py-1 bg-slate-800/80 hover:bg-brand-500 hover:text-white border border-slate-700 rounded text-xs font-bold font-condensed transition-colors">Honda CB 150F</a>
              <a href="/parts?q=YBR+125" class="px-2.5 py-1 bg-slate-800/80 hover:bg-brand-500 hover:text-white border border-slate-700 rounded text-xs font-bold font-condensed transition-colors">Yamaha YBR 125G</a>
              <a href="/parts?q=GS+150" class="px-2.5 py-1 bg-slate-800/80 hover:bg-brand-500 hover:text-white border border-slate-700 rounded text-xs font-bold font-condensed transition-colors">Suzuki GS 150</a>
              <a href="/parts?q=GD+110" class="px-2.5 py-1 bg-slate-800/80 hover:bg-brand-500 hover:text-white border border-slate-700 rounded text-xs font-bold font-condensed transition-colors">Suzuki GD 110S</a>
            </div>
          </div>
        </div>

        <!-- "SELECT YOUR MOTORCYCLE" WIDGET (Hero Centerpiece) -->
        <div class="lg:col-span-5">
          <div class="bg-white text-slate-900 rounded-2xl shadow-2xl p-6 sm:p-7 border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
              <div class="flex items-center gap-2.5">
                <span class="text-2xl">🏍️</span>
                <div>
                  <h2 class="text-base font-bold uppercase tracking-wider font-condensed text-slate-900 leading-none">Select Your Motorcycle</h2>
                  <span class="text-[11px] text-slate-500">Filter catalog to 100% compatible bike parts</span>
                </div>
              </div>
              <span class="text-xs font-bold text-brand-600 uppercase font-condensed bg-brand-50 px-2 py-0.5 rounded">Exact Fit</span>
            </div>

            <!-- Vehicle Chained Form -->
            <form action="/parts" method="GET" id="hero-vehicle-form" onsubmit="handleHeroSubmit(event)">
              <div class="space-y-4">
                
                <!-- Make -->
                <div>
                  <label class="form-label">1. Motorcycle Make</label>
                  <select id="hero-make" class="form-input" onchange="loadHeroModels(this.value)" required>
                    <option value="">-- Choose Brand (e.g. Honda, Yamaha, Suzuki) --</option>
                    <?php foreach ($makes as $m): ?>
                      <option value="<?= e($m) ?>"><?= e($m) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Model -->
                <div>
                  <label class="form-label">2. Bike Model</label>
                  <select id="hero-model" class="form-input" onchange="loadHeroYears()" disabled required>
                    <option value="">-- Select Brand First --</option>
                  </select>
                </div>

                <!-- Year / Engine -->
                <div>
                  <label class="form-label">3. Year &amp; Engine Displacement</label>
                  <select id="hero-year" name="vehicle_id" class="form-input" disabled required>
                    <option value="">-- Select Model First --</option>
                  </select>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="hero-submit-btn" class="w-full btn-primary py-3 text-sm shadow-md flex items-center justify-center gap-2" disabled>
                  <span>Find Compatible Bike Parts</span> &rarr;
                </button>

              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================================
       POPULAR MOTORCYCLE CATEGORIES
       ============================================================================ -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold uppercase tracking-wider font-condensed text-slate-900">
          Motorcycle Systems &amp; Categories
        </h2>
        <p class="text-xs text-slate-500">Explore authentic replacement components for your bike</p>
      </div>
      <a href="/parts" class="text-xs font-bold text-brand-600 hover:text-brand-700 uppercase font-condensed">
        View All Categories &rarr;
      </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
      <?php foreach ($categories as $cat): ?>
        <a href="/category/<?= e($cat['slug']) ?>" class="group bg-white p-4 rounded-xl border border-slate-200 hover:border-brand-500 hover:shadow-card-hover transition-all text-center flex flex-col items-center justify-center">
          <div class="w-14 h-14 rounded-full bg-slate-50 group-hover:bg-brand-50 flex items-center justify-center text-2xl mb-2.5 transition-colors border border-slate-100">
            <?php
              $icons = [
                'engine-cylinder'       => '⚙️',
                'carburetor-fuel'       => '⛽',
                'drive-transmission'    => '⛓️',
                'electrical-ignition'   => '⚡',
                'brakes-cables'         => '🛑',
                'suspension-handling'   => '🔩',
                'body-plastics-lights'  => '💡',
                'silencers-exhaust'     => '🔥',
                'tyres-tubes-oils'      => '🛞',
              ];
              echo $icons[$cat['slug']] ?? '📦';
            ?>
          </div>
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 group-hover:text-brand-600 transition-colors font-condensed">
            <?= e($cat['name']) ?>
          </h3>
          <span class="text-[10px] text-slate-400 mt-0.5"><?= (int)$cat['product_count'] ?> parts</span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ============================================================================
       FEATURED & BEST SELLING SPARE PARTS
       ============================================================================ -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold uppercase tracking-wider font-condensed text-slate-900">
          Best Selling Spare Parts
        </h2>
        <p class="text-xs text-slate-500">Most requested fast-moving OEM components in Pakistan</p>
      </div>
      <a href="/parts?sort=newest" class="text-xs font-bold text-brand-600 hover:text-brand-700 uppercase font-condensed">
        Browse All Parts &rarr;
      </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
      <?php foreach ($bestSellers as $part): ?>
        <?= \App\Core\View::partial('partials/product_card', ['product' => $part]) ?>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ============================================================================
       SHOP BY POPULAR PAKISTANI VEHICLES
       ============================================================================ -->
  <section class="bg-slate-100 py-12 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-xl mx-auto mb-8">
        <h2 class="text-xl sm:text-2xl font-bold uppercase tracking-wider font-condensed text-slate-900">
          Shop By Popular Vehicles
        </h2>
        <p class="text-xs text-slate-600 mt-1">Direct access to compatible spare parts for Pakistan's top-selling models</p>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5">
        <?php foreach ($popularVehicles as $v): ?>
          <a href="/parts/<?= urlencode($v['make']) ?>/<?= urlencode($v['model']) ?>/<?= (int)$v['year_start'] ?>"
             class="bg-white p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 hover:shadow-md transition-all text-left group">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider font-condensed"><?= e($v['make']) ?></div>
            <div class="text-xs font-bold text-slate-900 group-hover:text-brand-600 transition-colors truncate"><?= e($v['model']) ?></div>
            <div class="text-[10px] text-slate-500 mt-1 font-mono"><?= (int)$v['year_start'] ?> - <?= (int)$v['year_end'] ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============================================================================
       SHOP BY POPULAR BRANDS
       ============================================================================ -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl sm:text-2xl font-bold uppercase tracking-wider font-condensed text-slate-900">
        Certified Brands
      </h2>
      <span class="text-xs text-slate-500">Verified factory quality</span>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
      <?php foreach ($popularBrands as $b): ?>
        <a href="/brand/<?= e($b['slug']) ?>" class="bg-white p-4 rounded-xl border border-slate-200 hover:border-slate-400 hover:shadow-sm transition-all flex flex-col items-center justify-center text-center">
          <div class="text-sm font-extrabold uppercase tracking-wider text-slate-800 font-condensed mb-1">
            <?= e($b['name']) ?>
          </div>
          <span class="text-[10px] text-slate-400">Genuine Supplier</span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ============================================================================
       CAN'T FIND YOUR PART? (High-Conversion Banner)
       ============================================================================ -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-gradient-to-r from-chassis-900 to-chassis-800 text-white rounded-2xl p-8 sm:p-10 shadow-xl border border-slate-700 flex flex-col md:flex-row items-center justify-between gap-6">
      <div class="space-y-2 max-w-xl text-center md:text-left">
        <span class="text-brand-400 text-xs font-bold uppercase tracking-wider font-condensed">Can't Find What You Need?</span>
        <h3 class="text-2xl font-bold uppercase font-condensed tracking-tight">Looking for a Hard-to-Find or Kabli Part?</h3>
        <p class="text-xs text-slate-300 leading-relaxed">
          Submit your vehicle details, chassis/VIN number, or upload a photo of your old part. Our sourcing team at Montgomery Road &amp; Plaza Karachi will locate it within hours.
        </p>
      </div>
      <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <a href="/request-part" class="btn-primary py-3 px-6 text-center text-xs whitespace-nowrap">
          Submit Part Request &rarr;
        </a>
        <a href="https://wa.me/<?= e(setting('contact_whatsapp', '923001234567')) ?>" target="_blank" rel="noopener" class="btn-whatsapp py-3 px-6 text-center text-xs whitespace-nowrap">
          Ask via WhatsApp
        </a>
      </div>
    </div>
  </section>

  <!-- ============================================================================
       LATEST MAINTENANCE GUIDES & BLOG
       ============================================================================ -->
  <?php if (!empty($latestPosts)): ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-xl sm:text-2xl font-bold uppercase tracking-wider font-condensed text-slate-900">
            Maintenance &amp; Buying Guides
          </h2>
          <p class="text-xs text-slate-500">Expert tips on fake part identification and engine maintenance in Pakistan</p>
        </div>
        <a href="/blog" class="text-xs font-bold text-brand-600 hover:text-brand-700 uppercase font-condensed">
          Read All Guides &rarr;
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($latestPosts as $post): ?>
          <article class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-card hover:shadow-card-hover transition-all flex flex-col justify-between">
            <div class="p-5">
              <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-bold text-brand-600 uppercase font-condensed"><?= e($post['category_name'] ?? 'Guide') ?></span>
                <span><?= (int)$post['reading_time_min'] ?> min read</span>
              </div>
              <h3 class="text-base font-bold text-slate-900 hover:text-brand-600 transition-colors leading-snug mb-2 font-condensed uppercase">
                <a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
              </h3>
              <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">
                <?= e($post['excerpt']) ?>
              </p>
            </div>
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
              <span class="text-slate-500"><?= \App\Helpers\Formatter::date($post['published_at'] ?? $post['created_at'], 'd M Y') ?></span>
              <a href="/blog/<?= e($post['slug']) ?>" class="font-bold text-brand-600 hover:text-brand-700 uppercase">Read Article &rarr;</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

</div>

<!-- Hero Vehicle Selector Script -->
<script>
function loadHeroModels(make) {
  const modelSelect = document.getElementById('hero-model');
  const yearSelect = document.getElementById('hero-year');
  const submitBtn = document.getElementById('hero-submit-btn');

  modelSelect.innerHTML = '<option value="">Loading models...</option>';
  modelSelect.disabled = true;
  yearSelect.innerHTML = '<option value="">-- Select Model First --</option>';
  yearSelect.disabled = true;
  submitBtn.disabled = true;

  if (!make) return;

  fetch('/garage/options?make=' + encodeURIComponent(make))
    .then(r => r.json())
    .then(data => {
      let opts = '<option value="">-- Choose Model --</option>';
      data.models.forEach(m => {
        opts += `<option value="${m}">${m}</option>`;
      });
      modelSelect.innerHTML = opts;
      modelSelect.disabled = false;
    });
}

function loadHeroYears() {
  const make = document.getElementById('hero-make').value;
  const model = document.getElementById('hero-model').value;
  const yearSelect = document.getElementById('hero-year');
  const submitBtn = document.getElementById('hero-submit-btn');

  yearSelect.innerHTML = '<option value="">Loading years...</option>';
  yearSelect.disabled = true;
  submitBtn.disabled = true;

  if (!model) return;

  fetch('/garage/options?make=' + encodeURIComponent(make) + '&model=' + encodeURIComponent(model))
    .then(r => r.json())
    .then(data => {
      let opts = '<option value="">-- Choose Year / Engine --</option>';
      data.vehicles.forEach(v => {
        opts += `<option value="${v.id}" data-obj='${JSON.stringify(v)}'>${v.year_start}-${v.year_end} (${v.engine})</option>`;
      });
      yearSelect.innerHTML = opts;
      yearSelect.disabled = false;
      yearSelect.onchange = () => { submitBtn.disabled = !yearSelect.value; };
    });
}

function handleHeroSubmit(e) {
  e.preventDefault();
  const yearSelect = document.getElementById('hero-year');
  const opt = yearSelect.options[yearSelect.selectedIndex];
  if (!opt || !opt.dataset.obj) return;

  const vehicle = JSON.parse(opt.dataset.obj);
  Garage.set(vehicle);
  window.location.href = `/parts/${encodeURIComponent(vehicle.make)}/${encodeURIComponent(vehicle.model)}/${vehicle.year_start}`;
}
</script>

