<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
  <title><?= e($meta_title ?? $title ?? 'PakAutoParts | Genuine & OEM Spare Parts Online in Pakistan') ?></title>
  <meta name="description" content="<?= e($meta_description ?? 'Buy 100% genuine and OEM auto spare parts for Toyota, Honda, Suzuki, and motorbikes in Pakistan. Cash on delivery and nationwide shipping.') ?>">
  <link rel="canonical" href="<?= e($canonical_url ?? url()) ?>">
  <meta name="theme-color" content="#0F172A">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="<?= e($og_type ?? 'website') ?>">
  <meta property="og:url" content="<?= e($canonical_url ?? url()) ?>">
  <meta property="og:title" content="<?= e($meta_title ?? $title ?? 'PakAutoParts') ?>">
  <meta property="og:description" content="<?= e($meta_description ?? 'Pakistan\'s trusted spare parts network') ?>">
  <meta property="og:image" content="<?= e($og_image ?? url('assets/images/og-share.jpg')) ?>">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Tailwind CSS Core Engine -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              DEFAULT: '#f97316',
              50: '#fff7ed',
              100: '#ffedd5',
              200: '#fed7aa',
              300: '#fdba74',
              400: '#fb923c',
              500: '#f97316',
              600: '#ea580c',
              700: '#c2410c',
              800: '#9a3412',
              900: '#7c2d12'
            },
            chassis: {
              50: '#f8fafc',
              100: '#f1f5f9',
              200: '#e2e8f0',
              300: '#cbd5e1',
              400: '#94a3b8',
              500: '#64748b',
              600: '#475569',
              700: '#334155',
              800: '#1e293b',
              900: '#0f172a',
              950: '#090d16'
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            condensed: ['Chakra Petch', 'sans-serif'],
            mono: ['JetBrains Mono', 'monospace']
          }
        }
      }
    }
  </script>

  <!-- Production App CSS & Custom Design Tokens -->
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/custom.css') ?>">

  <!-- Pinned Alpine.js 3 via CDN -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

  <!-- Schema.org JSON-LD -->
  <?php if (!empty($json_ld)): ?>
    <script type="application/ld+json">
      <?= json_encode($json_ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>
  <?php endif; ?>
</head>
<body class="flex flex-col min-h-screen bg-slate-50 text-slate-900 pb-16 md:pb-0 antialiased" x-data="{ mobileMenuOpen: false, searchOpen: false }">

  <!-- Top Announcement Bar -->
  <div class="bg-chassis-950 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
        <span class="font-medium"><?= e(setting('announcement_banner', '⚡ Free Nationwide Shipping on orders above Rs. 5,000 | Cash on Delivery Available')) ?></span>
      </div>
      <div class="hidden sm:flex items-center gap-4 text-xs font-semibold">
        <a href="/order/track" class="hover:text-brand-500 transition-colors">📦 Track Order</a>
        <a href="/request-part" class="hover:text-brand-500 transition-colors">🔍 Can't Find Part?</a>
        <a href="https://wa.me/<?= e(setting('contact_whatsapp', '923001234567')) ?>" target="_blank" rel="noopener" class="text-emerald-400 hover:text-emerald-300 flex items-center gap-1 font-bold">
          <span>WhatsApp Help: <?= e(setting('contact_phone', '0300-1234567')) ?></span>
        </a>
      </div>
    </div>
  </div>

  <!-- Header -->
  <?= \App\Core\View::partial('partials/header') ?>

  <!-- Flash Messages -->
  <?= \App\Core\View::partial('partials/flash') ?>

  <!-- Main View Content -->
  <main class="flex-1">
    <?= $content ?>
  </main>

  <!-- Trust Bar -->
  <?= \App\Core\View::partial('partials/trust_bar') ?>

  <!-- Footer -->
  <?= \App\Core\View::partial('partials/footer') ?>

  <!-- Mobile Bottom Nav -->
  <?= \App\Core\View::partial('partials/mobile_nav') ?>

  <!-- Floating WhatsApp CTA (Desktop & Mobile) -->
  <a href="https://wa.me/<?= e(setting('contact_whatsapp', '923001234567')) ?>?text=<?= urlencode('Salam! I need assistance with auto spare parts.') ?>"
     target="_blank"
     rel="noopener"
     class="floating-whatsapp-btn fixed bottom-20 right-4 md:bottom-8 md:right-8 z-40 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-full shadow-2xl flex items-center justify-center whatsapp-pulse transition-transform hover:scale-110 no-print"
     style="width: 52px !important; height: 52px !important; min-width: 52px !important; min-height: 52px !important; max-width: 52px !important; max-height: 52px !important;"
     aria-label="Chat on WhatsApp">
    <svg class="fill-current" style="width: 28px !important; height: 28px !important; max-width: 28px !important; max-height: 28px !important;" viewBox="0 0 24 24">
      <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
    </svg>
  </a>

  <!-- Core Scripts -->
  <script src="<?= asset('assets/js/garage.js') ?>"></script>
  <script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>

