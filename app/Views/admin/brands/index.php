<!-- Admin Brands Management -->
<div class="space-y-6" x-data="{ editMode: false, currentBrand: { id: 0, name: '', slug: '', description: '', is_popular: 0 } }">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">Auto Parts Brands</h2>
      <p class="text-xs text-slate-500">Manage manufacturers (Toyota, Denso, NGK, Bosch, AISIN, etc.)</p>
    </div>
    <button type="button" @click="editMode = true; currentBrand = { id: 0, name: '', slug: '', description: '', is_popular: 0 }" class="btn-primary text-xs py-2">
      + Add Brand &rarr;
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Brands List Table -->
    <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Brand</th>
            <th class="py-3 px-4">Slug</th>
            <th class="py-3 px-4 text-center">Parts Count</th>
            <th class="py-3 px-4 text-center">Popular Badge</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($brands as $b): ?>
            <tr class="hover:bg-slate-50">
              <td class="py-3 px-4 font-bold text-slate-900"><?= e($b['name']) ?></td>
              <td class="py-3 px-4 font-mono text-slate-500"><?= e($b['slug']) ?></td>
              <td class="py-3 px-4 text-center font-bold font-mono"><?= (int)$b['product_count'] ?></td>
              <td class="py-3 px-4 text-center">
                <?php if (!empty($b['is_popular'])): ?>
                  <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded">Featured</span>
                <?php else: ?>
                  <span class="text-slate-400">—</span>
                <?php endif; ?>
              </td>
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <button type="button"
                        @click="editMode = true; currentBrand = <?= htmlspecialchars(json_encode($b), ENT_QUOTES, 'UTF-8') ?>"
                        class="p-1 text-slate-600 hover:text-brand-600" title="Edit">
                  ✏️
                </button>
                <form action="/admin/brands/delete/<?= (int)$b['id'] ?>" method="POST" onsubmit="return confirm('Delete this brand?');" class="inline">
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

    <!-- Brand Form Card -->
    <div class="lg:col-span-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
      <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
        <span x-text="currentBrand.id ? 'Edit Brand' : 'Create New Brand'"></span>
      </h3>

      <form action="/admin/brands/save" method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="id" :value="currentBrand.id">

        <div>
          <label class="form-label">Brand Name *</label>
          <input type="text" name="name" x-model="currentBrand.name" required class="form-input text-xs" placeholder="e.g. Denso">
        </div>

        <div>
          <label class="form-label">Slug (Optional)</label>
          <input type="text" name="slug" x-model="currentBrand.slug" class="form-input text-xs font-mono">
        </div>

        <div>
          <label class="form-label">Description</label>
          <textarea name="description" x-model="currentBrand.description" rows="2" class="form-input text-xs"></textarea>
        </div>

        <div>
          <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
            <input type="checkbox" name="is_popular" value="1" :checked="currentBrand.is_popular == 1" class="rounded text-brand-500">
            <span>Show on Homepage "Certified Brands"</span>
          </label>
        </div>

        <button type="submit" class="w-full btn-primary text-xs py-2">
          Save Brand &rarr;
        </button>
      </form>
    </div>

  </div>

</div>

