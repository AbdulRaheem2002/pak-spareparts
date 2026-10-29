<!-- Admin Coupons Management -->
<div class="space-y-6" x-data="{ editMode: false, currentC: { id: 0, code: '', discount_type: 'percent', discount_value: 10, min_spend: 3000, max_discount: 1500, usage_limit: 100, expiry_date: '', is_active: 1 } }">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">Coupons &amp; Promo Codes</h2>
      <p class="text-xs text-slate-500">Configure percentage or fixed PKR discounts with minimum spend constraints</p>
    </div>
    <button type="button" @click="editMode = true; currentC = { id: 0, code: '', discount_type: 'percent', discount_value: 10, min_spend: 3000, max_discount: 1500, usage_limit: 100, expiry_date: '', is_active: 1 }" class="btn-primary text-xs py-2">
      + Create Coupon &rarr;
    </button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Coupons List Table -->
    <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Coupon Code</th>
            <th class="py-3 px-4">Discount</th>
            <th class="py-3 px-4">Min Spend</th>
            <th class="py-3 px-4 text-center">Used</th>
            <th class="py-3 px-4">Expiry</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($coupons as $c): ?>
            <tr class="hover:bg-slate-50">
              <td class="py-3 px-4">
                <span class="font-mono font-bold text-slate-900 px-2 py-0.5 bg-slate-100 rounded border border-slate-200">
                  <?= e($c['code']) ?>
                </span>
              </td>
              <td class="py-3 px-4 font-bold text-emerald-600">
                <?= $c['discount_type'] === 'percent' ? (float)$c['discount_value'] . '%' : format_pkr($c['discount_value']) ?>
              </td>
              <td class="py-3 px-4 text-slate-600"><?= format_pkr($c['min_spend']) ?></td>
              <td class="py-3 px-4 text-center font-mono">
                <?= (int)$c['usage_count'] ?> <?= $c['usage_limit'] ? '/ ' . (int)$c['usage_limit'] : '' ?>
              </td>
              <td class="py-3 px-4 text-slate-500 font-mono"><?= e($c['expiry_date'] ?: 'No Expiry') ?></td>
              <td class="py-3 px-4 text-right whitespace-nowrap">
                <form action="/admin/coupons/delete/<?= (int)$c['id'] ?>" method="POST" onsubmit="return confirm('Delete this coupon?');" class="inline">
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

    <!-- Coupon Form Card -->
    <div class="lg:col-span-4 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
      <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
        <span x-text="currentC.id ? 'Edit Coupon' : 'Create New Coupon'"></span>
      </h3>

      <form action="/admin/coupons/save" method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="id" :value="currentC.id">

        <div>
          <label class="form-label">Promo Code *</label>
          <input type="text" name="code" x-model="currentC.code" required class="form-input text-xs font-mono uppercase" placeholder="e.g. PAKSAVE10">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="form-label">Type</label>
            <select name="discount_type" x-model="currentC.discount_type" class="form-input text-xs">
              <option value="percent">Percentage (%)</option>
              <option value="fixed">Fixed (PKR)</option>
            </select>
          </div>
          <div>
            <label class="form-label">Discount Value *</label>
            <input type="number" step="0.5" name="discount_value" x-model="currentC.discount_value" required class="form-input text-xs font-mono">
          </div>
        </div>

        <div>
          <label class="form-label">Minimum Order Spend (PKR)</label>
          <input type="number" name="min_spend" x-model="currentC.min_spend" class="form-input text-xs font-mono">
        </div>

        <div>
          <label class="form-label">Max Discount Cap (PKR, for % coupons)</label>
          <input type="number" name="max_discount" x-model="currentC.max_discount" placeholder="Leave empty for unlimited" class="form-input text-xs font-mono">
        </div>

        <div>
          <label class="form-label">Expiry Date</label>
          <input type="date" name="expiry_date" x-model="currentC.expiry_date" class="form-input text-xs">
        </div>

        <div>
          <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" :checked="currentC.is_active == 1" class="rounded text-brand-500">
            <span>Coupon Active</span>
          </label>
        </div>

        <button type="submit" class="w-full btn-primary text-xs py-2">
          Save Coupon &rarr;
        </button>
      </form>
    </div>

  </div>

</div>

