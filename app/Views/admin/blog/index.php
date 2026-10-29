<!-- Admin Blog Posts List View -->
<div class="space-y-6">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">
        Maintenance Guides &amp; Blog Posts
      </h2>
      <p class="text-xs text-slate-500">Publish vehicle maintenance articles and technical SEO buying guides</p>
    </div>
    <a href="/admin/blog/create" class="btn-primary text-xs py-2">
      + Write Article &rarr;
    </a>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Article Title</th>
            <th class="py-3 px-4">Category</th>
            <th class="py-3 px-4 text-center">Read Time</th>
            <th class="py-3 px-4 text-center">Views</th>
            <th class="py-3 px-4 text-center">Status</th>
            <th class="py-3 px-4">Date</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
              <tr class="hover:bg-slate-50">
                <td class="py-3 px-4 font-bold text-slate-900 max-w-sm truncate">
                  <a href="/blog/<?= e($post['slug']) ?>" target="_blank" class="hover:text-brand-600">
                    <?= e($post['title']) ?>
                  </a>
                </td>
                <td class="py-3 px-4 text-slate-600"><?= e($post['category_name'] ?? 'General') ?></td>
                <td class="py-3 px-4 text-center font-mono"><?= (int)$post['reading_time_min'] ?>m</td>
                <td class="py-3 px-4 text-center font-mono font-bold"><?= number_format((float)$post['view_count']) ?></td>
                <td class="py-3 px-4 text-center">
                  <?php if ((int)$post['is_published'] === 1): ?>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded">Published</span>
                  <?php else: ?>
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded">Draft</span>
                  <?php endif; ?>
                </td>
                <td class="py-3 px-4 text-slate-500 font-mono whitespace-nowrap">
                  <?= \App\Helpers\Formatter::date($post['created_at'], 'd M Y') ?>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <a href="/admin/blog/edit/<?= (int)$post['id'] ?>" class="p-1 text-slate-600 hover:text-brand-600" title="Edit">
                      ✏️
                    </a>
                    <form action="/admin/blog/delete/<?= (int)$post['id'] ?>" method="POST" onsubmit="return confirm('Delete article?');" class="inline">
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
              <td colspan="7" class="py-8 text-center text-slate-400">No blog articles published yet.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

