<!-- Admin Reviews Moderation -->
<div class="space-y-6">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">Product Reviews Moderation</h2>
      <p class="text-xs text-slate-500">Approve or reject customer verified feedback</p>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Part</th>
            <th class="py-3 px-4">Customer</th>
            <th class="py-3 px-4">Rating</th>
            <th class="py-3 px-4">Feedback Comment</th>
            <th class="py-3 px-4 text-center">Status</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $r): ?>
              <tr class="hover:bg-slate-50">
                <td class="py-3 px-4 font-bold text-slate-900 max-w-[180px] truncate" title="<?= e($r['product_name']) ?>">
                  <a href="/product/<?= e($r['product_slug']) ?>" target="_blank" class="hover:text-brand-600">
                    <?= e($r['product_name']) ?>
                  </a>
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900"><?= e($r['customer_name']) ?></div>
                  <div class="text-[10px] text-slate-400 font-mono"><?= e($r['customer_phone'] ?: 'No phone') ?></div>
                </td>
                <td class="py-3 px-4 text-amber-500 font-bold whitespace-nowrap">
                  ★ <?= (int)$r['rating'] ?> / 5
                </td>
                <td class="py-3 px-4 text-slate-700 max-w-xs leading-relaxed">
                  <?= e($r['comment']) ?>
                </td>
                <td class="py-3 px-4 text-center">
                  <?php if ((int)$r['is_approved'] === 1): ?>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded">Approved</span>
                  <?php else: ?>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded">Pending</span>
                  <?php endif; ?>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <form action="/admin/reviews/toggle/<?= (int)$r['id'] ?>" method="POST" class="inline">
                      <?= csrf_field() ?>
                      <button type="submit" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-bold">
                        <?= (int)$r['is_approved'] === 1 ? 'Unapprove' : 'Approve' ?>
                      </button>
                    </form>
                    <form action="/admin/reviews/delete/<?= (int)$r['id'] ?>" method="POST" onsubmit="return confirm('Delete review?');" class="inline">
                      <?= csrf_field() ?>
                      <button type="submit" class="p-1 text-slate-400 hover:text-rose-600" title="Delete">
                        🗑️
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="py-8 text-center text-slate-400">No customer reviews to moderate.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

