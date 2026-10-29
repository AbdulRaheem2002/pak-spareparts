<!-- Main Footer -->
<footer class="bg-chassis-950 text-slate-400 pt-16 pb-12 border-t border-slate-800 no-print">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">

      <!-- Column 1: Brand & Contact -->
      <div class="lg:col-span-2 space-y-4">
        <a href="/" class="block">
          <img src="<?= asset('assets/images/logo.svg') ?>" alt="PakAutoParts" class="h-10 w-auto brightness-200">
        </a>
        <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
          Pakistan's premier direct-to-consumer and workshop auto spare parts platform. Supplying verified genuine, OEM, and performance replacement components nationwide with guaranteed vehicle fitment.
        </p>

        <div class="space-y-2 text-xs pt-2">
          <div class="flex items-center gap-2">
            <span class="text-brand-500 font-bold">📍</span>
            <span><?= e(setting('contact_address', 'Plaza #14, Auto Market, Montgomery Road, Lahore, Pakistan')) ?></span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-brand-500 font-bold">📞</span>
            <a href="tel:<?= e(setting('contact_phone', '0300-1234567')) ?>" class="text-slate-200 hover:text-brand-400 font-bold">
              <?= e(setting('contact_phone', '0300-1234567')) ?>
            </a>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-emerald-500 font-bold">💬</span>
            <a href="https://wa.me/<?= e(setting('contact_whatsapp', '923001234567')) ?>" target="_blank" rel="noopener" class="text-emerald-400 hover:underline font-bold">
              WhatsApp Support (9 AM - 9 PM)
            </a>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-brand-500 font-bold">✉️</span>
            <a href="mailto:<?= e(setting('contact_email', 'support@pakautoparts.pk')) ?>" class="text-slate-200 hover:text-brand-400">
              <?= e(setting('contact_email', 'support@pakautoparts.pk')) ?>
            </a>
          </div>
        </div>
      </div>

      <!-- Column 2: Popular Categories -->
      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-white font-condensed mb-4 border-l-2 border-brand-500 pl-2.5">
          Categories
        </h4>
        <ul class="space-y-2 text-xs">
          <li><a href="/category/engine-components" class="hover:text-white transition-colors">Engine Components</a></li>
          <li><a href="/category/brake-system" class="hover:text-white transition-colors">Brake Pads &amp; Rotors</a></li>
          <li><a href="/category/suspension-steering" class="hover:text-white transition-colors">Shock Absorbers</a></li>
          <li><a href="/category/filters-maintenance" class="hover:text-white transition-colors">Filters &amp; Maintenance</a></li>
          <li><a href="/category/electrical-ignition" class="hover:text-white transition-colors">Spark Plugs &amp; Coils</a></li>
          <li><a href="/category/oils-lubricants" class="hover:text-white transition-colors">Synthetic Motor Oils</a></li>
          <li><a href="/category/lighting-bulbs" class="hover:text-white transition-colors">Headlights &amp; LEDs</a></li>
        </ul>
      </div>

      <!-- Column 3: Popular Makes & Models -->
      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-white font-condensed mb-4 border-l-2 border-brand-500 pl-2.5">
          Popular Vehicles
        </h4>
        <ul class="space-y-2 text-xs">
          <li><a href="/parts/Toyota/Corolla/2018" class="hover:text-white transition-colors">Toyota Corolla Parts</a></li>
          <li><a href="/parts/Honda/Civic/2019" class="hover:text-white transition-colors">Honda Civic X Parts</a></li>
          <li><a href="/parts/Suzuki/Alto/2021" class="hover:text-white transition-colors">Suzuki Alto 660cc Parts</a></li>
          <li><a href="/parts/Suzuki/Cultus/2020" class="hover:text-white transition-colors">Suzuki Cultus Parts</a></li>
          <li><a href="/parts/Honda/CG125/2022" class="hover:text-white transition-colors">Honda CG125 Bike Parts</a></li>
          <li><a href="/parts/Honda/CD70/2023" class="hover:text-white transition-colors">Honda CD70 Bike Parts</a></li>
        </ul>
      </div>

      <!-- Column 4: Customer Support & Legal -->
      <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-white font-condensed mb-4 border-l-2 border-brand-500 pl-2.5">
          Customer Care
        </h4>
        <ul class="space-y-2 text-xs">
          <li><a href="/order/track" class="hover:text-white transition-colors">Track Courier Order</a></li>
          <li><a href="/request-part" class="hover:text-white transition-colors">Can't Find Your Part?</a></li>
          <li><a href="/blog" class="hover:text-white transition-colors">Maintenance Articles &amp; Blog</a></li>
          <li><a href="/admin/login" class="text-slate-600 hover:text-slate-400 transition-colors">Staff Login</a></li>
        </ul>

        <!-- Supported Payment Badges -->
        <div class="mt-6 pt-4 border-t border-slate-800">
          <div class="text-[11px] font-bold uppercase text-slate-300 font-condensed mb-2">Accepted Payments</div>
          <div class="flex flex-wrap gap-1.5 text-[10px] font-bold">
            <span class="px-2 py-0.5 bg-slate-800 text-slate-200 rounded border border-slate-700">Cash on Delivery</span>
            <span class="px-2 py-0.5 bg-slate-800 text-slate-200 rounded border border-slate-700">Meezan Bank</span>
            <span class="px-2 py-0.5 bg-red-950 text-red-300 rounded border border-red-800">JazzCash</span>
            <span class="px-2 py-0.5 bg-emerald-950 text-emerald-300 rounded border border-emerald-800">Easypaisa</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Bottom Copyright -->
    <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
      <div>
        &copy; <?= date('Y') ?> <?= e(setting('site_name', 'PakAutoParts')) ?>. All Rights Reserved. Engineered for Pakistan.
      </div>
      <div class="flex items-center gap-4">
        <span>Delivery via TCS / Leopards / Trax</span>
      </div>
    </div>
  </div>
</footer>

