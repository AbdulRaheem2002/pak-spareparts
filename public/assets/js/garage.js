/**
 * PakAutoParts - "My Garage" Client Vehicle Manager
 * Persists selected vehicle in localStorage & synchronizes cookie for SSR catalog filtering.
 */

window.Garage = {
  STORAGE_KEY: 'pak_garage_vehicle',

  get: function() {
    try {
      const data = localStorage.getItem(this.STORAGE_KEY);
      return data ? JSON.parse(data) : null;
    } catch (e) {
      return null;
    }
  },

  set: function(vehicle) {
    if (!vehicle || !vehicle.id) return;
    try {
      localStorage.setItem(this.STORAGE_KEY, JSON.stringify(vehicle));
      // Set 30-day cookie for server-side fitment queries
      document.cookie = "my_garage_id=" + encodeURIComponent(vehicle.id) + "; path=/; max-age=" + (86400 * 30) + "; SameSite=Lax";
      window.dispatchEvent(new CustomEvent('garage:changed', { detail: vehicle }));
    } catch (e) {
      console.error("Garage storage error:", e);
    }
  },

  clear: function() {
    try {
      localStorage.removeItem(this.STORAGE_KEY);
      document.cookie = "my_garage_id=; path=/; max-age=0; SameSite=Lax";
      window.dispatchEvent(new CustomEvent('garage:changed', { detail: null }));
    } catch (e) {
      console.error("Garage clear error:", e);
    }
  },

  initUI: function() {
    const v = this.get();
    const displayElements = document.querySelectorAll('[data-garage-display]');
    const activeIndicators = document.querySelectorAll('[data-garage-active]');
    const emptyIndicators = document.querySelectorAll('[data-garage-empty]');

    if (v) {
      displayElements.forEach(el => {
        el.textContent = v.make + " " + v.model + " (" + v.year_start + "-" + v.year_end + ") " + (v.engine || "");
      });
      activeIndicators.forEach(el => el.classList.remove('hidden'));
      emptyIndicators.forEach(el => el.classList.add('hidden'));
    } else {
      activeIndicators.forEach(el => el.classList.add('hidden'));
      emptyIndicators.forEach(el => el.classList.remove('hidden'));
    }
  }
};

document.addEventListener('DOMContentLoaded', function() {
  window.Garage.initUI();
  window.addEventListener('garage:changed', function() {
    window.Garage.initUI();
  });
});

