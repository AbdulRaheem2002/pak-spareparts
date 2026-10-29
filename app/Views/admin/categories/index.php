<!-- Admin Categories Management -->
<div class="space-y-6" x-data="{ editMode: false, currentCat: { id: 0, name: '', slug: '', description: '', sort_order: 0 } }">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">Motorcycle Categories</h2>
      <p class="text-xs text-slate-500">Organize motorcycle systems, parts categories and catalog navigation</p>
    </div>
    <button type="button" @click="editMode = true; currentCat = { id: 0, name: '', slug: '', description: '', sort_order: 0 }" class="btn-primary text-xs py-2">
      + Add Category &rarr;
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Categories List Table -->
    <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Category</th>
            <th class="py-3 px-4">Slug</th>
            <th class="py-3 px-4 text-center">Parts</th>
            <th class="py-3 px-4 text-center">Order</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($categories as $cat): ?>
            <tr class="hover:bg-slate-50">
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900"><?= e($cat['name']) ?></div>
                <div class="text-[11px] text-slate-400 line-clamp-1"><?= e($cat['description']) ?></div>
              </td>
              <td class="py-3 px-4 font-mono text-slate-500"><?= e($cat['slug']) ?></td>
              <td class="py-3 px-4 text-center font-bold font-mono"><?= (int)$cat['product_count'] ?></td>
              <td class="py-3 px-4 text-center font-mono"><?= (int)$cat['sort_order'] ?></td>
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <button type="button"
                        @click="editMode = true; currentCat = <?= htmlspecialchars(json_encode($cat), ENT_QUOTES, 'UTF-8') ?>"
                        class="p-1 text-slate-600 hover:text-brand-600 font-bold" title="Edit">
                  ✏️
                </button>
                <form action="/admin/categories/delete/<?= (int)$cat['id'] ?>" method="POST" onsubmit="return confirm('Delete this category?');" class="inline">
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

    <!-- Category Form Card -->
    <div class="lg:col-span-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
      <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
        <span x-text="currentCat.id ? 'Edit Category' : 'Create New Category'"></span>
      </h3>

      <form action="/admin/categories/save" method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="id" :value="currentCat.id">

        <div>
          <label class="form-label">Category Name *</label>
          <input type="text" name="name" x-model="currentCat.name" required class="form-input text-xs" placeholder="e.g. Brake System">
        </div>

        <div>
          <label class="form-label">Slug (Optional)</label>
          <input type="text" name="slug" x-model="currentCat.slug" class="form-input text-xs font-mono" placeholder="auto-generated-if-empty">
        </div>

        <div>
          <label class="form-label">Description</label>
          <textarea name="description" x-model="currentCat.description" rows="3" class="form-input text-xs"></textarea>
        </div>

        <div>
          <label class="form-label">Sort Order</label>
          <input type="number" name="sort_order" x-model="currentCat.sort_order" class="form-input text-xs">
        </div>

        <button type="submit" class="w-full btn-primary text-xs py-2">
          Save Category &rarr;
        </button>
      </form>
    </div>

  </div>

</div>

