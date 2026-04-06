/**
 * ============================================================
 * DESKTOP ONLY - Exam Device Restriction
 * Detects mobile/tablet devices and blocks exam access.
 * Standalone reusable file - load in <head> for instant blocking.
 * ============================================================
 *
 * Usage:
 *   Before loading this script, set:
 *     var DESKTOP_ONLY_ENABLED = true;
 *     var DESKTOP_ONLY_CONFIG = {
 *         redirectUrl: '/home',
 *         allowedRoutes: ['/home']  // only these pages are accessible
 *     };
 */

(function() {
    'use strict';

    var enabled = (typeof DESKTOP_ONLY_ENABLED !== 'undefined') ? DESKTOP_ONLY_ENABLED : false;
    var modalId = 'desktopOnlyModal';

    if (!enabled) return;

    var userConfig = (typeof DESKTOP_ONLY_CONFIG !== 'undefined') ? DESKTOP_ONLY_CONFIG : {};
    var redirectUrl = userConfig.redirectUrl || '/';
    var allowedRoutes = userConfig.allowedRoutes || [];

    // --- Device Detection (mobile phones only, tablets/iPads are allowed) ---
    function isMobilePhone() {
        if (navigator.userAgentData && navigator.userAgentData.mobile) return true;

        var ua = navigator.userAgent || '';

        // Allow iPads and tablets explicitly
        if (/iPad|Tablet|PlayBook|Silk/i.test(ua)) return false;
        if (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1) return false;

        // Detect mobile phones
        if (/Mobile|Android.*Mobile|iPhone|iPod|webOS|BlackBerry|Opera Mini|Opera Mobi|IEMobile|Windows Phone|BB10/i.test(ua)) return true;

        // Small screen with touch = likely a phone
        var hasTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
        var smallScreen = (Math.min(window.screen.width, window.screen.height) <= 480);
        if (hasTouch && smallScreen) return true;

        return false;
    }

    if (!isMobilePhone()) return;

    // --- We're on a mobile/tablet device ---

    function isAllowedUrl(url) {
        for (var i = 0; i < allowedRoutes.length; i++) {
            if (url === allowedRoutes[i] || url === allowedRoutes[i] + '/') return true;
        }
        return false;
    }

    // --- INSTANT: runs in <head> before page renders ---
    if (!isAllowedUrl(window.location.pathname)) {
        document.documentElement.style.visibility = 'hidden';
        window.location.replace(redirectUrl + '?desktop_only=1');
        return;
    }

    // --- DEFERRED: runs after DOM is ready (allowed pages only) ---

    function createModal() {
        if (document.getElementById(modalId)) return;

        var modalHTML =
            '<div class="modal fade" id="' + modalId + '" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">' +
            '  <div class="modal-dialog modal-dialog-centered">' +
            '    <div class="modal-content">' +
            '      <div class="modal-header" style="background-color:#f0ad4e;color:#fff;text-align:center;">' +
            '        <h4 class="modal-title" style="width:100%;">' +
            '          <i class="fa fa-desktop"></i> ' +
            '          تنبيه / Warning' +
            '        </h4>' +
            '      </div>' +
            '      <div class="modal-body text-center" style="font-size:16px;padding:30px;">' +
            '        <div style="margin-bottom:20px;">' +
            '          <i class="fa fa-laptop" style="font-size:48px;color:#f0ad4e;"></i>' +
            '        </div>' +
            '        <p style="font-size:18px;font-weight:bold;margin-bottom:10px;">' +
            '          يجب استخدام جهاز كمبيوتر أو لابتوب لأداء الاختبار' +
            '        </p>' +
            '        <p class="text-muted" style="margin-bottom:20px;">' +
            '          You must use a computer or laptop to take the exam' +
            '        </p>' +
            '        <hr>' +
            '        <p style="margin-bottom:5px;color:#e74c3c;">' +
            '          <i class="fa fa-times-circle"></i> ' +
            '          لا يُسمح باستخدام الهاتف المحمول' +
            '        </p>' +
            '        <p class="text-muted">' +
            '          Mobile phones are not allowed' +
            '        </p>' +
            '      </div>' +
            '      <div class="modal-footer" style="text-align:center;">' +
            '        <button type="button" class="btn btn-warning" data-bs-dismiss="modal" style="margin:0 auto;">' +
            '          حسناً / OK' +
            '        </button>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>';

        var div = document.createElement('div');
        div.innerHTML = modalHTML;
        document.body.appendChild(div.firstChild);
    }

    function showModal() {
        createModal();
        var el = document.getElementById(modalId);
        if (el) {
            var modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el, { backdrop: 'static', keyboard: false });
            modal.show();
        }
    }

    function init() {
        // Intercept all link clicks — block non-allowed routes
        document.addEventListener('click', function(e) {
            var link = e.target.closest('a');
            if (!link || !link.href) return;

            try {
                var url = new URL(link.href, window.location.origin);
                if (!isAllowedUrl(url.pathname)) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    showModal();
                }
            } catch(err) {}
        }, true);

        // Show dialog if redirected here with desktop_only param
        if (window.location.search.indexOf('desktop_only=1') !== -1) {
            showModal();
        }
    }

    document.addEventListener('DOMContentLoaded', init);
})();
