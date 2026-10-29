<!-- Part Request Sourcing Form -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 font-medium">
    <?php foreach ($breadcrumbs as $idx => $bc): ?>
      <?php if ($idx > 0): ?>
        <span>/</span>
      <?php endif; ?>
      <?php if (!empty($bc['url'])): ?>
        <a href="<?= e($bc['url']) ?>" class="hover:text-brand-600 transition-colors"><?= e($bc['title']) ?></a>
      <?php else: ?>
        <span class="text-slate-900 font-bold"><?= e($bc['title']) ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>

  <div class="text-center max-w-xl mx-auto mb-8 space-y-2">
    <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-50 text-brand-600 rounded-full text-xs font-bold uppercase font-condensed">
      <span>🔍</span> Dedicated Sourcing Desk
    </div>
    <h1 class="text-2xl sm:text-3xl font-extrabold uppercase font-condensed tracking-tight text-slate-900">
      Can't Find Your Part? Request Sourcing
    </h1>
    <p class="text-xs text-slate-500 leading-relaxed">
      Looking for a rare, Japanese import (JDM), Kabli, or discontinued spare part? Fill out the form below or upload a photo of your old part. We locate it through our Lahore Montgomery Road &amp; Karachi wholesale network.
    </p>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 max-w-2xl mx-auto">
    <form action="/request-part" method="POST" enctype="multipart/form-data" class="space-y-5">
      <?= csrf_field() ?>

      <div class="space-y-4">
        <!-- Vehicle Info -->
        <div>
          <label class="form-label">1. Vehicle Information (Make, Model, Year, Engine) *</label>
          <input type="text"
                 name="vehicle_info"
                 value="<?= e($vehicleInfo) ?>"
                 required
                 placeholder="e.g. Toyota Corolla 2017 GLi 1.3L Manual (or Chassis #)"
                 class="form-input text-xs">
          <span class="text-[10px] text-slate-400 mt-1 block">Mentioning your chassis / VIN number guarantees exact fitment.</span>
        </div>

        <!-- Part Details -->
        <div>
          <label class="form-label">2. Part Name / Component Description *</label>
          <input type="text"
                 name="part_name"
                 value="<?= e($partName) ?>"
                 required
                 placeholder="e.g. Front Right Side Mirror with Indicator, Steering Rack, Throttle Body"
                 class="form-input text-xs">
        </div>

        <div>
          <label class="form-label">3. OEM or Part Number (If Visible on Old Part)</label>
          <input type="text"
                 name="part_number"
                 placeholder="e.g. 87910-02H00, IK20, D1210 (Optional)"
                 class="form-input text-xs font-mono uppercase">
        </div>

        <!-- Photo of Old Part -->
        <div>
          <label class="form-label">4. Upload Photo of Old Part or Defect (Optional)</label>
          <input type="file" name="part_photo" accept="image/*" class="form-input text-xs py-1.5">
          <span class="text-[10px] text-slate-400 mt-1 block">Helps our technicians identify exact mount pins and plug connectors.</span>
        </div>

        <!-- Contact Information -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
          <div>
            <label class="form-label">Your Name *</label>
            <input type="text" name="name" required placeholder="e.g. Hamza Ali" class="form-input text-xs">
          </div>

          <div>
            <label class="form-label">Pakistani Mobile / WhatsApp *</label>
            <input type="tel" name="phone" required placeholder="0300-1234567" class="form-input text-xs">
          </div>

          <div class="sm:col-span-2">
            <label class="form-label">Email Address (Optional)</label>
            <input type="email" name="email" placeholder="name@domain.com" class="form-input text-xs">
          </div>
        </div>

        <div>
          <label class="form-label">Additional Instructions / Urgency</label>
          <textarea name="description" rows="2" class="form-input text-xs" placeholder="e.g. Need genuine Japanese only, urgent delivery required in Islamabad..."></textarea>
        </div>
      </div>

      <div class="pt-4 border-t border-slate-100">
        <button type="submit" class="w-full btn-primary py-3 text-xs shadow-lg flex items-center justify-center gap-2">
          <span>Submit Request &amp; Open WhatsApp Chat</span> &rarr;
        </button>
      </div>

    </form>
  </div>

</div>

