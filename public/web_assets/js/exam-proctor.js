/**
 * ============================================================
 * EXAM PROCTOR - Exam Security & Proctoring System
 * Version: 1.0.0
 * ============================================================
 *
 * This is a STANDALONE, REUSABLE proctoring module that captures
 * webcam selfies and screen screenshots during an exam.
 *
 * HOW TO USE ON OTHER PLATFORMS:
 * 1. Include this file in your exam page
 * 2. Set `window.PROCTOR_ENABLED = true` before this script loads
 * 3. Adjust EXAM_PROCTOR_CONFIG below to match your platform's
 *    form selectors, language, capture counts, etc.
 * 4. The proctor will automatically initialize on DOM ready
 *
 * DEPENDENCIES: jQuery (for Bootstrap modal), Bootstrap CSS/JS (for modal)
 * BROWSER: Chrome 62+, Firefox 66+, Edge 79+ (getDisplayMedia support)
 *
 * ============================================================
 * CONFIGURABLE SETTINGS - Change these for your platform
 * ============================================================
 */
var EXAM_PROCTOR_CONFIG = {

    // --- Core Toggle ---
    // This is overridden by the server/platform via `window.PROCTOR_ENABLED`
    enabled: false,

    // --- Capture Counts (smart distribution across exam time) ---
    // Captures are auto-distributed: first at start, last near end, rest evenly in between
    // Set to 0 to disable that capture type entirely
    maxSelfies: 5,                     // Number of webcam selfies per exam step
    maxScreenshots: 5,                 // Number of screen screenshots per exam step

    // --- Permission Denied Behavior ---
    // 'continue' = warn student but allow exam to proceed (limited monitoring)
    // 'reject'   = block exam entirely, show Go Back + Retry buttons
    onDenied: 'reject',

    // --- Image Quality & Resolution ---
    imageQuality: 0.7,                 // JPEG quality (0.0 - 1.0)
    selfieMaxWidth: 640,               // Selfie capture resolution
    selfieMaxHeight: 480,
    screenshotMaxWidth: 1280,          // Screenshot capture resolution
    screenshotMaxHeight: 720,

    // --- Platform Selectors (change per platform) ---
    formSelector: '#exams',                              // CSS selector for the exam form
    confirmButtonSelector: '.btn-exam-view',             // The final submit/confirm button
    backUrl: null,                                     // URL for "Go Back" in reject mode (null = history.back)

    // --- Badge ---
    showBadgeCounts: false,            // Show capture counts (3/6) in the floating status badge

    // --- Language ---
    // 'en' = English only, 'ar' = Arabic only, 'both' = bilingual
    lang: 'both',

    // --- Timer Control ---
    // CSS selector for the countdown timer element on the exam page
    clockSelector: '#clock',
    // Pause the countdown timer while the permissions dialog is visible.
    // When the student completes the permissions, the timer resumes automatically.
    // Set to false to let the timer keep running during the permissions dialog.
    pauseTimerDuringPermissions: true,

    // --- Callbacks (optional, for platform-specific hooks) ---
    // function(type, error) - called on any error
    onError: null,
    // function(type, blob, minute) - called after each successful capture
    onCapture: null,
    // function(type) - called when a permission is granted ('camera' or 'screen')
    onPermissionGranted: null,
    // function(type) - called when a permission is denied ('camera' or 'screen')
    onPermissionDenied: null
};


/**
 * ============================================================
 * EXAM PROCTOR CLASS - Do NOT modify below unless customizing
 * ============================================================
 */
