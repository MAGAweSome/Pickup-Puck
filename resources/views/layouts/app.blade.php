<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pickup Puck</title>

    <!-- Prevent Flash of Incorrect Theme (FOUC) & Atmos-compliant Theme Engine -->
    <script>
        (function () {
            function applyTheme(theme) {
                if (theme === 'light') {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.style.colorScheme = 'light';
                } else {
                    // Dark mode is default
                    document.documentElement.classList.add('dark');
                    document.documentElement.style.colorScheme = 'dark';
                }
            }

            try {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme === 'light') {
                    applyTheme('light');
                } else {
                    // Default to dark mode unless user preference is explicitly light
                    applyTheme('dark');
                }
            } catch (e) {
                applyTheme('dark');
            }

            window.setTheme = function (newTheme) {
                try {
                    const theme = (newTheme === 'light') ? 'light' : 'dark';
                    applyTheme(theme);
                    localStorage.setItem('theme', theme);
                    window.dispatchEvent(new CustomEvent('theme-changed', {
                        detail: { isDark: theme === 'dark', theme: theme }
                    }));
                } catch (err) {
                    console.error('Failed to set theme', err);
                }
            };

            window.toggleTheme = function () {
                const isDark = document.documentElement.classList.contains('dark');
                window.setTheme(isDark ? 'light' : 'dark');
            };
        })();
    </script>

    <!-- Inter font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind Play CDN (Must load before assigning tailwind.config) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    screens: {
                        '2xl': '1536px',
                        '3xl': '1780px',
                    },
                    colors: {
                        'deep-navy': '#0b1120',
                        'slate-gray': '#1e293b',
                        'ice-blue': '#38bdf8',
                        'ice': '#f0f9ff'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Inter', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/2a47ed06c2.js" crossorigin="anonymous"></script>

    <!-- Utilities kept from original layout -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css" />

    <style>
        :root {
            --nav-height: 4rem;
            color-scheme: light;
        }
        html.dark {
            color-scheme: dark;
        }

        /* Custom scrollbars */
        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.4); border-radius: 9999px; }
        .dark ::-webkit-scrollbar-thumb { background: rgba(56, 189, 248, 0.3); }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 0.7); }
        .dark ::-webkit-scrollbar-thumb:hover { background: rgba(56, 189, 248, 0.6); }

        /* Atmos Surface Hierarchy:
           Light Mode: Canvas #f1f5f9, Cards #ffffff, Text #0f172a
           Dark Mode: Canvas #0b1120 (not pitch black), Cards #1e293b (elevation), Text #f8fafc */
        body {
            background-color: #f1f5f9;
            color: #0f172a;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        html.dark body {
            background-color: #0b1120 !important;
            color: #f8fafc !important;
        }

        .stadium-bg {
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(14, 165, 233, 0.05), transparent 30%),
                radial-gradient(circle at 90% 80%, rgba(2, 132, 199, 0.04), transparent 40%);
            background-attachment: fixed;
        }
        html.dark .stadium-bg {
            background-color: #0b1120 !important;
            background-image: 
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(56, 189, 248, 0.09), transparent),
                radial-gradient(circle at 90% 90%, rgba(6, 182, 212, 0.04), transparent 40%) !important;
            background-attachment: fixed;
        }

        /* Instantaneous visibility helpers for toggle icons */
        html.dark .dark\:hidden { display: none !important; }
        html.dark .dark\:inline { display: inline !important; }
        html.dark .dark\:flex { display: flex !important; }
        html:not(.dark) .hidden.dark\:inline { display: none !important; }
        html:not(.dark) .hidden.dark\:flex { display: none !important; }
    </style>
</head>

