<!-- Admin Settings & Business Configuration View -->
<div class="space-y-6">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">
        Store &amp; Payment Configuration
      </h2>
      <p class="text-xs text-slate-500">Manage business contact info, bank accounts, mobile wallets, and shipping rates</p>
    </div>

    <!-- DB Backup Button -->
    <a href="/admin/backup-db" class="btn-outline text-xs py-2 flex items-center gap-1.5 text-emerald-700 border-emerald-300 hover:bg-emerald-50">
      <span>💾</span> Download Full Database Backup (.sql)
    </a>
  </div>

  <form action="/admin/settings/save" method="POST" class="space-y-8">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

      <!-- LEFT: GENERAL & LOGISTICS (7 Cols) -->
      <div class="lg:col-span-7 space-y-6">

        <!-- General Store Info -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Store Identity &amp; Contact
          </h3>

          <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Store Brand Name *</label>
                <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? 'PakAutoParts') ?>" required class="form-input text-xs font-bold">
              </div>

              <div>
                <label class="form-label">Store Slogan / Tagline</label>
                <input type="text" name="site_tagline" value="<?= e($settings['site_tagline'] ?? '') ?>" class="form-input text-xs">
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Display Phone Number *</label>
                <input type="text" name="contact_phone" value="<?= e($settings['contact_phone'] ?? '0300-1234567') ?>" required class="form-input text-xs font-mono">
              </div>

              <div>
                <label class="form-label">WhatsApp Number (e.g. 923001234567) *</label>
                <input type="text" name="contact_whatsapp" value="<?= e($settings['contact_whatsapp'] ?? '923001234567') ?>" required class="form-input text-xs font-mono">
                <span class="text-[10px] text-slate-400 mt-0.5 block">Used for direct wa.me ordering</span>
              </div>
            </div>

            <div>
              <label class="form-label">Customer Support Email</label>
              <input type="email" name="contact_email" value="<?= e($settings['contact_email'] ?? 'support@pakautoparts.pk') ?>" class="form-input text-xs">
            </div>

            <div>
              <label class="form-label">Physical Warehouse / Shop Address</label>
              <textarea name="contact_address" rows="2" class="form-input text-xs"><?= e($settings['contact_address'] ?? '') ?></textarea>
            </div>

            <div>
              <label class="form-label">Top Banner Announcement Text</label>
              <input type="text" name="announcement_banner" value="<?= e($settings['announcement_banner'] ?? '') ?>" class="form-input text-xs">
            </div>
          </div>
        </div>

        <!-- Shipping & Delivery Fees -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Nationwide Courier Rates &amp; COD Fees
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="form-label">Default Shipping Fee (PKR)</label>
              <input type="number" name="default_shipping_fee" value="<?= e($settings['default_shipping_fee'] ?? '250') ?>" class="form-input text-xs font-mono">
            </div>

            <div>
              <label class="form-label">Cash on Delivery (COD) Fee</label>
              <input type="number" name="cod_fee" value="<?= e($settings['cod_fee'] ?? '50') ?>" class="form-input text-xs font-mono">
            </div>

            <div>
              <label class="form-label">Free Delivery Min Spend (PKR)</label>
              <input type="number" name="free_shipping_min" value="<?= e($settings['free_shipping_min'] ?? '5000') ?>" class="form-input text-xs font-mono">
            </div>
          </div>
        </div>

      </div>

      <!-- RIGHT: PAYMENT GATEWAY ACCOUNTS (5 Cols) -->
      <div class="lg:col-span-5 space-y-6">

        <!-- Bank Account -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Bank Transfer Details (Meezan Bank)
          </h3>

          <div class="space-y-3">
            <div>
              <label class="form-label">Bank Name</label>
              <input type="text" name="bank_name" value="<?= e($settings['bank_name'] ?? 'Meezan Bank Limited') ?>" class="form-input text-xs">
            </div>

            <div>
              <label class="form-label">Account Title</label>
              <input type="text" name="bank_title" value="<?= e($settings['bank_title'] ?? '') ?>" class="form-input text-xs">
            </div>

            <div>
              <label class="form-label">Account Number</label>
              <input type="text" name="bank_account_no" value="<?= e($settings['bank_account_no'] ?? '') ?>" class="form-input text-xs font-mono">
            </div>

            <div>
              <label class="form-label">IBAN</label>
              <input type="text" name="bank_iban" value="<?= e($settings['bank_iban'] ?? '') ?>" class="form-input text-xs font-mono uppercase">
            </div>
          </div>
        </div>

        <!-- Mobile Wallets -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider font-condensed text-slate-900 border-b border-slate-100 pb-2">
            Mobile Wallets (JazzCash &amp; Easypaisa)
          </h3>

          <div class="space-y-3">
            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="form-label">JazzCash Title</label>
                <input type="text" name="jazzcash_title" value="<?= e($settings['jazzcash_title'] ?? '') ?>" class="form-input text-xs">
              </div>
              <div>
                <label class="form-label">JazzCash Mobile #</label>
                <input type="text" name="jazzcash_no" value="<?= e($settings['jazzcash_no'] ?? '') ?>" class="form-input text-xs font-mono">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
              <div>
                <label class="form-label">Easypaisa Title</label>
                <input type="text" name="easypaisa_title" value="<?= e($settings['easypaisa_title'] ?? '') ?>" class="form-input text-xs">
              </div>
              <div>
                <label class="form-label">Easypaisa Mobile #</label>
                <input type="text" name="easypaisa_no" value="<?= e($settings['easypaisa_no'] ?? '') ?>" class="form-input text-xs font-mono">
              </div>
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <button type="submit" class="w-full btn-primary py-3 text-xs shadow-md">
          Save Settings &rarr;
        </button>

      </div>

    </div>
  </form>

</div>