(function (window, $) {
    'use strict';

    // --------------------------------------------------------
    // Merge server-side override into config
    // --------------------------------------------------------
    if (typeof window.PROCTOR_ENABLED !== 'undefined') {
        EXAM_PROCTOR_CONFIG.enabled = !!window.PROCTOR_ENABLED;
    }

    /**
     * ExamProctor Constructor
     */
    function isTabletDevice() {
        var ua = navigator.userAgent || '';
        if (/iPad/i.test(ua)) return true;
        if (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1) return true;
        if (/Android/i.test(ua) && !/Mobile/i.test(ua)) return true;
        if (/Tablet|PlayBook|Silk|Kindle/i.test(ua)) return true;
        return false;
    }

    function ExamProctor(config) {
        // --- State ---
        this.config = config;
        this.webcamStream = null;       // MediaStream from getUserMedia
        this.screenStream = null;       // MediaStream from getDisplayMedia
        this.webcamVideo = null;        // Hidden <video> element for webcam
        this.screenVideo = null;        // Hidden <video> element for screen
        this.selfies = [];              // Array of { blob: Blob, minute: Number }
        this.screenshots = [];          // Array of { blob: Blob, minute: Number }
        this.selfieTimeouts = [];       // setTimeout IDs for scheduled selfie captures
        this.screenshotTimeouts = [];   // setTimeout IDs for scheduled screenshot captures
        this.startTime = null;          // Timestamp when capturing started
        this.examDurationMinutes = 40;  // Read from page timer element
        this.permissionsGranted = { camera: false, screen: false };
        this.badgeElement = null;       // Status badge DOM element
        this.isDestroyed = false;       // Cleanup flag
    }

    // --------------------------------------------------------
    // INITIALIZATION
    // --------------------------------------------------------

    /**
     * Initialize the proctoring system.
     * Called automatically on DOM ready if enabled.
     */
    ExamProctor.prototype.init = function () {
        if (!this.config.enabled) {
            return;
        }

        // Check for secure context (HTTPS required for media APIs)
        // Allow localhost, 127.0.0.1, and local network IPs for development
        var host = window.location.hostname;
        var isLocal = (host === 'localhost' || host === '127.0.0.1' || host.endsWith('.local') || host.endsWith('.test') || /^192\.168\./.test(host) || /^10\./.test(host));
        if (window.location.protocol !== 'https:' && !isLocal) {
            return;
        }

        // Check for required browser APIs
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            return;
        }

        // Show the permission request dialog immediately to block exam access.
        // Timer pause is set up asynchronously (see _pauseExamTimer).
        this.showPermissionDialog();

        // Hook into form submission to inject captured images
        this._hookFormSubmission();

        // Pause the exam countdown timer until permissions are resolved.
        // Must be called AFTER showPermissionDialog so the modal backdrop is in place.
        this._pauseExamTimer();

    };

    /**
     * Read exam duration from the page's countdown timer element.
     * Expected format: "HH:MM:SS" (e.g., "00:40:00" or "01:00:00")
     */
    ExamProctor.prototype._readExamDuration = function () {
        var clockEl = document.querySelector(this.config.clockSelector);
        if (clockEl) {
            var text = clockEl.textContent.trim();
            var parts = text.split(':');
            if (parts.length === 3) {
                var hours = parseInt(parts[0], 10) || 0;
                var minutes = parseInt(parts[1], 10) || 0;
                this.examDurationMinutes = hours * 60 + minutes;
            }
        }
        // Fallback: default 40 minutes if we can't read the timer
        if (this.examDurationMinutes <= 0) {
            this.examDurationMinutes = 40;
        }
    };

    // --------------------------------------------------------
    // EXAM TIMER CONTROL - Pause/Resume during permission dialog
    // --------------------------------------------------------

    /**
     * Pause the exam countdown timer.
     *
     * HOW IT WORKS (The Final Countdown v2.2.0):
     *   - pause() = stop() → clears the setInterval only.
     *   - resume() = start() → recalculates from (finalDate - now), so calling
     *     resume() after a delay LOSES that delay from the student's time.
     *
     * CORRECT APPROACH:
     *   1. Listen for the first 'update.countdown' event to capture the plugin's
     *      real finalDate (accurate even when localStorage spend_time is set).
     *   2. Record the exact timestamp we paused (_timerPausedAt).
     *   3. Stop the interval via countdown('pause').
     *
     * On resume, _resumeExamTimer() extends the finalDate by the pause duration
     * and restarts via countdown(newTarget) so no time is lost.
     *
     * Controlled by config.pauseTimerDuringPermissions and config.clockSelector.
     */
    ExamProctor.prototype._pauseExamTimer = function () {
        if (!this.config.pauseTimerDuringPermissions) return;
        var self = this;
        var $clock = $(this.config.clockSelector);
        if (!$clock.length) return;

        // Hook into the first update.countdown tick to capture the real finalDate.
        // The first tick fires ~100 ms after countdown init (precision default).
        // We use a namespaced event so we can safely remove it if needed.
        $clock.one('update.countdown.proctorPause', function (event) {
            self._timerFinalDate = event.finalDate;   // accurate finalDate from plugin
            self._timerPausedAt  = Date.now();         // timestamp of pause
            $clock.countdown('pause');                 // stop the interval
        });
    };

    /**
     * Resume the exam countdown timer after permissions are resolved.
     *
     * Extends the original finalDate by the full pause duration so the student
     * does not lose any exam time while granting permissions.
     * Then calls countdown(newTarget) which sets the new finalDate and restarts
     * the interval — all existing event handlers (.on) remain intact.
     *
     * Controlled by config.pauseTimerDuringPermissions and config.clockSelector.
     */
    ExamProctor.prototype._resumeExamTimer = function () {
        if (!this.config.pauseTimerDuringPermissions) return;
        var $clock = $(this.config.clockSelector);
        if (!$clock.length) return;

        // Remove the one-time pause listener in case it hasn't fired yet
        $clock.off('update.countdown.proctorPause');

        if (this._timerFinalDate && this._timerPausedAt) {
            // Extend finalDate by the full time spent in the permissions dialog
            var pausedMs  = Date.now() - this._timerPausedAt;
            var newTarget = new Date(this._timerFinalDate.getTime() + pausedMs);
            this._timerFinalDate = null;
            this._timerPausedAt  = null;
            // Passing a Date to countdown() calls setFinalDate() + start()
            // and keeps all existing event handlers on the element intact.
            $clock.countdown(newTarget);
        } else {
            // Fallback: timer was never paused (e.g. first tick didn't fire yet)
            $clock.countdown('resume');
        }
    };

    // --------------------------------------------------------
    // PERMISSION DIALOG
    // --------------------------------------------------------

    /**
     * Create and show the permission request modal dialog.
     * This is created dynamically so the JS file remains standalone.
     */
    ExamProctor.prototype.showPermissionDialog = function () {
        var self = this;
        var cfg = this.config;

        // --- Build bilingual text ---
        var grantText = this._t('Grant Permissions', 'منح الصلاحيات');
        var continueText = this._t('Continue Exam', 'متابعة الاختبار');
        var goBackText = this._t('Go Back', 'العودة');
        var retryText = this._t('Confirm Permissions', 'تأكيد الصلاحيات');

        // --- Inject styles for the dialog ---
        if (!document.getElementById('proctor-dialog-styles')) {
            var style = document.createElement('style');
            style.id = 'proctor-dialog-styles';
            style.textContent = '' +
                '#modal-proctor-permissions .modal-content {' +
                '  border: none;' +
                '  border-radius: 20px;' +
                '  overflow: hidden;' +
                '  box-shadow: 0 25px 60px rgba(0,0,0,0.15);' +
                '}' +
                '.proctor-dialog-header {' +
                '  background: var(--science-color, #068241);' +
                '  padding: 35px 30px 30px;' +
                '  text-align: center;' +
                '  color: #fff;' +
                '  position: relative;' +
                '}' +
                '.proctor-dialog-header::after {' +
                '  content: "";' +
                '  position: absolute;' +
                '  bottom: -1px;' +
                '  left: 0;' +
                '  right: 0;' +
                '  height: 30px;' +
                '  background: #fff;' +
                '  border-radius: 20px 20px 0 0;' +
                '}' +
                '.proctor-shield-icon {' +
                '  width: 70px;' +
                '  height: 70px;' +
                '  background: rgba(255,255,255,0.2);' +
                '  border-radius: 50%;' +
                '  display: inline-flex;' +
                '  align-items: center;' +
                '  justify-content: center;' +
                '  font-size: 40px;' +
                '  margin-bottom: 15px;' +
                '  backdrop-filter: blur(10px);' +
                '  border: 2px solid rgba(255,255,255,0.3);' +
                '}' +
                '.proctor-dialog-header h3 {' +
                '  margin: 0;' +
                '  font-size: 22px;' +
                '  font-weight: 700;' +
                '  letter-spacing: 0.3px;' +
                '}' +
                '.proctor-dialog-header .proctor-desc-line {' +
                '  display: block;' +
                '  opacity: 0.95;' +
                '  font-size: 15px;' +
                '  font-weight: 600;' +
                '  line-height: 1.8;' +
                '  max-width: 440px;' +
                '  margin: 0 auto;' +
                '}' +
                '.proctor-dialog-header .proctor-desc-line:first-child {' +
                '  margin-top: 10px;' +
                '}' +
                '.proctor-dialog-header .proctor-desc-line + .proctor-desc-line {' +
                '  margin-top: 4px;' +
                '}' +
                '.proctor-dialog-body {' +
                '  padding: 10px 35px 30px;' +
                '}' +
                '.proctor-perm-cards {' +
                '  display: flex;' +
                '  gap: 16px;' +
                '  margin-bottom: 25px;' +
                '  justify-content: center;' +
                '}' +
                '.proctor-perm-card {' +
                '  flex: 1;' +
                '  max-width: 220px;' +
                '  border: 2px solid #bcbfc1;' +
                '  border-radius: 14px;' +
                '  padding: 22px 16px;' +
                '  text-align: center;' +
                '  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);' +
                '  background: #fafbfc;' +
                '}' +
                '.proctor-perm-card.granted {' +
                '  border-color: #28a745;' +
                '  background: linear-gradient(135deg, #f0fff4 0%, #e6ffed 100%);' +
                '}' +
                '.proctor-perm-card.denied {' +
                '  border-color: #dc3545;' +
                '  background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);' +
                '}' +
                '.proctor-perm-icon {' +
                '  width: 52px;' +
                '  height: 52px;' +
                '  border-radius: 14px;' +
                '  display: inline-flex;' +
                '  align-items: center;' +
                '  justify-content: center;' +
                '  font-size: 24px;' +
                '  margin-bottom: 12px;' +
                '  transition: all 0.4s;' +
                '}' +
                '.proctor-perm-card-camera .proctor-perm-icon { background: rgba(var(--science-color-rgb, 6, 130, 65), 0.12); color: var(--science-color, #068241); }' +
                '.proctor-perm-card-camera.granted .proctor-perm-icon { background: #d4edda; color: #28a745; }' +
                '.proctor-perm-card-camera.denied .proctor-perm-icon { background: #f8d7da; color: #dc3545; }' +
                '.proctor-perm-card-screen .proctor-perm-icon { background: rgba(var(--science-color-rgb, 6, 130, 65), 0.12); color: var(--science-color, #068241); }' +
                '.proctor-perm-card-screen.granted .proctor-perm-icon { background: #d4edda; color: #28a745; }' +
                '.proctor-perm-card-screen.denied .proctor-perm-icon { background: #f8d7da; color: #dc3545; }' +
                '.proctor-perm-title {' +
                '  font-weight: 600;' +
                '  font-size: 18px;' +
                '  color: #333;' +
                '  margin-bottom: 4px;' +
                '}' +
                '.proctor-perm-status {' +
                '  font-size: 12px;' +
                '  color: #999;' +
                '  transition: all 0.3s;' +
                '  display: flex;' +
                '  align-items: center;' +
                '  justify-content: center;' +
                '  gap: 5px;' +
                '  color: red;' +
                '}' +
                '.proctor-perm-card.granted .proctor-perm-status { color: #28a745; font-weight: 600; }' +
                '.proctor-perm-card.denied .proctor-perm-status { color: #dc3545; font-weight: 600; }' +
                '@keyframes proctor-spin { to { transform: rotate(360deg); } }' +
                '.proctor-spinner {' +
                '  display: inline-block;' +
                '  width: 16px;' +
                '  height: 16px;' +
                '  border: 2px solid rgba(255,255,255,0.4);' +
                '  border-top-color: #fff;' +
                '  border-radius: 50%;' +
                '  animation: proctor-spin 0.8s linear infinite;' +
                '  vertical-align: middle;' +
                '}' +
                '@keyframes proctor-check-pop { 0% { transform: scale(0); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }' +
                '.proctor-check-icon {' +
                '  animation: proctor-check-pop 0.4s ease-out;' +
                '  display: inline-block;' +
                '}' +
                '#proctor-grant-btn {' +
                '  background: var(--science-color, #068241);' +
                '  border: none;' +
                '  border-radius: 12px;' +
                '  padding: 14px 45px;' +
                '  font-size: 16px;' +
                '  font-weight: 600;' +
                '  letter-spacing: 0.3px;' +
                '  transition: all 0.3s;' +
                '  box-shadow: 0 4px 15px rgba(var(--science-color-rgb, 6, 130, 65), 0.35);' +
                '  color: #fff;' +
                '  display: inline-flex;' +
                '  align-items: center;' +
                '  justify-content: center;' +
                '  gap: 8px;' +
                '  line-height: 1;' +
                '}' +
                '#proctor-grant-btn:hover:not(:disabled) {' +
                '  transform: translateY(-2px);' +
                '  box-shadow: 0 6px 25px rgba(var(--science-color-rgb, 6, 130, 65), 0.4);' +
                '  background: rgba(var(--science-color-rgb, 6, 130, 65), 0.9);' +
                '  color: #fff;' +
                '}' +
                '#proctor-grant-btn:disabled {' +
                '  opacity: 0.7;' +
                '  cursor: not-allowed;' +
                '  color: #fff;' +
                '}' +
                '.proctor-denied-box {' +
                '  background: #fffbeb;' +
                '  border: 1px solid #fde68a;' +
                '  border-radius: 12px;' +
                '  padding: 20px;' +
                '  margin-top: 20px;' +
                '  display: none;' +
                '  text-align: center;' +
                '}' +
                '.proctor-denied-box.is-reject {' +
                '  background: #fef2f2;' +
                '  border-color: #fca5a5;' +
                '}' +
                '.proctor-denied-box p {' +
                '  margin: 0 0 12px;' +
                '  font-size: 14px;' +
                '  color: #92400e;' +
                '  line-height: 1.6;' +
                '}' +
                '.proctor-denied-box.is-reject p { color: #991b1b; }' +
                '.proctor-denied-actions { display: flex; gap: 10px; justify-content: center; }' +
                '.proctor-denied-actions .btn {' +
                '  border-radius: 10px;' +
                '  padding: 10px 24px;' +
                '  font-weight: 600;' +
                '  font-size: 14px;' +
                '  display: inline-flex;' +
                '  align-items: center;' +
                '  justify-content: center;' +
                '  gap: 6px;' +
                '  line-height: 1;' +
                '}';
            document.head.appendChild(style);
        }

        // Detect if screen sharing is available (not on mobile/tablet)
        var screenAvailable = !!(navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) && !isTabletDevice();

        // --- Create modal HTML ---
        var modalHtml = '' +
            '<div class="modal fade" id="modal-proctor-permissions" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">' +
            '  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width:520px;">' +
            '    <div class="modal-content">' +
            '      <div class="proctor-dialog-header">' +
            '        <div class="proctor-shield-icon"><i class="fas fa-eye" style="color:white;"></i></div>' +
            '        <h3>' + this._t('The assessment permissions access', 'صلاحيات دخول الاختبار') + '</h3>' +
            '        <span class="proctor-desc-line" dir="rtl">' + (screenAvailable
                ? 'الاختبار مراقب بالكامل من مؤسسة اي بي تي المعيارية الدولية - من فضلك أَعْطِ صلاحيات الكاميرا ومشاركة الشاشة لتتمكن من البدء في الاختبار.'
                : 'الاختبار مراقب بالكامل من مؤسسة اي بي تي المعيارية الدولية - من فضلك أَعْطِ صلاحيات الكاميرا لتتمكن من البدء في الاختبار.') + '</span>' +
            '        <span class="proctor-desc-line" dir="ltr">' + (screenAvailable
                ? 'This assessment is fully proctored by ABT Assessment Establishment. Kindly allow access to your camera and screen sharing to proceed.'
                : 'This assessment is fully proctored by ABT Assessment Establishment. Kindly allow access to your camera to proceed.') + '</span>' +

            '      </div>' +
            '      <div class="proctor-dialog-body">' +
            '        <div class="proctor-perm-cards">' +
            '          <div class="proctor-perm-card proctor-perm-card-camera" id="proctor-card-camera">' +
            '            <div class="proctor-perm-icon">&#128247;</div>' +
            '            <div class="proctor-perm-title">' + this._t('Access Camera', 'صلاحيات الكاميرا') + '</div>' +
            '            <div class="proctor-perm-status" id="proctor-camera-status">' +
            '              <span class="proctor-status-dot" style="width:8px;height:8px;border-radius:50%;background:#FF0000;display:inline-block;"></span> ' +
                           this._t('Not active', 'غير مفعل') +
            '            </div>' +
            '          </div>' +
            (screenAvailable ? (
            '          <div class="proctor-perm-card proctor-perm-card-screen" id="proctor-card-screen">' +
            '            <div class="proctor-perm-icon">🖥️</div>' +
            '            <div class="proctor-perm-title">' + this._t('Sharing screen', 'مشاركة الشاشة') + '</div>' +
            '            <div style="font-size:11px;color:#888;margin-top:-2px;margin-bottom:4px;">' + this._t('Select (The Entire Screen)', 'اختر ( الشاشة كاملة )') + '</div>' +
            '            <div class="proctor-perm-status" id="proctor-screen-status">' +
            '              <span class="proctor-status-dot" style="width:8px;height:8px;border-radius:50%;background:#FF0000;display:inline-block;"></span> ' +
                           this._t('Not active', 'غير مفعل') +
            '            </div>' +
            '          </div>'
            ) : '') +
            '        </div>' +
            '        <div class="text-center">' +
            '          <button id="proctor-grant-btn" class="btn btn-primary btn-lg">' + grantText + '</button>' +
            '        </div>' +
            '        <div class="text-center" style="margin-top:14px;">' +
            '          <a href="https://abt-assessments.com/upload-file/y2sfXe9cEcnCTWQFdAlGOjLybYkA9v" target="_blank" rel="noopener noreferrer" style="font-size:12px;color:#6c757d;text-decoration:underline;display:inline-block;line-height:1.6;">' +
            '            <i class="fas fa-shield-alt" style="margin-left:4px;margin-right:4px;"></i>' +
            '            <span dir="rtl" style="display:block;">اضغط هنا للاطلاع على سياسة الخصوصية والأمان وإجراءات الاختبار.</span>' +
            '            <span dir="ltr" style="display:block;">Click here to review our Privacy, Security, and Testing Procedures.</span>' +
            '          </a>' +
            '        </div>' +
            '        <div class="proctor-denied-box" id="proctor-denied-msg">' +
            '          <p id="proctor-denied-detail"></p>' +
            '          <div class="proctor-denied-actions">' +
            '            <button id="proctor-retry-btn" class="btn btn-success">' + retryText + '</button>' +
            '            <button id="proctor-continue-btn" class="btn btn-warning" style="display:none;">' + continueText + '</button>' +
            '            <button id="proctor-back-btn" class="btn btn-danger" style="display:none;">' + goBackText + '</button>' +
            '          </div>' +
            '        </div>' +
            '      </div>' +
            '    </div>' +
            '  </div>' +
            '</div>';

        // Append to body
        $('body').append(modalHtml);

        // Show modal
        $('#modal-proctor-permissions').modal('show');

        // --- Prevent closing the modal until permissions are granted ---
        // Block all attempts to dismiss (programmatic .modal('hide'), ESC, click outside)
        this.permissionsResolved = false;
        var self2 = self; // keep reference
        $('#modal-proctor-permissions').on('hide.bs.modal', function (e) {
            if (!self2.permissionsResolved) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });

        // Hide/disable the exam form while dialog is open (prevent interaction behind modal)
        $(this.config.formSelector).css({ 'opacity': '0.3', 'pointer-events': 'none' });

        // Update badges for permissions that are already granted (e.g., when re-showing after stream loss)
        if (this.permissionsGranted.camera) {
            this._updatePermissionBadge('camera', true);
        }
        if (this.permissionsGranted.screen) {
            this._updatePermissionBadge('screen', true);
        }

        // --- Grant button click handler ---
        $('#proctor-grant-btn').on('click', function () {
            $(this).prop('disabled', true).html('<span class="proctor-spinner"></span> ' + self._t('Requesting...', 'جاري الطلب...'));
            self._requestAllPermissions();
        });

        // --- Retry button click handler ---
        $('#proctor-retry-btn').on('click', function () {
            // Reset UI
            $('#proctor-denied-msg').hide();
            $('#proctor-grant-btn').prop('disabled', false).html(grantText).show();
            // Reset cards
            $('#proctor-card-camera, #proctor-card-screen').removeClass('granted denied');
            $('#proctor-camera-status').html(
                '<span class="proctor-status-dot" style="width:8px;height:8px;border-radius:50%;background:#ccc;display:inline-block;"></span> ' +
                self._t('Not active', 'غير مفعل')
            );
            $('#proctor-screen-status').html(
                '<span class="proctor-status-dot" style="width:8px;height:8px;border-radius:50%;background:#ccc;display:inline-block;"></span> ' +
                self._t('Not active', 'غير مفعل')
            );
            self._requestAllPermissions();
        });

        // --- Continue button (onDenied = 'continue') ---
        $('#proctor-continue-btn').on('click', function () {
            $(self.config.formSelector).css({ 'opacity': '', 'pointer-events': '' });
            self.permissionsResolved = true;
            self._resumeExamTimer();
            $('#modal-proctor-permissions').modal('hide');
            if (self.startTime) {
                self._resumeCapturing();
            } else {
                self._startCapturing();
            }
        });

        // --- Go Back button (onDenied = 'reject') ---
        $('#proctor-back-btn').on('click', function () {
            if (cfg.backUrl) {
                window.location.href = cfg.backUrl;
            } else {
                window.history.back();
            }
        });
    };

    /**
     * Request camera and screen permissions sequentially.
     * Screen share MUST be triggered from a user gesture (the button click).
     */
    ExamProctor.prototype._requestAllPermissions = function () {
        var self = this;
        var cfg = this.config;

        // Step 1: Request camera
        this._requestCameraPermission().then(function () {
            self.permissionsGranted.camera = true;
            self._updatePermissionBadge('camera', true);

            if (cfg.onPermissionGranted) cfg.onPermissionGranted('camera');

            // Step 2: Request screen (must be in same user gesture chain)
            return self._requestScreenPermission();
        }).then(function () {
            // Only mark screen as granted if it was actually granted
            // (on mobile, _requestScreenPermission resolves but sets screen = false)
            if (self.permissionsGranted.screen) {
                self._updatePermissionBadge('screen', true);
                if (cfg.onPermissionGranted) cfg.onPermissionGranted('screen');
            }

            // Check if required permissions are granted
            // Camera is always required; screen is optional (unavailable on mobile/tablet)
            var screenAvailable = !!(navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) && !isTabletDevice();
            var allRequired = self.permissionsGranted.camera && (!screenAvailable || self.permissionsGranted.screen);

            if (!allRequired) {
                self._showDeniedMessage();
                return;
            }

            // All required permissions granted - restore form, resume timer, dismiss dialog
            $(self.config.formSelector).css({ 'opacity': '', 'pointer-events': '' });
            self.permissionsResolved = true;
            self._resumeExamTimer();
            setTimeout(function () {
                $('#modal-proctor-permissions').modal('hide');
                // If this is a re-grant (stream was lost mid-exam), resume remaining captures
                // Otherwise start fresh captures
                if (self.startTime) {
                    self._resumeCapturing();
                } else {
                    self._startCapturing();
                }
            }, 500);

        }).catch(function (error) {

            // Check if user selected wrong screen surface (tab/window instead of full screen)
            if (error.message && error.message.indexOf('WRONG_SURFACE:') === 0) {
                var userMsg = error.message.replace('WRONG_SURFACE:', '');
                self._updatePermissionBadge('screen', false);
                self._showWrongSurfaceMessage(userMsg);
                return;
            }

            // Update badges for whichever failed
            if (!self.permissionsGranted.camera) {
                self._updatePermissionBadge('camera', false);
                if (cfg.onPermissionDenied) cfg.onPermissionDenied('camera');
            }
            if (!self.permissionsGranted.screen) {
                self._updatePermissionBadge('screen', false);
                if (cfg.onPermissionDenied) cfg.onPermissionDenied('screen');
            }

            if (cfg.onError) cfg.onError('permission', error);

            // Show denied message
            self._showDeniedMessage();
        });
    };

    /**
     * Request webcam (camera) access.
     * @returns {Promise<MediaStream>}
     */
    ExamProctor.prototype._requestCameraPermission = function () {
        var self = this;
        if (this.config.maxSelfies <= 0) {
            // Selfies disabled, skip camera request
            this.permissionsGranted.camera = true;
            this._updatePermissionBadge('camera', true);
            return Promise.resolve();
        }
        // Skip if camera stream is still active
        if (this.webcamStream && this.webcamStream.active) {
            this.permissionsGranted.camera = true;
            this._updatePermissionBadge('camera', true);
            return Promise.resolve();
        }
        // Clean up old video element if exists
        if (this.webcamVideo && this.webcamVideo.parentNode) {
            this.webcamVideo.parentNode.removeChild(this.webcamVideo);
        }
        return navigator.mediaDevices.getUserMedia({
            video: { width: { ideal: this.config.selfieMaxWidth }, height: { ideal: this.config.selfieMaxHeight } },
            audio: false
        }).then(function (stream) {
            self.webcamStream = stream;
            // Create hidden video element to draw frames from
            self.webcamVideo = document.createElement('video');
            self.webcamVideo.srcObject = stream;
            self.webcamVideo.setAttribute('playsinline', 'true');
            self.webcamVideo.muted = true;
            self.webcamVideo.style.display = 'none';
            document.body.appendChild(self.webcamVideo);
            return self.webcamVideo.play();
        });
    };

    /**
     * Request screen sharing access.
     * IMPORTANT: getDisplayMedia requires a user gesture (button click).
     * @returns {Promise<MediaStream>}
     */
    ExamProctor.prototype._requestScreenPermission = function () {
        var self = this;
        if (this.config.maxScreenshots <= 0) {
            // Screenshots disabled, skip screen request
            this.permissionsGranted.screen = true;
            this._updatePermissionBadge('screen', true);
            return Promise.resolve();
        }

        // Check if getDisplayMedia is supported (not available on mobile/tablet)
        if (!navigator.mediaDevices.getDisplayMedia || isTabletDevice()) {
            this.permissionsGranted.screen = false;
            this._updatePermissionBadge('screen', false);
            // Don't reject - just resolve with no screen stream
            return Promise.resolve();
        }

        // Skip if screen stream is still active
        if (this.screenStream && this.screenStream.active) {
            this.permissionsGranted.screen = true;
            this._updatePermissionBadge('screen', true);
            return Promise.resolve();
        }
        // Clean up old video element if exists
        if (this.screenVideo && this.screenVideo.parentNode) {
            this.screenVideo.parentNode.removeChild(this.screenVideo);
        }

        return navigator.mediaDevices.getDisplayMedia({
            video: {
                width: { ideal: this.config.screenshotMaxWidth },
                height: { ideal: this.config.screenshotMaxHeight },
                displaySurface: 'monitor'  // Hint browser to prefer full screen
            },
            audio: false
        }).then(function (stream) {
            // Verify user selected "Entire Screen" (not a tab or window)
            var track = stream.getVideoTracks()[0];
            if (track) {
                var settings = track.getSettings();
                // displaySurface: 'monitor' = entire screen, 'window' = app window, 'browser' = tab
                if (settings.displaySurface && settings.displaySurface !== 'monitor') {
                    // Wrong selection - stop the stream and reject
                    stream.getTracks().forEach(function (t) { t.stop(); });
                    return Promise.reject(new Error(
                        'WRONG_SURFACE:' + self._t(
                            'You must select "Entire Screen", not a window or tab. Please try again',
                            'يجب اختيار "الشاشة بالكامل" وليس نافذة أو تبويب. يرجى المحاولة مرة أخرى'
                        )
                    ));
                }
            }

            self.screenStream = stream;
            self.permissionsGranted.screen = true;
            // Create hidden video element for screen capture
            self.screenVideo = document.createElement('video');
            self.screenVideo.srcObject = stream;
            self.screenVideo.setAttribute('playsinline', 'true');
            self.screenVideo.muted = true;
            self.screenVideo.style.display = 'none';
            document.body.appendChild(self.screenVideo);

            // Stream ended detection is handled by _watchStreamHealth()

            return self.screenVideo.play();
        });
    };

    /**
     * Watch active media streams for unexpected stops (user disables camera,
     * clicks "Stop sharing", etc.). When a stream ends, pause the exam timer
     * and re-show the permission dialog so the student must re-grant access.
     */
    ExamProctor.prototype._watchStreamHealth = function () {
        var self = this;

        // Watch camera stream
        if (this.webcamStream) {
            var camTrack = this.webcamStream.getVideoTracks()[0];
            if (camTrack) {
                camTrack.addEventListener('ended', function () {
                    if (self.isDestroyed) return;
                    self.permissionsGranted.camera = false;
                    self.webcamStream = null;
                    // Cancel remaining selfie timeouts
                    self.selfieTimeouts.forEach(function (t) { clearTimeout(t); });
                    self.selfieTimeouts = [];
                    self._onStreamLost('camera');
                });
            }
        }

        // Watch screen stream
        if (this.screenStream) {
            var screenTrack = this.screenStream.getVideoTracks()[0];
            if (screenTrack) {
                screenTrack.addEventListener('ended', function () {
                    if (self.isDestroyed) return;
                    self.permissionsGranted.screen = false;
                    self.screenStream = null;
                    // Cancel remaining screenshot timeouts
                    self.screenshotTimeouts.forEach(function (t) { clearTimeout(t); });
                    self.screenshotTimeouts = [];
                    self._onStreamLost('screen');
                });
            }
        }
    };

    /**
     * Called when a media stream is lost mid-exam.
     * Pauses the timer, disables the form, and re-shows the permission dialog.
     * @param {string} type - 'camera' or 'screen'
     */
    ExamProctor.prototype._onStreamLost = function (type) {

        // Pause the exam timer
        this._pauseExamTimer();

        // Disable the form
        $(this.config.formSelector).css({ 'opacity': '0.3', 'pointer-events': 'none' });

        // Lock the modal again
        this.permissionsResolved = false;

        // Remove old dialog if exists, then re-create it
        $('#modal-proctor-permissions').remove();
        this.showPermissionDialog();

        this._updateStatusBadge();
    };

    /**
     * Update the permission badge in the dialog.
     * @param {string} type - 'camera' or 'screen'
     * @param {boolean} granted
     */
    ExamProctor.prototype._updatePermissionBadge = function (type, granted) {
        var cardSelector = type === 'camera' ? '#proctor-card-camera' : '#proctor-card-screen';
        var statusSelector = type === 'camera' ? '#proctor-camera-status' : '#proctor-screen-status';

        var statusHtml;
        if (granted) {
            statusHtml = '<span class="proctor-check-icon" style="color:#28a745;">&#10003;</span> ' +
                this._t('Ready', 'جاهز');
            $(cardSelector).removeClass('denied').addClass('granted');
        } else {
            statusHtml = '<span style="color:#dc3545;">&#10007;</span> ' +
                this._t('Denied', 'مرفوض');
            $(cardSelector).removeClass('granted').addClass('denied');
        }
        $(statusSelector).html(statusHtml);
    };

    /**
     * Show the permission denied message with appropriate buttons.
     */
    /**
     * Show a message when user selected a tab/window instead of entire screen.
     * Only shows the retry button so the user must try again.
     * @param {string} message - Bilingual error message
     */
    ExamProctor.prototype._showWrongSurfaceMessage = function (message) {
        $('#proctor-grant-btn').hide();
        var $box = $('#proctor-denied-msg');
        $box.show().addClass('is-reject');
        $('#proctor-denied-detail').html(message);
        $('#proctor-retry-btn').show();
        $('#proctor-continue-btn').hide();
        $('#proctor-back-btn').show();
    };

    ExamProctor.prototype._showDeniedMessage = function () {
        var cfg = this.config;
        $('#proctor-grant-btn').hide();
        var $box = $('#proctor-denied-msg');
        $box.show();

        if (cfg.onDenied === 'reject') {
            $box.addClass('is-reject');
            $('#proctor-denied-detail').html(
                this._t(
                    'You must grant all permissions to start the assessment',
                    'يجب منح جميع الصلاحيات لبدء الاختبار'
                )
            );
            $('#proctor-back-btn').show();
            $('#proctor-retry-btn').show();
            $('#proctor-continue-btn').hide();
        } else {
            $box.removeClass('is-reject');
            $('#proctor-denied-detail').html(
                this._t(
                    'Some permissions were denied. The exam will continue but monitoring may be limited',
                    'تم رفض بعض الصلاحيات. سيستمر الاختبار لكن المراقبة قد تكون محدودة'
                )
            );
            $('#proctor-continue-btn').show();
            $('#proctor-retry-btn').show();
            $('#proctor-back-btn').hide();
        }
    };

    // --------------------------------------------------------
    // CAPTURE SCHEDULING - Smart Distribution Algorithm
    // --------------------------------------------------------

    /**
     * Calculate the capture schedule: distribute N captures across the exam duration.
     * Always captures at: start (minute 0), end (1 min before exam ends), and evenly in between.
     *
     * @param {number} examDurationMinutes - Total exam time in minutes
     * @param {number} maxCaptures - Number of captures to distribute
     * @returns {number[]} Array of minute marks to capture at
     *
     * Example: calculateCaptureSchedule(40, 6) => [0, 7, 14, 21, 28, 39]
     */
    ExamProctor.prototype.calculateCaptureSchedule = function (examDurationMinutes, maxCaptures) {
        // Disabled
        if (maxCaptures <= 0) return [];

        // Very short exam (<=1 min): just capture at start
        if (examDurationMinutes <= 1) return [0];

        // Cap captures to available minutes (can't take more shots than minutes)
        var effectiveCaptures = Math.min(maxCaptures, examDurationMinutes);

        // Only 1 capture: take it at the start
        if (effectiveCaptures === 1) return [0];

        var endMinute = examDurationMinutes - 1;

        // Only 2 captures: start and end
        if (effectiveCaptures === 2) return [0, endMinute];

        var schedule = [];
        var startMinute = 0;

        // First capture: always at the start
        schedule.push(startMinute);

        // Middle captures: evenly distributed between start and end
        var middleCount = effectiveCaptures - 2;
        var gap = (endMinute - startMinute) / (middleCount + 1);
        for (var i = 1; i <= middleCount; i++) {
            var minute = Math.round(startMinute + gap * i);
            // Avoid duplicates with start or end
            if (minute > startMinute && minute < endMinute) {
                schedule.push(minute);
            }
        }

        // Last capture: always 1 minute before exam ends
        schedule.push(endMinute);

        return schedule;
    };

    /**
     * Start the capture timers based on calculated schedules.
     * Called after permissions are granted (or partially granted in 'continue' mode).
     */
    ExamProctor.prototype._startCapturing = function () {
        var self = this;
        this.startTime = Date.now();

        // Read exam duration NOW (after permissions granted, clock shows actual remaining time)
        this._readExamDuration();


        // Calculate schedules and store as instance properties (used by _captureFinalShots)
        this.selfieSchedule = this.permissionsGranted.camera
            ? this.calculateCaptureSchedule(this.examDurationMinutes, this.config.maxSelfies)
            : [];
        this.screenshotSchedule = this.permissionsGranted.screen
            ? this.calculateCaptureSchedule(this.examDurationMinutes, this.config.maxScreenshots)
            : [];
        var selfieSchedule = this.selfieSchedule;
        var screenshotSchedule = this.screenshotSchedule;


        // Schedule selfie captures
        selfieSchedule.forEach(function (minute, index) {
            var delayMs = minute === 0 ? 1000 : minute * 60 * 1000;
            var t = setTimeout(function () {
                self._captureSelfie(minute);
            }, delayMs);
            self.selfieTimeouts.push(t);
        });

        // Schedule screenshot captures
        screenshotSchedule.forEach(function (minute, index) {
            var delayMs = minute === 0 ? 1500 : minute * 60 * 1000;
            var t = setTimeout(function () {
                self._captureScreenshot(minute);
            }, delayMs);
            self.screenshotTimeouts.push(t);
        });

        // Create the floating status badge
        this._createStatusBadge();

        // Watch streams for unexpected stops (user disables camera/screen sharing)
        this._watchStreamHealth();
    };

    /**
     * Resume capturing after a stream was lost and re-granted mid-exam.
     * Only schedules captures that haven't been taken yet, based on elapsed time.
     * Does NOT reset startTime or existing captures arrays.
     */
    ExamProctor.prototype._resumeCapturing = function () {
        var self = this;
        var elapsedMs = Date.now() - this.startTime;
        var elapsedMinutes = Math.floor(elapsedMs / 60000);


        // Recalculate schedules for the full exam duration (same as original)
        this.selfieSchedule = this.permissionsGranted.camera
            ? this.calculateCaptureSchedule(this.examDurationMinutes, this.config.maxSelfies)
            : [];
        this.screenshotSchedule = this.permissionsGranted.screen
            ? this.calculateCaptureSchedule(this.examDurationMinutes, this.config.maxScreenshots)
            : [];

        // Schedule only future selfie captures (skip already-taken ones)
        this.selfieSchedule.forEach(function (minute) {
            if (minute > elapsedMinutes) {
                var delayMs = (minute * 60 * 1000) - elapsedMs;
                var t = setTimeout(function () { self._captureSelfie(minute); }, delayMs);
                self.selfieTimeouts.push(t);
            }
        });

        // Schedule only future screenshot captures
        this.screenshotSchedule.forEach(function (minute) {
            if (minute > elapsedMinutes) {
                var delayMs = (minute * 60 * 1000) - elapsedMs;
                var t = setTimeout(function () { self._captureScreenshot(minute); }, delayMs);
                self.screenshotTimeouts.push(t);
            }
        });

        // Re-watch the new streams for future stops
        this._watchStreamHealth();
        this._updateStatusBadge();

    };

    // --------------------------------------------------------
    // IMAGE CAPTURE
    // --------------------------------------------------------

    /**
     * Capture a selfie frame from the webcam video stream.
     * @param {number} scheduledMinute - The minute mark this capture was scheduled for
     */
    ExamProctor.prototype._captureSelfie = function (scheduledMinute) {

        if (this.isDestroyed) return;
        if (!this.webcamVideo || !this.webcamStream) {
            return;
        }

        // Skip if tab is hidden (canvas draw may fail)
        if (document.hidden) {
            var self = this;
            var retries = 0;
            var retryFn = function () {
                if (retries >= 3 || self.isDestroyed) return;
                retries++;
                if (!document.hidden) {
                    self._captureSelfie(scheduledMinute);
                } else {
                    setTimeout(retryFn, 5000);
                }
            };
            setTimeout(retryFn, 5000);
            return;
        }

        try {
            var canvas = document.createElement('canvas');
            var video = this.webcamVideo;
            canvas.width = Math.min(video.videoWidth || this.config.selfieMaxWidth, this.config.selfieMaxWidth);
            canvas.height = Math.min(video.videoHeight || this.config.selfieMaxHeight, this.config.selfieMaxHeight);

            var ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            var self = this;
            canvas.toBlob(function (blob) {
                if (blob) {
                    self.selfies.push({ blob: blob, minute: scheduledMinute });
                    self._updateStatusBadge();

                    if (self.config.onCapture) {
                        self.config.onCapture('selfie', blob, scheduledMinute);
                    }
                }
            }, 'image/jpeg', this.config.imageQuality);
        } catch (e) {
            console.error('[ExamProctor] Selfie capture error:', e);
            if (this.config.onError) this.config.onError('selfie_capture', e);
        }
    };

    /**
     * Capture a screenshot frame from the screen share stream.
     * @param {number} scheduledMinute - The minute mark this capture was scheduled for
     */
    ExamProctor.prototype._captureScreenshot = function (scheduledMinute) {

        if (this.isDestroyed) return;
        if (!this.screenVideo || !this.screenStream) {
            return;
        }

        // Skip if tab is hidden
        if (document.hidden) {
            var self = this;
            var retries = 0;
            var retryFn = function () {
                if (retries >= 3 || self.isDestroyed) return;
                retries++;
                if (!document.hidden) {
                    self._captureScreenshot(scheduledMinute);
                } else {
                    setTimeout(retryFn, 5000);
                }
            };
            setTimeout(retryFn, 5000);
            return;
        }

        try {
            var canvas = document.createElement('canvas');
            var video = this.screenVideo;
            canvas.width = Math.min(video.videoWidth || this.config.screenshotMaxWidth, this.config.screenshotMaxWidth);
            canvas.height = Math.min(video.videoHeight || this.config.screenshotMaxHeight, this.config.screenshotMaxHeight);

            var ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            var self = this;
            canvas.toBlob(function (blob) {
                if (blob) {
                    self.screenshots.push({ blob: blob, minute: scheduledMinute });
                    self._updateStatusBadge();

                    if (self.config.onCapture) {
                        self.config.onCapture('screenshot', blob, scheduledMinute);
                    }
                }
            }, 'image/jpeg', this.config.imageQuality);
        } catch (e) {
            console.error('[ExamProctor] Screenshot capture error:', e);
            if (this.config.onError) this.config.onError('screenshot_capture', e);
        }
    };

    // --------------------------------------------------------
    // FLOATING STATUS BADGE
    // --------------------------------------------------------

    /**
     * Create the floating status badge that shows proctoring is active.
     * Displays capture counts with a pulsing indicator.
     */
    ExamProctor.prototype._createStatusBadge = function () {
        // Inject CSS for the badge and pulse animation
        if (!document.getElementById('proctor-badge-styles')) {
            var style = document.createElement('style');
            style.id = 'proctor-badge-styles';
            style.textContent = '' +
                '@keyframes proctor-pulse {' +
                '  0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }' +
                '  70% { box-shadow: 0 0 0 8px rgba(40, 167, 69, 0); }' +
                '  100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }' +
                '}' +
                '.proctor-status-badge {' +
                '  position: fixed;' +
                '  top: 20px;' +
                '  right: 20px;' +
                '  background: rgba(0, 0, 0, 0.85);' +
                '  color: #fff;' +
                '  padding: 10px 16px;' +
                '  border-radius: 12px;' +
                '  font-size: 13px;' +
                '  z-index: 9999;' +
                '  display: flex;' +
                '  align-items: center;' +
                '  gap: 10px;' +
                '  font-family: sans-serif;' +
                '  box-shadow: 0 2px 12px rgba(0,0,0,0.3);' +
                '  transition: all 0.3s ease;' +
                '}' +
                '.proctor-dot {' +
                '  width: 10px;' +
                '  height: 10px;' +
                '  border-radius: 50%;' +
                '  display: inline-block;' +
                '  flex-shrink: 0;' +
                '}' +
                '.proctor-dot-green {' +
                '  background: #28a745;' +
                '  animation: proctor-pulse 2s infinite;' +
                '}' +
                '.proctor-dot-yellow {' +
                '  background: #ffc107;' +
                '  animation: proctor-pulse 2s infinite;' +
                '}' +
                '.proctor-dot-orange {' +
                '  background: #fd7e14;' +
                '}' +
                '.proctor-dot-gray {' +
                '  background: #6c757d;' +
                '}' +
                '.proctor-badge-info {' +
                '  display: flex;' +
                '  flex-direction: column;' +
                '  gap: 2px;' +
                '}' +
                '.proctor-badge-title {' +
                '  font-weight: 600;' +
                '  font-size: 16px;' +
                '}' +
                '.proctor-badge-counts {' +
                '  font-size: 11px;' +
                '  opacity: 0.85;' +
                '}' +
                '.proctor-badge-active {' +
                '  flex-direction: column;' +
                '  align-items: center;' +
                '  text-align: center;' +
                '  gap: 5px;' +
                '}' +
                '@keyframes proctor-eye-blink {' +
                '  0%, 80%, 100% { transform: scaleY(1); opacity: 1; }' +
                '  90% { transform: scaleY(0.06); opacity: 0.8; }' +
                '}' +
                '.proctor-eye-icon {' +
                '  font-size: 22px;' +
                '  color: #28a745;' +
                '  display: inline-block;' +
                '  transform-origin: center;' +
                '  animation: proctor-eye-blink 3.5s ease-in-out infinite;' +
                '}';
            document.head.appendChild(style);
        }

        // Create badge element
        this.badgeElement = document.createElement('div');
        this.badgeElement.className = 'proctor-status-badge';
        document.body.appendChild(this.badgeElement);

        // Initial render
        this._updateStatusBadge();
    };

    /**
     * Update the status badge with current capture counts and state.
     */
    ExamProctor.prototype._updateStatusBadge = function () {
        if (!this.badgeElement) return;

        var cam = this.permissionsGranted.camera;
        var scr = this.permissionsGranted.screen;
        var selfieCount = this.selfies.length;
        var screenshotCount = this.screenshots.length;
        var maxSelfies = this.config.maxSelfies;
        var maxScreenshots = this.config.maxScreenshots;

        // Determine dot color and status text
        var dotClass, statusText;
        var allSelfiesDone = maxSelfies > 0 && selfieCount >= maxSelfies;
        var allScreenshotsDone = maxScreenshots > 0 && screenshotCount >= maxScreenshots;

        var tablet = isTabletDevice();

        if (allSelfiesDone && (allScreenshotsDone || tablet)) {
            dotClass = 'proctor-dot-gray';
            statusText = this._t('Monitoring Complete', 'اكتملت المراقبة');
        } else if (document.hidden) {
            dotClass = 'proctor-dot-orange';
            statusText = this._t('Proctoring Paused', 'المراقبة متوقفة مؤقتاً');
        } else if (cam && (scr || tablet)) {
            dotClass = 'proctor-dot-green';
            statusText = this._t('Proctoring Active', 'المراقبة نشطة');
        } else if (cam || scr) {
            dotClass = 'proctor-dot-yellow';
            statusText = cam
                ? this._t('Camera Active', 'الكاميرا نشطة')
                : this._t('Screen Active / Camera Inactive', 'الشاشة نشطة / الكاميرا غير نشطة');
        } else {
            dotClass = 'proctor-dot-orange';
            statusText = this._t('Monitoring Inactive', 'المراقبة غير نشطة');
        }

        // Build counts text (only if enabled in config)
        var countsText = '';
        if (this.config.showBadgeCounts) {
            var countsArr = [];
            if (maxSelfies > 0) {
                countsArr.push('&#128247; ' + selfieCount + '/' + maxSelfies);
            }
            if (maxScreenshots > 0) {
                countsArr.push('&#128421; ' + screenshotCount + '/' + maxScreenshots);
            }
            countsText = countsArr.join('&nbsp;&nbsp;|&nbsp;&nbsp;');
        }

        // When fully active: show animated eye above text (column layout)
        // Otherwise: show dot beside text (row layout)
        if (cam && (scr || tablet) && !allSelfiesDone) {
            this.badgeElement.className = 'proctor-status-badge proctor-badge-active';
            this.badgeElement.innerHTML = '' +
                '<i class="fas fa-eye proctor-eye-icon"></i>' +
                '<div class="proctor-badge-info">' +
                '  <div class="proctor-badge-title">' + statusText + '</div>' +
                (countsText ? '  <div class="proctor-badge-counts">' + countsText + '</div>' : '') +
                '</div>';
        } else {
            this.badgeElement.className = 'proctor-status-badge';
            this.badgeElement.innerHTML = '' +
                '<span class="proctor-dot ' + dotClass + '"></span>' +
                '<div class="proctor-badge-info">' +
                '  <div class="proctor-badge-title">' + statusText + '</div>' +
                (countsText ? '  <div class="proctor-badge-counts">' + countsText + '</div>' : '') +
                '</div>';
        }
    };

    // --------------------------------------------------------
    // FINAL CAPTURE - Take remaining shots before submit
    // --------------------------------------------------------

    /**
     * Synchronously capture any remaining selfies/screenshots before form submission.
     * Uses toDataURL (sync) instead of toBlob (async) to guarantee captures
     * are available immediately for form injection.
     */
    ExamProctor.prototype._captureFinalShots = function () {
        var elapsedMinute = this.startTime
            ? Math.floor((Date.now() - this.startTime) / 60000)
            : 0;

        // Always take ONE final selfie on submit (sync capture for guaranteed availability)
        if (this.permissionsGranted.camera && this.webcamVideo && this.webcamStream && this.config.maxSelfies > 0) {
            try {
                var blob = this._captureSync(
                    this.webcamVideo,
                    this.config.selfieMaxWidth,
                    this.config.selfieMaxHeight
                );
                if (blob) {
                    this.selfies.push({ blob: blob, minute: elapsedMinute });
                }
            } catch (e) {
                console.error('[ExamProctor] Final selfie capture error:', e);
            }
        }

        // Always take ONE final screenshot on submit (sync capture for guaranteed availability)
        if (this.permissionsGranted.screen && this.screenVideo && this.screenStream && this.config.maxScreenshots > 0) {
            try {
                var blob = this._captureSync(
                    this.screenVideo,
                    this.config.screenshotMaxWidth,
                    this.config.screenshotMaxHeight
                );
                if (blob) {
                    this.screenshots.push({ blob: blob, minute: elapsedMinute });
                }
            } catch (e) {
                console.error('[ExamProctor] Final screenshot capture error:', e);
            }
        }

        this._updateStatusBadge();
    };

    /**
     * Synchronous capture: draw video frame to canvas and return Blob immediately.
     * Uses dataURL -> Blob conversion (synchronous) instead of toBlob (async).
     *
     * @param {HTMLVideoElement} video - Source video element
     * @param {number} maxW - Max width
     * @param {number} maxH - Max height
     * @returns {Blob|null}
     */
    ExamProctor.prototype._captureSync = function (video, maxW, maxH) {
        var canvas = document.createElement('canvas');
        canvas.width = Math.min(video.videoWidth || maxW, maxW);
        canvas.height = Math.min(video.videoHeight || maxH, maxH);

        var ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert dataURL to Blob synchronously
        var dataURL = canvas.toDataURL('image/jpeg', this.config.imageQuality);
        var parts = dataURL.split(',');
        var byteString = atob(parts[1]);
        var mimeString = parts[0].split(':')[1].split(';')[0];
        var ab = new ArrayBuffer(byteString.length);
        var ia = new Uint8Array(ab);
        for (var i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }
        return new Blob([ab], { type: mimeString });
    };

    // --------------------------------------------------------
    // FORM INJECTION - Append captures to exam form on submit
    // --------------------------------------------------------

    /**
     * Hook into the form submission process.
     * Injects captured images as file inputs BEFORE the form is submitted.
     */
    ExamProctor.prototype._hookFormSubmission = function () {
        var self = this;
        this._injected = false; // Prevent double injection

        /**
         * Core injection logic - called before any form submission.
         * Captures final shots, injects files into form, stops capturing.
         */
        var doInjection = function () {
            if (self._injected) {
                return;
            }
            var form = document.querySelector(self.config.formSelector);
            if (form) {
                self._captureFinalShots(); // Take one last shot if needed
                self._injectIntoForm(form);
                self._stopCapturing();
                self._injected = true;
            } else {
            }
        };

        // Hook 1: Confirm button click (manual submit)
        // Runs BEFORE any jQuery handlers via capture phase
        var confirmBtn = document.querySelector(this.config.confirmButtonSelector);
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                doInjection();
            }, true); // useCapture = true to run BEFORE other handlers
        }

        // Hook 2: jQuery submit event (runs BEFORE ajaxSubmit serializes form data)
        // IMPORTANT: We use jQuery's event system here, not native addEventListener.
        // When jQuery's .submit() is called (e.g., by auto-submit timer), jQuery triggers
        // its own handlers FIRST, then dispatches the native event. If we only used
        // native addEventListener, ajaxSubmit would serialize the form BEFORE our
        // native listener runs, missing the injected files.
        // By binding via jQuery, our handler runs in jQuery's own event queue,
        // BEFORE the ajaxSubmit handler (because we reorder it to first position).
        var $forms = $(this.config.formSelector);
        if ($forms.length) {
            $forms.on('submit.examProctor', function () {
                doInjection();
            });
            // Move our handler to the FIRST position in jQuery's event queue for each form
            $forms.each(function () {
                var events = $._data(this, 'events');
                if (events && events.submit && events.submit.length > 1) {
                    events.submit.unshift(events.submit.pop());
                }
            });
        }

        // Hook 3: Native capture-phase listener as backup
        // This ensures injection happens even if jQuery event ordering fails
        var formEl = document.querySelector(this.config.formSelector);
        if (formEl) {
            formEl.addEventListener('submit', function () {
                doInjection();
            }, true);
        }
    };

    /**
     * Inject all captured selfies and screenshots into the form as file inputs.
     * Uses the DataTransfer API to create synthetic file inputs.
     *
     * @param {HTMLFormElement} formElement - The exam form element
     */
    ExamProctor.prototype._injectIntoForm = function (formElement) {
        // Remove any previously injected inputs (in case of re-submission attempts)
        var old = formElement.querySelectorAll('.proctor-injected');
        for (var i = 0; i < old.length; i++) {
            old[i].parentNode.removeChild(old[i]);
        }

        // Inject selfies
        this._injectFiles(formElement, this.selfies, 'proctor_selfies', 'proctor_selfie_minutes', 'selfie');

        // Inject screenshots
        this._injectFiles(formElement, this.screenshots, 'proctor_screenshots', 'proctor_screenshot_minutes', 'screenshot');

    };

    /**
     * Inject an array of captured blobs as file inputs into a form.
     *
     * @param {HTMLFormElement} form - Target form
     * @param {Array} captures - Array of { blob, minute }
     * @param {string} fileInputName - Name for the file input (e.g., 'proctor_selfies[]')
     * @param {string} minuteInputName - Name for the minute hidden input (e.g., 'proctor_selfie_minutes[]')
     * @param {string} prefix - Filename prefix (e.g., 'selfie')
     */
    ExamProctor.prototype._injectFiles = function (form, captures, fileInputName, minuteInputName, prefix) {
        for (var i = 0; i < captures.length; i++) {
            var capture = captures[i];
            if (!capture.blob) continue;

            try {
                // Create a File from the Blob
                var file = new File(
                    [capture.blob],
                    prefix + '_' + capture.minute + 'min_' + i + '.jpg',
                    { type: 'image/jpeg' }
                );

                // Use DataTransfer to assign File to an input element
                var dt = new DataTransfer();
                dt.items.add(file);

                var fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = fileInputName + '[]';
                fileInput.className = 'proctor-injected';
                fileInput.style.display = 'none';
                fileInput.files = dt.files;
                form.appendChild(fileInput);

                // Hidden input for the capture minute
                var minuteInput = document.createElement('input');
                minuteInput.type = 'hidden';
                minuteInput.name = minuteInputName + '[]';
                minuteInput.value = capture.minute;
                minuteInput.className = 'proctor-injected';
                form.appendChild(minuteInput);

            } catch (e) {
                console.error('[ExamProctor] Error injecting file ' + i + ':', e);
                // Fallback: try without DataTransfer (older browsers)
                // In this case, we'll send as base64 hidden input instead
                try {
                    var reader = new FileReader();
                    reader.onloadend = (function (idx, min) {
                        return function () {
                            var base64Input = document.createElement('input');
                            base64Input.type = 'hidden';
                            base64Input.name = fileInputName + '_base64[]';
                            base64Input.value = reader.result;
                            base64Input.className = 'proctor-injected';
                            form.appendChild(base64Input);

                            var minInput = document.createElement('input');
                            minInput.type = 'hidden';
                            minInput.name = minuteInputName + '[]';
                            minInput.value = min;
                            minInput.className = 'proctor-injected';
                            form.appendChild(minInput);
                        };
                    })(i, capture.minute);
                    reader.readAsDataURL(capture.blob);
                } catch (e2) {
                    console.error('[ExamProctor] Base64 fallback also failed:', e2);
                }
            }
        }
    };

    // --------------------------------------------------------
    // CLEANUP
    // --------------------------------------------------------

    /**
     * Stop all capture timers and release media streams.
     */
    ExamProctor.prototype._stopCapturing = function () {
        // Clear all scheduled timeouts
        this.selfieTimeouts.forEach(function (t) { clearTimeout(t); });
        this.screenshotTimeouts.forEach(function (t) { clearTimeout(t); });
        this.selfieTimeouts = [];
        this.screenshotTimeouts = [];

        // Stop webcam stream
        if (this.webcamStream) {
            this.webcamStream.getTracks().forEach(function (track) { track.stop(); });
            this.webcamStream = null;
        }
        // Stop screen stream
        if (this.screenStream) {
            this.screenStream.getTracks().forEach(function (track) { track.stop(); });
            this.screenStream = null;
        }

    };

    /**
     * Full cleanup: stop capturing, remove DOM elements, reset state.
     */
    ExamProctor.prototype.destroy = function () {
        this.isDestroyed = true;
        this._stopCapturing();

        // Remove hidden video elements
        if (this.webcamVideo && this.webcamVideo.parentNode) {
            this.webcamVideo.parentNode.removeChild(this.webcamVideo);
        }
        if (this.screenVideo && this.screenVideo.parentNode) {
            this.screenVideo.parentNode.removeChild(this.screenVideo);
        }

        // Remove status badge
        if (this.badgeElement && this.badgeElement.parentNode) {
            this.badgeElement.parentNode.removeChild(this.badgeElement);
        }

        // Remove permission modal
        $('#modal-proctor-permissions').remove();

        this.selfies = [];
        this.screenshots = [];

    };

    // --------------------------------------------------------
    // UTILITY - Bilingual text helper
    // --------------------------------------------------------

    /**
     * Return text based on configured language.
     * @param {string} en - English text
     * @param {string} ar - Arabic text
     * @returns {string}
     */
    ExamProctor.prototype._t = function (en, ar) {
        if (this.config.lang === 'en') return en;
        if (this.config.lang === 'ar') return ar;
        // 'both' - return both with separator
        return ar + ' </br> ' + en;
    };

    // --------------------------------------------------------
    // AUTO-INITIALIZATION
    // --------------------------------------------------------

    // Create global instance and auto-init on DOM ready
    var proctorInstance = new ExamProctor(EXAM_PROCTOR_CONFIG);

    // Expose globally for external access
    window.ExamProctor = proctorInstance;

    // Auto-initialize when DOM is ready
    if (typeof $ !== 'undefined' && $.fn) {
        $(document).ready(function () {
            proctorInstance.init();
        });
    } else {
        // Fallback if jQuery is not available
        document.addEventListener('DOMContentLoaded', function () {
            proctorInstance.init();
        });
    }

})(window, window.jQuery || window.$);
