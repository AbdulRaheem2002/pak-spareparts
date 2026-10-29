<!-- Sticky Main Header -->
<header class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm" x-data="{ garageModalOpen: false }">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-20 gap-4">

      <!-- Brand Logo -->
      <a href="/" class="flex-shrink-0 flex items-center">
        <img src="<?= asset('assets/images/logo.svg') ?>" alt="PakAutoParts Logo" class="h-10 w-auto">
      </a>

      <!-- Central Search Bar (Desktop & Tablet) -->
      <div class="hidden md:flex flex-1 max-w-2xl relative">
        <form action="/parts" method="GET" class="w-full relative">
          <div class="relative flex items-center">
            <input type="text"
                   id="global-search-input"
                   name="q"
                   placeholder="<?= e(trans('search_placeholder')) ?>"
                   value="<?= e($_GET['q'] ?? '') ?>"
                   autocomplete="off"
                   class="w-full pl-11 pr-24 py-3 bg-slate-50 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all font-medium">
            
            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>

            <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white font-condensed font-bold text-xs uppercase tracking-wider rounded-md transition-colors shadow-sm">
              Search
            </button>
          </div>
        </form>

        <!-- Live AJAX Search Suggestions Container -->
        <div id="search-suggestions" class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-slate-200 rounded-lg shadow-xl z-50 hidden overflow-hidden"></div>
      </div>

      <!-- Action Nav (My Garage, Cart, Support) -->
      <div class="flex items-center gap-3 sm:gap-5">

        <!-- "My Bike" Quick Selector Pill -->
        <div class="relative">
          <button type="button"
                  onclick="document.getElementById('garage-modal').classList.remove('hidden')"
                  class="flex items-center gap-2 px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 transition-colors">
            <span class="text-brand-500 text-base">🏍️</span>
            <div class="text-left">
              <div class="text-[10px] uppercase font-bold text-slate-500 leading-none">My Bike</div>
              <div class="truncate max-w-[120px] sm:max-w-[150px] leading-tight font-bold" data-garage-display>Select Bike</div>
            </div>
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
        </div>

        <!-- Cart Trigger -->
        <a href="/cart" class="relative p-2.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-800 transition-colors flex items-center gap-2" aria-label="Shopping Cart">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
          </svg>
          <span class="hidden lg:inline text-xs font-bold uppercase tracking-wider font-condensed">Cart</span>
          <span data-cart-badge class="absolute -top-1.5 -right-1.5 bg-brand-500 text-white text-[11px] font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-white hidden">0</span>
        </a>

        <!-- Mobile Search Button -->
        <button type="button"
                @click="searchOpen = !searchOpen"
                class="md:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg"
                aria-label="Open Search">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </button>

      </div>
    </div>

    <!-- Mobile Slide-Down Search -->
    <div x-show="searchOpen" x-cloak class="md:hidden pb-4">
      <form action="/parts" method="GET" class="relative">
        <input type="text"
               name="q"
               placeholder="Search Part Name or OEM #..."
               class="w-full pl-10 pr-4 py-2.5 bg-slate-100 border border-slate-300 rounded-lg text-sm text-slate-900 focus:bg-white focus:border-brand-500 focus:outline-none">
        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
      </form>
    </div>
  </div>

  <!-- Primary Categories Navigation Strip -->
  <nav class="bg-chassis-900 text-white hidden md:block">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between text-xs font-semibold tracking-wider uppercase font-condensed">
        <div class="flex items-center space-x-1 lg:space-x-3 overflow-x-auto py-2.5">
          <a href="/parts" class="px-2.5 py-1 text-brand-400 hover:text-white transition-colors flex items-center gap-1 font-bold">
            <span class="text-sm">☰</span> All Parts
          </a>
          <a href="/category/engine-components" class="px-2.5 py-1 hover:text-brand-400 transition-colors">Engine</a>
          <a href="/category/brake-system" class="px-2.5 py-1 hover:text-brand-400 transition-colors">Brakes</a>
          <a href="/category/suspension-steering" class="px-2.5 py-1 hover:text-brand-400 transition-colors">Suspension</a>
          <a href="/category/filters-maintenance" class="px-2.5 py-1 hover:text-brand-400 transition-colors">Filters</a>
          <a href="/category/electrical-ignition" class="px-2.5 py-1 hover:text-brand-400 transition-colors">Electrical</a>
          <a href="/category/lighting-bulbs" class="px-2.5 py-1 hover:text-brand-400 transition-colors">Lighting</a>
          <a href="/category/body-parts-mirrors" class="px-2.5 py-1 hover:text-brand-400 transition-colors">Body Parts</a>
          <a href="/category/oils-lubricants" class="px-2.5 py-1 hover:text-brand-400 transition-colors">Oils &amp; Fluids</a>
          <a href="/category/accessories" class="px-2.5 py-1 hover:text-brand-400 transition-colors">Accessories</a>
          <a href="/blog" class="px-2.5 py-1 text-slate-300 hover:text-brand-400 transition-colors">Guides &amp; Blog</a>
        </div>
        <div class="flex items-center">
          <a href="/request-part" class="px-3 py-1 bg-brand-500 hover:bg-brand-600 text-white rounded font-bold text-[11px] tracking-wider transition-colors">
            Can't Find Part?
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- My Garage Modal Dialog -->
  <?= \App\Core\View::partial('partials/garage_selector') ?>
</header>

