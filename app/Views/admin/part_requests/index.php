<!-- Admin Part Sourcing Requests ("Can't Find Your Part?") -->
<div class="space-y-6">

  <div class="flex items-center justify-between pb-4 border-b border-slate-200">
    <div>
      <h2 class="text-xl font-bold uppercase font-condensed tracking-wider text-slate-900">
        Part Sourcing Inquiries
      </h2>
      <p class="text-xs text-slate-500">Inquiries submitted by customers looking for rare, JDM, or discontinued parts</p>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-slate-600 uppercase font-condensed font-bold border-b border-slate-200">
          <tr>
            <th class="py-3 px-4">Date</th>
            <th class="py-3 px-4">Customer</th>
            <th class="py-3 px-4">Vehicle Info</th>
            <th class="py-3 px-4">Part Requested</th>
            <th class="py-3 px-4">Photo</th>
            <th class="py-3 px-4">Status</th>
            <th class="py-3 px-4 text-right">Contact / Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (!empty($requests)): ?>
            <?php foreach ($requests as $req): ?>
              <tr class="hover:bg-slate-50">
                <td class="py-3 px-4 text-slate-500 font-mono whitespace-nowrap">
                  <?= \App\Helpers\Formatter::date($req['created_at'], 'd M, h:i A') ?>
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900"><?= e($req['name']) ?></div>
                  <div class="font-mono text-brand-600 font-bold"><?= e($req['phone']) ?></div>
                </td>
                <td class="py-3 px-4 font-semibold text-slate-800"><?= e($req['vehicle_info']) ?></td>
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900"><?= e($req['part_name']) ?></div>
                  <?php if (!empty($req['part_number'])): ?>
                    <div class="font-mono text-[10px] text-slate-500">OEM: <?= e($req['part_number']) ?></div>
                  <?php endif; ?>
                  <?php if (!empty($req['description'])): ?>
                    <div class="text-[11px] text-slate-400 mt-0.5 line-clamp-1"><?= e($req['description']) ?></div>
                  <?php endif; ?>
                </td>
                <td class="py-3 px-4">
                  <?php if (!empty($req['photo'])): ?>
                    <a href="<?= e($req['photo']) ?>" target="_blank" class="block w-10 h-10 rounded border border-slate-200 overflow-hidden bg-slate-50">
                      <img src="<?= e($req['photo']) ?>" alt="Sample" class="w-full h-full object-cover">
                    </a>
                  <?php else: ?>
                    <span class="text-slate-400">—</span>
                  <?php endif; ?>
                </td>
                <td class="py-3 px-4">
                  <form action="/admin/part-requests/status/<?= (int)$req['id'] ?>" method="POST">
                    <?= csrf_field() ?>
                    <select name="status" onchange="this.form.submit()" class="form-input text-[11px] py-1 font-bold">
                      <?php foreach (['New', 'Quoted', 'Fulfilled', 'Closed'] as $st): ?>
                        <option value="<?= $st ?>" <?= $req['status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <?php
                    $cleanPhone = whatsapp_clean_phone($req['phone']);
                    $waMsg = "Salam {$req['name']}! Regarding your part inquiry for {$req['vehicle_info']} ({$req['part_name']}): We have located availability at our Lahore warehouse.";
                  ?>
                  <a href="https://wa.me/<?= e($cleanPhone) ?>?text=<?= urlencode($waMsg) ?>" target="_blank" rel="noopener" class="btn-whatsapp text-[11px] py-1 px-2.5">
                    WhatsApp Quote
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="py-8 text-center text-slate-400">No part sourcing inquiries yet.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

