<!-- My Garage Vehicle Selector Modal -->
<div id="garage-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/75 transition-opacity" onclick="document.getElementById('garage-modal').classList.add('hidden')"></div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

    <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
      
      <div class="bg-chassis-900 px-6 py-4 flex items-center justify-between text-white border-b border-slate-800">
        <div class="flex items-center gap-2">
          <span class="text-xl">🚗</span>
          <h3 class="text-base font-bold uppercase tracking-wider font-condensed" id="modal-title">Select Your Vehicle (My Garage)</h3>
        </div>
        <button type="button" onclick="document.getElementById('garage-modal').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl font-bold">&times;</button>
      </div>

      <div class="p-6">
        <p class="text-xs text-slate-600 mb-4">
          Save your car or motorcycle to guarantee 100% exact part fitment and filter out non-compatible parts across our entire catalog.
        </p>

        <!-- Current Saved Vehicle Banner -->
        <div data-garage-active class="hidden mb-5 p-3.5 bg-emerald-50 border border-emerald-200 rounded-lg flex items-center justify-between">
          <div class="flex items-center gap-2.5">
            <span class="text-emerald-600 text-lg">✓</span>
            <div>
              <div class="text-[11px] font-bold uppercase text-emerald-800 tracking-wider">Active Garage Vehicle</div>
              <div class="text-sm font-bold text-slate-900" data-garage-display>—</div>
            </div>
          </div>
          <button type="button" onclick="Garage.clear(); window.location.reload();" class="text-xs font-semibold text-rose-600 hover:text-rose-800 underline">
            Remove
          </button>
        </div>

        <!-- Vehicle Selection Form -->
        <form id="garage-modal-form" onsubmit="event.preventDefault(); handleGarageModalSubmit();">
          <div class="space-y-3.5">
            <div>
              <label class="form-label">1. Select Vehicle Make</label>
              <select id="modal-make-select" class="form-input" onchange="loadGarageModels(this.value)" required>
                <option value="">-- Choose Make (e.g. Toyota, Honda, Suzuki) --</option>
                <option value="Toyota">Toyota</option>
                <option value="Honda">Honda</option>
                <option value="Suzuki">Suzuki</option>
                <option value="KIA">KIA</option>
                <option value="Hyundai">Hyundai</option>
                <option value="Yamaha">Yamaha</option>
              </select>
            </div>

            <div>
              <label class="form-label">2. Select Vehicle Model</label>
              <select id="modal-model-select" class="form-input" onchange="loadGarageYears()" disabled required>
                <option value="">-- First Select Make --</option>
              </select>
            </div>

            <div>
              <label class="form-label">3. Select Production Year</label>
              <select id="modal-year-select" class="form-input" disabled required>
                <option value="">-- First Select Model --</option>
              </select>
            </div>
          </div>

          <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('garage-modal').classList.add('hidden')" class="btn-outline">
              Cancel
            </button>
            <button type="submit" id="garage-save-btn" class="btn-primary" disabled>
              Save to My Garage &rarr;
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function loadGarageModels(make) {
  const modelSelect = document.getElementById('modal-model-select');
  const yearSelect = document.getElementById('modal-year-select');
  const saveBtn = document.getElementById('garage-save-btn');

  modelSelect.innerHTML = '<option value="">Loading models...</option>';
  modelSelect.disabled = true;
  yearSelect.innerHTML = '<option value="">-- First Select Model --</option>';
  yearSelect.disabled = true;
  saveBtn.disabled = true;

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

function loadGarageYears() {
  const make = document.getElementById('modal-make-select').value;
  const model = document.getElementById('modal-model-select').value;
  const yearSelect = document.getElementById('modal-year-select');
  const saveBtn = document.getElementById('garage-save-btn');

  yearSelect.innerHTML = '<option value="">Loading years...</option>';
  yearSelect.disabled = true;
  saveBtn.disabled = true;

  if (!model) return;

  fetch('/garage/options?make=' + encodeURIComponent(make) + '&model=' + encodeURIComponent(model))
    .then(r => r.json())
    .then(data => {
      let opts = '<option value="">-- Choose Year / Variant --</option>';
      data.vehicles.forEach(v => {
        opts += `<option value="${v.id}" data-obj='${JSON.stringify(v)}'>${v.year_start}-${v.year_end} (${v.engine})</option>`;
      });
      yearSelect.innerHTML = opts;
      yearSelect.disabled = false;
      yearSelect.onchange = () => { saveBtn.disabled = !yearSelect.value; };
    });
}

function handleGarageModalSubmit() {
  const yearSelect = document.getElementById('modal-year-select');
  const selectedOpt = yearSelect.options[yearSelect.selectedIndex];
  if (!selectedOpt || !selectedOpt.dataset.obj) return;

  const vehicle = JSON.parse(selectedOpt.dataset.obj);
  Garage.set(vehicle);
  document.getElementById('garage-modal').classList.add('hidden');
  window.showToast("Vehicle set to: " + vehicle.make + " " + vehicle.model, "success");
  setTimeout(() => {
    window.location.reload();
  }, 400);
}
</script>