<body class="stadium-bg min-h-screen text-slate-800 dark:text-slate-100 antialiased font-sans transition-colors duration-150" x-data="{ mobileMenuOpen: false }">

    @include('inc.nav')

    <div class="pt-16 min-h-screen flex flex-col justify-between">
        @if(Auth::check() && !Auth::user()->hasVerifiedEmail() && !request()->routeIs('verification.notice'))
            <div id="email-verification-banner" class="bg-amber-500/15 border-b border-amber-500/30 text-amber-900 dark:text-amber-200 px-4 py-2.5 shadow-sm shrink-0">
                <div class="w-full max-w-7xl 2xl:max-w-[1680px] 3xl:max-w-[1880px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm">
                    <div class="flex items-center gap-2.5 text-center sm:text-left">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400 text-base shrink-0"></i>
                        <span>
                            Your email address (<strong class="font-bold text-slate-900 dark:text-white">{{ Auth::user()->email }}</strong>) is not verified. Please verify your email to unlock all league features.
                        </span>
                    </div>
                    <form method="POST" action="{{ route('verification.resend') }}" class="shrink-0 inline-flex" onsubmit="event.preventDefault(); window.resendVerificationEmail(this);">
                        @csrf
                        <button type="submit" id="banner-resend-btn" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-lg text-xs transition shadow-sm flex items-center gap-1.5 whitespace-nowrap">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>Resend Verification Email</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <main class="flex-1 w-full max-w-7xl 2xl:max-w-[1680px] 3xl:max-w-[1880px] mx-auto px-3.5 sm:px-6 lg:px-8 2xl:px-10 py-6 pb-24 lg:pb-12">
            @yield('content')
        </main>

        <!-- Global Footer -->
        <footer class="w-full border-t border-slate-200 dark:border-slate-800/80 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md py-6 text-center text-xs text-slate-500 dark:text-slate-400 transition-colors">
            <div class="w-full max-w-7xl 2xl:max-w-[1680px] 3xl:max-w-[1880px] mx-auto px-4 sm:px-6 lg:px-8 2xl:px-10 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 font-medium">
                    <span class="font-black text-slate-800 dark:text-white tracking-wider">PICKUP PUCK</span>
                    <span>•</span>
                    <span>Pickup Hockey Scheduling Hub</span>
                </div>
                <div class="flex items-center gap-3 text-[11px]">
                    <span>Balanced Teams</span>
                    <span>•</span>
                    <span>Live Crease Tracking</span>
                    <span>•</span>
                    <span>T-30 Roster Reveals</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Mobile Bottom Navigation Dock -->
    @include('components.mobile_dock')

    <!-- Global Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 pointer-events-none max-w-sm w-full"></div>

    <script>
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-2xl border text-sm font-medium transition-all duration-300 transform translate-y-3 opacity-0 ${
                type === 'success' 
                    ? 'bg-slate-900/95 border-emerald-500/50 text-emerald-300 shadow-emerald-950/40' 
                    : type === 'error'
                    ? 'bg-slate-900/95 border-rose-500/50 text-rose-300 shadow-rose-950/40'
                    : 'bg-slate-900/95 border-sky-500/50 text-sky-300 shadow-sky-950/40'
            }`;

            const icon = type === 'success'
                ? '<i class="fas fa-check-circle text-emerald-400 text-base"></i>'
                : type === 'error'
                ? '<i class="fas fa-exclamation-circle text-rose-400 text-base"></i>'
                : '<i class="fas fa-info-circle text-sky-400 text-base"></i>';

            toast.innerHTML = `
                ${icon}
                <div class="flex-1">${message}</div>
                <button type="button" class="text-slate-400 hover:text-white ml-2 transition-colors" onclick="this.parentElement.remove()">
                    <i class="fas fa-times text-xs"></i>
                </button>
            `;

            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-3', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        };

        window.resendVerificationEmail = async function(form) {
            const btn = form.querySelector('button[type="submit"]');
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i> <span>Sending...</span>';
            }

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    }
                });

                if (res.ok || res.status === 202) {
                    window.showToast('Verification email resent! Please check your inbox or junk folder.', 'success');
                } else if (res.status === 429) {
                    window.showToast('Please wait a moment before requesting another verification email.', 'error');
                } else {
                    form.submit();
                }
            } catch (e) {
                form.submit();
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }
        };
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>
    @stack('scripts')
</body>

</html>