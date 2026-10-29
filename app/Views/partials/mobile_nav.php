<!-- Fixed Bottom Mobile Navigation Bar -->
<div id="mobile-nav" class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 shadow-2xl py-1.5 px-2 flex items-center justify-around text-slate-600 no-print">
  
  <!-- Home -->
  <a href="/" class="flex flex-col items-center py-1 px-2 text-xs font-medium hover:text-brand-500 <?= ($_SERVER['REQUEST_URI'] ?? '') === '/' ? 'text-brand-500 font-bold' : '' ?>">
    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    <span>Home</span>
  </a>

  <!-- Catalog / Parts -->
  <a href="/parts" class="flex flex-col items-center py-1 px-2 text-xs font-medium hover:text-brand-500 <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/parts') ? 'text-brand-500 font-bold' : '' ?>">
    <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
    </svg>
    <span>Catalog</span>
  </a>

  <!-- My Garage (Mobile Trigger) -->
  <button type="button" onclick="document.getElementById('garage-modal').classList.remove('hidden')" class="flex flex-col items-center py-1 px-2 text-xs font-medium text-brand-600 hover:text-brand-700">
    <div class="relative">
      <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
      </svg>
    </div>
    <span>Garage</span>
  </button>

  <!-- Cart -->
  <a href="/cart" class="flex flex-col items-center py-1 px-2 text-xs font-medium hover:text-brand-500 relative <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/cart') ? 'text-brand-500 font-bold' : '' ?>">
    <div class="relative">
      <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
      </svg>
      <span data-cart-badge class="absolute -top-1.5 -right-2 bg-brand-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white hidden">0</span>
    </div>
    <span>Cart</span>
  </a>

  <!-- WhatsApp Chat -->
  <a href="https://wa.me/<?= e(setting('contact_whatsapp', '923001234567')) ?>" target="_blank" rel="noopener" class="flex flex-col items-center py-1 px-2 text-xs font-medium text-emerald-600 hover:text-emerald-700">
    <svg class="w-5 h-5 mb-0.5 fill-current" viewBox="0 0 24 24">
      <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
    </svg>
    <span>Help</span>
  </a>

</div>

