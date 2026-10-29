<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Admin Sign In | PakBikeParts') ?></title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#0F172A">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Tailwind CSS Engine -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#fff7ed',
              100: '#ffedd5',
              500: '#f97316',
              600: '#ea580c',
              700: '#c2410c',
              800: '#9a3412',
            },
            chassis: {
              800: '#1e293b',
              900: '#0f172a',
              950: '#090d16',
            }
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            condensed: ['Chakra Petch', 'sans-serif'],
          }
        }
      }
    };
  </script>

  <style>
    body {
      background: radial-gradient(circle at 50% 20%, #1e293b 0%, #090d16 100%);
      font-family: 'Inter', sans-serif;
    }
    .font-condensed {
      font-family: 'Chakra Petch', sans-serif;
    }
  </style>
</head>
<body class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 text-slate-100 antialiased selection:bg-brand-500 selection:text-white">

  <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
    
    <!-- Brand / Logo Header -->
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-amber-500 shadow-lg shadow-brand-500/25 mb-4 border border-brand-400/30">
        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
        </svg>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-wider font-condensed text-white flex items-center justify-center gap-2">
        <span>PAK<span class="text-brand-500">BIKE</span>PARTS</span>
      </h1>
      <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mt-1">
        Secure Management Control Panel
      </p>
    </div>

    <!-- Login Card -->
    <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl p-6 sm:p-8 relative overflow-hidden">
      <!-- Subtle top orange glow accent -->
      <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-brand-500 to-transparent"></div>

      <!-- Flash Messages -->
      <?php if (!empty($flash_error)): ?>
        <div class="mb-5 p-3.5 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-300 text-xs flex items-start gap-2.5">
          <svg class="w-4 h-4 text-rose-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <div class="leading-relaxed"><?= e($flash_error) ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($flash_success)): ?>
        <div class="mb-5 p-3.5 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-300 text-xs flex items-start gap-2.5">
          <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <div class="leading-relaxed"><?= e($flash_success) ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($flash_info)): ?>
        <div class="mb-5 p-3.5 bg-sky-500/10 border border-sky-500/30 rounded-xl text-sky-300 text-xs flex items-start gap-2.5">
          <svg class="w-4 h-4 text-sky-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div class="leading-relaxed"><?= e($flash_info) ?></div>
        </div>
      <?php endif; ?>

      <form action="/admin/login" method="POST" class="space-y-5" autocomplete="on">
        <?= csrf_field() ?>

        <!-- Email Address -->
        <div>
          <label for="admin_email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5 font-condensed">
            Administrator Email
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
              </svg>
            </div>
            <input id="admin_email"
                   type="email"
                   name="email"
                   required
                   autofocus
                   placeholder="admin@pakbikeparts.pk"
                   value="<?= e($old['email'] ?? '') ?>"
                   class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
          </div>
        </div>

        <!-- Password with Eye Toggle -->
        <div>
          <label for="admin_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5 font-condensed">
            Password
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <input id="admin_password"
                   type="password"
                   name="password"
                   required
                   placeholder="Enter your admin password"
                   class="w-full bg-slate-950/80 border border-slate-700/80 rounded-xl pl-10 pr-12 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
            
            <!-- Eye Toggle Button -->
            <button type="button"
                    id="toggle_password_btn"
                    aria-label="Show password"
                    tabindex="-1"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white transition-colors focus:outline-none">
              <!-- Eye Open (show password) -->
              <svg id="eye_open_icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <!-- Eye Slashed (hide password, hidden by default) -->
              <svg id="eye_slash_icon" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full bg-gradient-to-r from-brand-600 to-amber-600 hover:from-brand-500 hover:to-amber-500 text-white font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-brand-600/30 text-sm font-condensed uppercase tracking-wider transition-all transform active:scale-[0.99] flex items-center justify-center gap-2 mt-2">
          <span>Authenticate &amp; Enter Dashboard</span>
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </button>
      </form>

      <!-- Back to Storefront Link -->
      <div class="mt-6 pt-5 border-t border-slate-800 text-center">
        <a href="/" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-brand-400 transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          <span>Return to PakBikeParts Storefront</span>
        </a>
      </div>

    </div>

    <!-- Security Footer -->
    <div class="mt-6 text-center text-[11px] text-slate-500 flex items-center justify-center gap-2">
      <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
      </svg>
      <span>Encrypted HTTPS Session &bull; 256-Bit Protection</span>
    </div>

  </div>

  <!-- Vanilla JS for Password Eye Toggle -->
  <script>
    (function() {
      const toggleBtn = document.getElementById('toggle_password_btn');
      const passwordInput = document.getElementById('admin_password');
      const eyeOpen = document.getElementById('eye_open_icon');
      const eyeSlash = document.getElementById('eye_slash_icon');

      if (toggleBtn && passwordInput) {
        toggleBtn.addEventListener('click', function() {
          const isPassword = passwordInput.getAttribute('type') === 'password';
          if (isPassword) {
            passwordInput.setAttribute('type', 'text');
            eyeOpen.classList.add('hidden');
            eyeSlash.classList.remove('hidden');
            toggleBtn.setAttribute('aria-label', 'Hide password');
          } else {
            passwordInput.setAttribute('type', 'password');
            eyeOpen.classList.remove('hidden');
            eyeSlash.classList.add('hidden');
            toggleBtn.setAttribute('aria-label', 'Show password');
          }
          passwordInput.focus();
        });
      }
    })();
  </script>

</body>
</html>
