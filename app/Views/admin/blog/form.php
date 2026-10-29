<!-- Admin Blog Create/Edit View with Quill 2 -->
<?php
  $isEdit = !empty($post);
  $action = $isEdit ? "/admin/blog/update/{$post['id']}" : "/admin/blog/store";
?>

<div class="space-y-6">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">
        <?= $isEdit ? 'Edit Guide / Article' : 'Write Maintenance Guide' ?>
      </h2>
      <p class="text-xs text-slate-500">Produce educational automotive content to rank on Google</p>
    </div>
    <a href="/admin/blog" class="btn-outline text-xs">
      &larr; Back to Articles
    </a>
  </div>

  <form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="space-y-6" id="blog-form">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- LEFT: CONTENT & QUILL (8 Cols) -->
      <div class="lg:col-span-8 space-y-6">
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <div>
            <label class="form-label">Article Title *</label>
            <input type="text"
                   name="title"
                   value="<?= e($post['title'] ?? '') ?>"
                   required
                   placeholder="e.g. How to Spot Fake vs Genuine Toyota Spare Parts in Pakistan"
                   class="form-input text-xs font-bold">
          </div>

          <?php if ($isEdit): ?>
            <div>
              <label class="form-label">Slug</label>
              <input type="text" name="slug" value="<?= e($post['slug']) ?>" class="form-input text-xs font-mono">
            </div>
          <?php endif; ?>

          <div>
            <label class="form-label">Article Excerpt / Introduction Summary</label>
            <textarea name="excerpt" rows="2" class="form-input text-xs" placeholder="Brief 2-line summary for article listing cards..."><?= e($post['excerpt'] ?? '') ?></textarea>
          </div>

          <div>
            <label class="form-label">Article Body Content (Quill Rich Text)</label>
            <input type="hidden" name="content" id="hidden-blog-content" value="<?= htmlspecialchars($post['content'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <div id="blog-quill" class="h-96 bg-white rounded-b-lg border border-slate-300">
              <?= $post['content'] ?? '' ?>
            </div>
          </div>
        </div>

        <!-- SEO Box -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Technical SEO Snippet
          </h3>

          <div class="space-y-3">
            <div>
              <label class="form-label">Focus Keyword</label>
              <input type="text" name="focus_keyword" value="<?= e($post['focus_keyword'] ?? '') ?>" placeholder="e.g. fake vs genuine toyota parts pakistan" class="form-input text-xs">
            </div>

            <div>
              <label class="form-label">SEO Meta Title</label>
              <input type="text" name="meta_title" value="<?= e($post['meta_title'] ?? '') ?>" class="form-input text-xs">
            </div>

            <div>
              <label class="form-label">SEO Meta Description</label>
              <textarea name="meta_description" rows="2" class="form-input text-xs"><?= e($post['meta_description'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

      </div>

      <!-- RIGHT: PUBLISH & META (4 Cols) -->
      <div class="lg:col-span-4 space-y-6">
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Publish Settings
          </h3>

          <div class="space-y-3">
            <div>
              <label class="form-label">Category</label>
              <select name="category_id" class="form-input text-xs">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= (int)$cat['id'] ?>" <?= ($post['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div>
              <label class="form-label">Tags (Comma Separated)</label>
              <input type="text" name="tags" value="<?= e($post['tags'] ?? '') ?>" placeholder="Toyota, Oil Filter, Maintenance" class="form-input text-xs">
            </div>

            <div>
              <label class="form-label">Featured Image (WebP / JPG / PNG)</label>
              <input type="file" name="featured_image" accept="image/*" class="form-input text-xs py-1.5">
            </div>

            <?php if (!empty($post['featured_image'])): ?>
              <div class="aspect-video rounded-lg overflow-hidden border border-slate-200 bg-slate-50">
                <img src="<?= e($post['featured_image']) ?>" alt="Featured" class="w-full h-full object-cover">
              </div>
            <?php endif; ?>

            <div class="pt-2">
              <label class="flex items-center gap-2 text-xs font-semibold text-slate-800 cursor-pointer">
                <input type="checkbox" name="is_published" value="1" <?= (!isset($post) || !empty($post['is_published'])) ? 'checked' : '' ?> class="rounded text-brand-500">
                <span>Publish Immediately</span>
              </label>
            </div>

            <div class="pt-4 border-t border-slate-100">
              <button type="submit" class="w-full btn-primary py-3 text-xs shadow-md">
                <?= $isEdit ? 'Save Changes' : 'Publish Article' ?> &rarr;
              </button>
            </div>
          </div>
        </div>

      </div>

    </div>
  </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const quill = new Quill('#blog-quill', {
    theme: 'snow',
    placeholder: 'Write your comprehensive article with headings, images, and tips...',
    modules: {
      toolbar: [
        [{ 'header': [2, 3, 4, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link', 'blockquote', 'code-block', 'clean']
      ]
    }
  });

  const form = document.getElementById('blog-form');
  const hiddenContent = document.getElementById('hidden-blog-content');

  form.addEventListener('submit', function() {
    hiddenContent.value = quill.root.innerHTML;
  });
});
</script>

