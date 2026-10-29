<!-- Admin Vehicles Fitment Management -->
<div class="space-y-6" x-data="{ editMode: false, currentV: { id: 0, make: '', model: '', year_start: 2018, year_end: 2024, engine: '', fuel_type: 'Petrol' } }">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">Vehicle Models Database</h2>
      <p class="text-xs text-slate-500">Powering "My Garage" compatibility and fitment matching</p>
    </div>
    <button type="button" @click="editMode = true; currentV = { id: 0, make: '', model: '', year_start: 2018, year_end: 2024, engine: '', fuel_type: 'Petrol' }" class="btn-primary text-xs py-2">
      + Add Vehicle Model &rarr;
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Vehicles List Table -->
    <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Make &amp; Model</th>
            <th class="py-3 px-4">Years</th>
            <th class="py-3 px-4">Engine / Fuel</th>
            <th class="py-3 px-4 text-center">Parts Mapped</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($vehicles as $v): ?>
            <tr class="hover:bg-slate-50">
              <td class="py-3 px-4">
                <span class="font-bold text-slate-900"><?= e($v['make']) ?> <?= e($v['model']) ?></span>
              </td>
              <td class="py-3 px-4 font-mono font-semibold text-slate-700">
                <?= (int)$v['year_start'] ?> - <?= (int)$v['year_end'] ?>
              </td>
              <td class="py-3 px-4 text-slate-600">
                <?= e($v['engine']) ?> (<?= e($v['fuel_type']) ?>)
              </td>
              <td class="py-3 px-4 text-center font-mono font-bold">
                <a href="/parts/<?= urlencode($v['make']) ?>/<?= urlencode($v['model']) ?>/<?= (int)$v['year_start'] ?>" target="_blank" class="text-brand-600 hover:underline">
                  <?= (int)$v['compatible_parts_count'] ?> parts
                </a>
              </td>
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <button type="button"
                        @click="editMode = true; currentV = <?= htmlspecialchars(json_encode($v), ENT_QUOTES, 'UTF-8') ?>"
                        class="p-1 text-slate-600 hover:text-brand-600" title="Edit">
                  ✏️
                </button>
                <form action="/admin/vehicles/delete/<?= (int)$v['id'] ?>" method="POST" onsubmit="return confirm('Delete this vehicle model?');" class="inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="p-1 text-slate-400 hover:text-rose-600" title="Delete">
                    🗑️
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Vehicle Form Card -->
    <div class="lg:col-span-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
      <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
        <span x-text="currentV.id ? 'Edit Vehicle' : 'Add Vehicle Model'"></span>
      </h3>

      <form action="/admin/vehicles/save" method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="id" :value="currentV.id">

        <div>
          <label class="form-label">Make *</label>
          <input type="text" name="make" x-model="currentV.make" required class="form-input text-xs" placeholder="e.g. Toyota">
        </div>

        <div>
          <label class="form-label">Model *</label>
          <input type="text" name="model" x-model="currentV.model" required class="form-input text-xs" placeholder="e.g. Corolla (Grande / Altis)">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="form-label">Year Start *</label>
            <input type="number" name="year_start" x-model="currentV.year_start" required class="form-input text-xs">
          </div>
          <div>
            <label class="form-label">Year End *</label>
            <input type="number" name="year_end" x-model="currentV.year_end" required class="form-input text-xs">
          </div>
        </div>

        <div>
          <label class="form-label">Engine Designation *</label>
          <input type="text" name="engine" x-model="currentV.engine" required class="form-input text-xs" placeholder="e.g. 1.8L 2ZR-FE">
        </div>

        <div>
          <label class="form-label">Fuel Type</label>
          <select name="fuel_type" x-model="currentV.fuel_type" class="form-input text-xs">
            <option value="Petrol">Petrol</option>
            <option value="Diesel">Diesel</option>
            <option value="Hybrid">Hybrid</option>
            <option value="Electric">Electric</option>
            <option value="CNG">CNG</option>
          </select>
        </div>

        <button type="submit" class="w-full btn-primary text-xs py-2">
          Save Vehicle &rarr;
        </button>
      </form>
    </div>

  </div>

</div>

