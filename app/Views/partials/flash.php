<!-- Flash Messages Partial -->
<?php if (!empty($flash_success)): ?>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 no-print">
    <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg shadow-sm flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="text-emerald-600 text-lg">✓</span>
        <p class="text-sm font-semibold text-emerald-800"><?= e($flash_success) ?></p>
      </div>
      <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
    </div>
  </div>
<?php endif; ?>

<?php if (!empty($flash_error)): ?>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 no-print">
    <div class="p-4 bg-rose-50 border-l-4 border-rose-500 rounded-r-lg shadow-sm flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="text-rose-600 text-lg">⚠</span>
        <p class="text-sm font-semibold text-rose-800"><?= e($flash_error) ?></p>
      </div>
      <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
    </div>
  </div>
<?php endif; ?>

<?php if (!empty($flash_info)): ?>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 no-print">
    <div class="p-4 bg-sky-50 border-l-4 border-sky-500 rounded-r-lg shadow-sm flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="text-sky-600 text-lg">ℹ</span>
        <p class="text-sm font-semibold text-sky-800"><?= e($flash_info) ?></p>
      </div>
      <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-sky-600 hover:text-sky-900 font-bold">&times;</button>
    </div>
  </div>
<?php endif; ?>

