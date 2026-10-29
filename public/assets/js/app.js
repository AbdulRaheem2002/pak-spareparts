/**
 * PakAutoParts - Core Storefront JavaScript
 * Handles client-side cart, AJAX search auto-suggestions, toast notifications & modals.
 */

window.Cart = {
  KEY: 'pap_cart_items',

  getItems: function() {
    try {
      const data = localStorage.getItem(this.KEY);
      return data ? JSON.parse(data) : [];
    } catch (e) {
      return [];
    }
  },

  add: function(productId, qty = 1) {
    let items = this.getItems();
    productId = parseInt(productId, 10);
    qty = Math.max(1, parseInt(qty, 10));

    const existingIndex = items.findIndex(i => i.product_id === productId);
    if (existingIndex > -1) {
      items[existingIndex].qty += qty;
    } else {
      items.push({ product_id: productId, qty: qty });
    }

    this.save(items);
    window.showToast("Part added to your cart!", "success");
    this.updateBadge();
    window.dispatchEvent(new CustomEvent('cart:updated', { detail: items }));
  },

  update: function(productId, qty) {
    let items = this.getItems();
    productId = parseInt(productId, 10);
    qty = parseInt(qty, 10);

    if (qty <= 0) {
      items = items.filter(i => i.product_id !== productId);
    } else {
      const item = items.find(i => i.product_id === productId);
      if (item) {
        item.qty = qty;
      }
    }

    this.save(items);
    this.updateBadge();
    window.dispatchEvent(new CustomEvent('cart:updated', { detail: items }));
  },

  remove: function(productId) {
    this.update(productId, 0);
    window.showToast("Item removed from cart.", "info");
  },

  clear: function() {
    localStorage.removeItem(this.KEY);
    this.updateBadge();
    window.dispatchEvent(new CustomEvent('cart:updated', { detail: [] }));
  },

  save: function(items) {
    try {
      localStorage.setItem(this.KEY, JSON.stringify(items));
    } catch (e) {
      console.error("Cart save failed:", e);
    }
  },

  count: function() {
    const items = this.getItems();
    return items.reduce((sum, item) => sum + (parseInt(item.qty, 10) || 0), 0);
  },

  updateBadge: function() {
    const count = this.count();
    const badges = document.querySelectorAll('[data-cart-badge]');
    badges.forEach(b => {
      b.textContent = count;
      if (count > 0) {
        b.classList.remove('hidden');
      } else {
        b.classList.add('hidden');
      }
    });
  }
};

// Global Toast Notification Helper
window.showToast = function(message, type = 'success') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'fixed bottom-20 right-4 md:bottom-6 md:right-6 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none px-4';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  const bg = type === 'error' ? 'bg-rose-600' : type === 'info' ? 'bg-slate-800' : 'bg-emerald-600';
  toast.className = `${bg} text-white px-4 py-3 rounded-lg shadow-lg flex items-center justify-between text-sm font-medium transform transition-all duration-300 translate-y-4 opacity-0 pointer-events-auto`;
  
  toast.innerHTML = `
    <span>${message}</span>
    <button class="ml-3 text-white/80 hover:text-white" onclick="this.parentElement.remove()">&times;</button>
  `;

  container.appendChild(toast);

  // Trigger animation
  requestAnimationFrame(() => {
    toast.classList.remove('translate-y-4', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');
  });

  setTimeout(() => {
    toast.classList.remove('translate-y-0', 'opacity-100');
    toast.classList.add('translate-y-4', 'opacity-0');
    setTimeout(() => toast.remove(), 300);
  }, 4000);
};

// Live AJAX Search Auto-suggestions with Part-Number-First Debouncing
document.addEventListener('DOMContentLoaded', function() {
  window.Cart.updateBadge();

  const searchInput = document.getElementById('global-search-input');
  const resultsContainer = document.getElementById('search-suggestions');

  if (searchInput && resultsContainer) {
    let debounceTimer;

    searchInput.addEventListener('input', function() {
      clearTimeout(debounceTimer);
      const query = searchInput.value.trim();

      if (query.length < 2) {
        resultsContainer.classList.add('hidden');
        resultsContainer.innerHTML = '';
        return;
      }

      debounceTimer = setTimeout(() => {
        fetch('/search/suggest?q=' + encodeURIComponent(query))
          .then(res => res.json())
          .then(data => {
            if (data.results && data.results.length > 0) {
              let html = '<div class="py-2 divide-y divide-slate-100">';
              data.results.forEach(item => {
                html += `
                  <a href="/product/${item.slug}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 transition-colors">
                    <img src="${item.primary_image || '/assets/images/placeholder.svg'}" class="w-10 h-10 object-cover rounded border border-slate-200" alt="${item.name}">
                    <div class="flex-1 min-w-0">
                      <div class="text-sm font-semibold text-slate-900 truncate">${item.name}</div>
                      <div class="text-xs text-slate-500 font-mono">OEM: <span class="text-brand-600 font-bold">${item.oem_number}</span> | SKU: ${item.sku}</div>
                    </div>
                    <div class="text-sm font-bold text-slate-900">Rs. ${Number(item.price).toLocaleString()}</div>
                  </a>
                `;
              });
              html += `</div>
                <div class="bg-slate-50 px-4 py-2 text-center border-t border-slate-200">
                  <a href="/parts?q=${encodeURIComponent(query)}" class="text-xs font-bold text-brand-600 hover:text-brand-700 uppercase">View All Results &rarr;</a>
                </div>
              `;
              resultsContainer.innerHTML = html;
              resultsContainer.classList.remove('hidden');
            } else {
              resultsContainer.innerHTML = `
                <div class="p-4 text-center text-sm text-slate-500">
                  No parts found for "<strong>${query}</strong>".
                  <div class="mt-2"><a href="/request-part?q=${encodeURIComponent(query)}" class="text-xs font-bold text-brand-600 hover:underline">Can't find your part? Request it now &rarr;</a></div>
                </div>
              `;
              resultsContainer.classList.remove('hidden');
            }
          })
          .catch(() => {
            resultsContainer.classList.add('hidden');
          });
      }, 250);
    });

    document.addEventListener('click', function(e) {
      if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
        resultsContainer.classList.add('hidden');
      }
    });
  }
});

