/**
 * Dark Mode Pro - Frontend JavaScript
 *
 * @package Dark_Mode_Pro
 */

(function($) {
    'use strict';

    // Main Dark Mode Pro object
    window.DarkModePro = {
        // Configuration
        config: null,
        colors: null,
        sunTimes: null,

        // State
        isDark: false,
        sessionId: null,
        modeStartTime: null,
        initialized: false,

        /**
         * Initialize
         */
        init: function() {
            if (this.initialized) return;

            this.config = window.dmpConfig ? dmpConfig.options : {};
            this.colors = window.dmpConfig ? dmpConfig.colorPreset : {};
            this.sunTimes = window.dmpConfig ? dmpConfig.sunTimes : {};

            this.sessionId = this.getOrCreateSessionId();
            this.determineInitialMode();
            this.bindEvents();
            this.setupKeyboardShortcut();
            this.setupSystemPreferenceListener();
            this.setupTimeBasedChecks();

            this.initialized = true;
            this.modeStartTime = Date.now();

            // Track initial mode preference
            if (this.config.analyticsEnabled && this.config.trackToggleEvents) {
                this.trackEvent('mode_preference', {
                    mode: this.isDark ? 'dark' : 'light',
                    source: this.getInitialModeSource()
                });
            }
        },

        /**
         * Get or create session ID
         */
        getOrCreateSessionId: function() {
            var sessionId = this.getCookie('dmp_session_id');
            if (!sessionId) {
                sessionId = this.generateUUID();
                this.setCookie('dmp_session_id', sessionId, 30);
            }
            return sessionId;
        },

        /**
         * Generate UUID
         */
        generateUUID: function() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0;
                var v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        },

        /**
         * Determine initial mode
         */
        determineInitialMode: function() {
            var savedMode = this.config.rememberChoice ? this.getStoredMode() : null;

            if (savedMode) {
                this.isDark = savedMode === 'dark';
            } else if (this.config.timeBasedEnabled) {
                this.isDark = this.shouldBeDarkByTime();
            } else if (this.config.defaultMode === 'system') {
                this.isDark = this.getSystemPreference();
            } else {
                this.isDark = this.config.defaultMode === 'dark';
            }

            this.applyMode(false);
        },

        /**
         * Get initial mode source
         */
        getInitialModeSource: function() {
            if (this.config.rememberChoice && this.getStoredMode()) {
                return 'manual';
            }
            if (this.config.timeBasedEnabled) {
                return 'scheduled';
            }
            if (this.config.defaultMode === 'system') {
                return 'system';
            }
            return 'default';
        },

        /**
         * Get stored mode from localStorage
         */
        getStoredMode: function() {
            try {
                return localStorage.getItem('dmp_mode');
            } catch (e) {
                return null;
            }
        },

        /**
         * Store mode in localStorage
         */
        storeMode: function(mode) {
            try {
                localStorage.setItem('dmp_mode', mode);
            } catch (e) {
                // localStorage not available
            }
        },

        /**
         * Get system preference
         */
        getSystemPreference: function() {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        },

        /**
         * Should be dark based on time
         */
        shouldBeDarkByTime: function() {
            var mode = this.config.timeBasedMode;

            if (mode === 'system') {
                return this.getSystemPreference();
            }

            if (mode === 'sunset') {
                return this.shouldBeDarkBySunset();
            }

            if (mode === 'schedule') {
                return this.shouldBeDarkBySchedule();
            }

            return false;
        },

        /**
         * Check sunset/sunrise times
         */
        shouldBeDarkBySunset: function() {
            var now = new Date();
            var currentMinutes = now.getHours() * 60 + now.getMinutes();

            var sunrise = this.timeToMinutes(this.sunTimes.sunrise || '06:30');
            var sunset = this.timeToMinutes(this.sunTimes.sunset || '18:30');

            return currentMinutes >= sunset || currentMinutes < sunrise;
        },

        /**
         * Check schedule times
         */
        shouldBeDarkBySchedule: function() {
            var now = new Date();
            var currentMinutes = now.getHours() * 60 + now.getMinutes();

            var start = this.timeToMinutes(this.config.scheduleStart || '19:00');
            var end = this.timeToMinutes(this.config.scheduleEnd || '07:00');

            // Handle overnight schedules
            if (start > end) {
                return currentMinutes >= start || currentMinutes < end;
            }

            return currentMinutes >= start && currentMinutes < end;
        },

        /**
         * Convert time string to minutes
         */
        timeToMinutes: function(time) {
            var parts = time.split(':');
            return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
        },

        /**
         * Apply mode to document
         */
        applyMode: function(animate) {
            var body = document.body;
            var duration = animate ? this.config.transitionDuration : 0;

            // Set transition duration
            document.documentElement.style.setProperty('--dmp-transition-duration', duration + 'ms');

            if (this.isDark) {
                body.classList.add('dmp-dark-mode');
                this.applyColors();
                this.applyTypography();
            } else {
                body.classList.remove('dmp-dark-mode');
                this.removeTypography();
            }

            // Update toggle switches
            this.updateToggles();

            // Store preference
            if (this.config.rememberChoice) {
                this.storeMode(this.isDark ? 'dark' : 'light');
            }

            // Trigger custom event
            $(document).trigger('dmp:modeChanged', { isDark: this.isDark });
        },

        /**
         * Apply color CSS variables
         */
        applyColors: function() {
            var root = document.documentElement;

            if (this.colors) {
                for (var key in this.colors) {
                    if (this.colors.hasOwnProperty(key)) {
                        var cssKey = '--dmp-' + key.replace(/_/g, '-');
                        root.style.setProperty(cssKey, this.colors[key]);
                    }
                }
            }
        },

        /**
         * Apply typography adjustments
         */
        applyTypography: function() {
            if (!this.config.typographyEnabled) return;

            var root = document.documentElement;

            if (this.config.fontSizeAdjust) {
                root.style.setProperty('--dmp-font-size-adjust', this.config.fontSizeAdjust + 'px');
            }

            if (this.config.lineHeightAdjust) {
                root.style.setProperty('--dmp-line-height-adjust', this.config.lineHeightAdjust);
            }

            if (this.config.letterSpacing) {
                root.style.setProperty('--dmp-letter-spacing', this.config.letterSpacing + 'px');
            }
        },

        /**
         * Remove typography adjustments
         */
        removeTypography: function() {
            var root = document.documentElement;
            root.style.removeProperty('--dmp-font-size-adjust');
            root.style.removeProperty('--dmp-line-height-adjust');
            root.style.removeProperty('--dmp-letter-spacing');
        },

        /**
         * Update toggle switches
         */
        updateToggles: function() {
            // Update checkbox toggles
            $('.dmp-toggle-input').prop('checked', this.isDark);

            // Update button toggles
            $('[class*="dmp-switch-icon_"], [class*="dmp-switch-text_"]').attr('aria-pressed', this.isDark);

            // Update ARIA attributes
            $('.dmp-switch').attr('aria-checked', this.isDark);
        },

        /**
         * Toggle dark mode
         */
        toggle: function() {
            var previousMode = this.isDark;

            // Track time spent in previous mode
            if (this.config.analyticsEnabled && this.config.trackTimeSpent && this.modeStartTime) {
                var duration = Math.round((Date.now() - this.modeStartTime) / 1000);
                this.trackEvent('time_spent', {
                    mode: previousMode ? 'dark' : 'light',
                    duration: duration
                });
            }

            this.isDark = !this.isDark;
            this.modeStartTime = Date.now();
            this.applyMode(true);

            // Track toggle event
            if (this.config.analyticsEnabled && this.config.trackToggleEvents) {
                this.trackEvent('toggle', {
                    from_mode: previousMode ? 'dark' : 'light',
                    to_mode: this.isDark ? 'dark' : 'light'
                });
            }
        },

        /**
         * Set mode directly
         */
        setMode: function(mode) {
            var newIsDark = mode === 'dark';
            if (newIsDark !== this.isDark) {
                this.isDark = newIsDark;
                this.modeStartTime = Date.now();
                this.applyMode(true);
            }
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;

            // Toggle switch clicks
            $(document).on('click change', '.dmp-switch, .dmp-toggle-input', function(e) {
                e.preventDefault();
                self.toggle();
            });

            // Button clicks
            $(document).on('click', '[class*="dmp-switch-icon_"], [class*="dmp-switch-text_"]', function(e) {
                e.preventDefault();
                self.toggle();
            });

            // Keyboard navigation for toggles
            $(document).on('keydown', '.dmp-switch', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    self.toggle();
                }
            });

            // Page visibility change - track time spent
            $(document).on('visibilitychange', function() {
                if (document.visibilityState === 'hidden' && self.config.analyticsEnabled && self.config.trackTimeSpent) {
                    var duration = Math.round((Date.now() - self.modeStartTime) / 1000);
                    self.trackEvent('time_spent', {
                        mode: self.isDark ? 'dark' : 'light',
                        duration: duration
                    });
                }
            });
        },

        /**
         * Setup keyboard shortcut
         */
        setupKeyboardShortcut: function() {
            if (!this.config.enableKeyboardShortcut) return;

            var self = this;
            var key = this.config.keyboardShortcut || 'd';

            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + Shift + Key
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key.toLowerCase() === key.toLowerCase()) {
                    e.preventDefault();
                    self.toggle();
                }
            });
        },

        /**
         * Setup system preference listener
         */
        setupSystemPreferenceListener: function() {
            if (!window.matchMedia) return;

            var self = this;
            var mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

            var handler = function(e) {
                // Only react if we're following system preference
                if (self.config.defaultMode === 'system' && !self.getStoredMode()) {
                    self.isDark = e.matches;
                    self.applyMode(true);

                    if (self.config.analyticsEnabled) {
                        self.trackEvent('mode_preference', {
                            mode: self.isDark ? 'dark' : 'light',
                            source: 'system'
                        });
                    }
                }
            };

            // Modern browsers
            if (mediaQuery.addEventListener) {
                mediaQuery.addEventListener('change', handler);
            } else if (mediaQuery.addListener) {
                // Legacy browsers
                mediaQuery.addListener(handler);
            }
        },

        /**
         * Setup time-based checks
         */
        setupTimeBasedChecks: function() {
            if (!this.config.timeBasedEnabled) return;

            var self = this;

            // Check every minute
            setInterval(function() {
                var shouldBeDark = self.shouldBeDarkByTime();

                // Only auto-switch if user hasn't manually toggled
                if (!self.getStoredMode() && shouldBeDark !== self.isDark) {
                    self.isDark = shouldBeDark;
                    self.applyMode(true);

                    if (self.config.analyticsEnabled) {
                        self.trackEvent('mode_preference', {
                            mode: self.isDark ? 'dark' : 'light',
                            source: 'scheduled'
                        });
                    }
                }
            }, 60000);
        },

        /**
         * Track analytics event
         */
        trackEvent: function(eventType, eventData) {
            if (!this.config.analyticsEnabled || !window.dmpConfig) return;

            $.ajax({
                url: dmpConfig.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'dmp_track_event',
                    nonce: dmpConfig.nonce,
                    event_type: eventType,
                    event_data: JSON.stringify(eventData)
                }
            });
        },

        /**
         * Cookie helpers
         */
        getCookie: function(name) {
            var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            return match ? match[2] : null;
        },

        setCookie: function(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + value + expires + '; path=/; SameSite=Lax';
        },

        /**
         * Public API methods
         */
        enable: function() {
            this.setMode('dark');
        },

        disable: function() {
            this.setMode('light');
        },

        isEnabled: function() {
            return this.isDark;
        },

        getMode: function() {
            return this.isDark ? 'dark' : 'light';
        }
    };

    // Initialize on DOM ready
    $(document).ready(function() {
        DarkModePro.init();
    });

    // Also initialize immediately if DOM already loaded
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        DarkModePro.init();
    }

})(jQuery);
