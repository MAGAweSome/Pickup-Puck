/**
 * Pickup Puck PWA Controller
 * Handles Service Worker registration, install prompts (Android & iOS),
 * offline/online sync toasts, and standalone view modes.
 */

(function () {
    'use strict';

    // State
    let deferredInstallPrompt = null;
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                         window.navigator.standalone === true ||
                         document.referrer.includes('android-app://');

    // Add standalone class to html if running as an installed PWA
    if (isStandalone) {
        document.documentElement.classList.add('pwa-standalone');
    }

    // Helper: Safely access toast function
    function notify(message, type = 'info') {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            console.log(`[PWA Notification] (${type}): ${message}`);
        }
    }

    // ==========================================
    // 1. Service Worker Registration & Updates
    // ==========================================
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then((registration) => {
                    console.log('[PWA] Service Worker registered with scope:', registration.scope);

                    // Check for updates
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        if (!newWorker) return;

                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // New content is available, show subtle update banner
                                showUpdateAvailableToast(newWorker);
                            }
                        });
                    });
                })
                .catch((error) => {
                    console.warn('[PWA] Service Worker registration failed:', error);
                });

            // Prevent reload loop on controller change
            let refreshing = false;
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                if (!refreshing) {
                    refreshing = true;
                    window.location.reload();
                }
            });
        });
    }

    function showUpdateAvailableToast(worker) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const updateToast = document.createElement('div');
        updateToast.className = 'pointer-events-auto flex items-center justify-between gap-3 px-4 py-3 rounded-xl shadow-2xl border text-sm font-medium bg-slate-900/95 border-sky-500/50 text-white shadow-sky-950/40 transform translate-y-3 opacity-0 transition-all duration-300';
        updateToast.innerHTML = `
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-arrows-rotate text-sky-400 animate-spin"></i>
                <span>A new update for Pickup Puck is ready!</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="pwa-update-now-btn" class="px-2.5 py-1 rounded-lg bg-sky-500 hover:bg-sky-400 text-slate-950 font-bold text-xs transition">
                    Refresh
                </button>
                <button type="button" class="text-slate-400 hover:text-white p-1" onclick="this.closest('.pointer-events-auto').remove()">
                    <i class="fa-solid fa-times text-xs"></i>
                </button>
            </div>
        `;
        container.appendChild(updateToast);
        requestAnimationFrame(() => updateToast.classList.remove('translate-y-3', 'opacity-0'));

        const btn = updateToast.querySelector('#pwa-update-now-btn');
        if (btn) {
            btn.addEventListener('click', () => {
                worker.postMessage({ type: 'SKIP_WAITING' });
            });
        }
    }

    // ==========================================
    // 2. Online / Offline Connectivity Detection
    // ==========================================
    window.addEventListener('online', () => {
        document.documentElement.classList.remove('pwa-offline');
        notify('Back online! Live locker room synchronization active.', 'success');
        const offlinePill = document.getElementById('pwa-offline-indicator');
        if (offlinePill) offlinePill.classList.add('hidden');
    });

    window.addEventListener('offline', () => {
        document.documentElement.classList.add('pwa-offline');
        notify('Offline mode active. Viewing cached game details and rosters.', 'error');
        const offlinePill = document.getElementById('pwa-offline-indicator');
        if (offlinePill) offlinePill.classList.remove('hidden');
    });

    // ==========================================
    // 3. Install Prompt Logic (Android / Chrome)
    // ==========================================
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent default mini-infobar on mobile Chrome
        e.preventDefault();
        deferredInstallPrompt = e;
        window.deferredPWAInstallPrompt = e;

        // Show install buttons and banner if not standalone and not recently dismissed
        if (!isStandalone) {
            showInstallUI();
        }
    });

    window.addEventListener('appinstalled', () => {
        deferredInstallPrompt = null;
        window.deferredPWAInstallPrompt = null;
        hideInstallUI();
        notify('Pickup Puck installed to your home screen! Welcome to the app.', 'success');
        try {
            localStorage.setItem('pwa_installed', 'true');
        } catch (err) {}
    });

    // Check on page load for iOS Safari or previously available prompt
    document.addEventListener('DOMContentLoaded', () => {
        if (!isStandalone) {
            if (isIOS && isSafari) {
                // On iOS Safari, show install UI (banner & buttons)
                showInstallUI(true);
            }
            setupInstallButtons();
        }
    });

    function showInstallUI(isApple = false) {
        // Top Nav button
        const navBtn = document.getElementById('pwa-nav-install-btn');
        if (navBtn) navBtn.classList.remove('hidden');

        // Floating Bottom Banner (check if dismissed in the last 5 days)
        const bannerDismissed = localStorage.getItem('pwa_banner_dismissed');
        const fiveDays = 5 * 24 * 60 * 60 * 1000;
        const isDismissed = bannerDismissed && (Date.now() - parseInt(bannerDismissed, 10)) < fiveDays;

        const banner = document.getElementById('pwa-install-banner');
        if (banner && !isDismissed) {
            banner.classList.remove('hidden');
            requestAnimationFrame(() => {
                banner.classList.remove('translate-y-full', 'opacity-0');
            });
        }
    }

    function hideInstallUI() {
        const navBtn = document.getElementById('pwa-nav-install-btn');
        if (navBtn) navBtn.classList.add('hidden');

        const banner = document.getElementById('pwa-install-banner');
        if (banner) {
            banner.classList.add('translate-y-full', 'opacity-0');
            setTimeout(() => banner.classList.add('hidden'), 350);
        }
    }

    function setupInstallButtons() {
        // Wire all install triggers
        const triggerButtons = document.querySelectorAll('.pwa-install-trigger');
        triggerButtons.forEach((btn) => {
            btn.addEventListener('click', handleInstallClick);
        });

        // Wire dismiss button
        const dismissBtn = document.getElementById('pwa-dismiss-banner-btn');
        if (dismissBtn) {
            dismissBtn.addEventListener('click', () => {
                try {
                    localStorage.setItem('pwa_banner_dismissed', Date.now().toString());
                } catch (e) {}
                hideInstallUI();
            });
        }

        // Wire iOS modal close button
        const iosModalClose = document.getElementById('pwa-ios-modal-close');
        if (iosModalClose) {
            iosModalClose.addEventListener('click', closeIOSModal);
        }
        const iosModalBackdrop = document.getElementById('pwa-ios-modal-backdrop');
        if (iosModalBackdrop) {
            iosModalBackdrop.addEventListener('click', closeIOSModal);
        }
    }

    async function handleInstallClick(e) {
        if (e) e.preventDefault();

        // 1. Android / Chromium / Desktop Install
        if (deferredInstallPrompt) {
            deferredInstallPrompt.prompt();
            const { outcome } = await deferredInstallPrompt.userChoice;
            console.log('[PWA] User install choice:', outcome);
            deferredInstallPrompt = null;
            window.deferredPWAInstallPrompt = null;
            hideInstallUI();
            return;
        }

        // 2. iOS Safari Install Guide Modal
        if (isIOS) {
            openIOSModal();
            return;
        }

        // 3. Fallback instructions for unsupported browsers
        notify('To install Pickup Puck, tap your browser menu (⋮) and select "Add to Home screen" or "Install App".', 'info');
    }

    function openIOSModal() {
        const modal = document.getElementById('pwa-ios-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            const drawer = modal.querySelector('.pwa-ios-drawer');
            if (drawer) drawer.classList.remove('translate-y-full');
        });
    }

    function closeIOSModal() {
        const modal = document.getElementById('pwa-ios-modal');
        if (!modal) return;
        modal.classList.add('opacity-0');
        const drawer = modal.querySelector('.pwa-ios-drawer');
        if (drawer) drawer.classList.add('translate-y-full');
        document.body.style.overflow = '';
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // Expose helpers globally
    window.installPWA = handleInstallClick;
    window.openPWAInstallGuide = openIOSModal;

})();
