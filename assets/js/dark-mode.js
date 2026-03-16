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
            var html = document.documentElement;
            var duration = animate ? this.config.transitionDuration : 0;
            var colorEngine = this.config.colorEngine || 'css_variables';

            // Set transition duration
            html.style.setProperty('--dmp-transition-duration', duration + 'ms');

            if (this.isDark) {
                body.classList.add('dmp-dark-mode');
                html.classList.add('dmp-dark-mode'); // For filter mode selector

                if (colorEngine === 'css_variables') {
                    this.applyColors();
                    this.applyTypography();
                } else if (colorEngine === 'css_filter') {
                    html.classList.add('dmp-type-filter');
                    this.applyTypography();
                } else if (colorEngine === 'js_extraction') {
                    this.applyDynamicColors();
                    this.applyTypography();
                }

            } else {
                body.classList.remove('dmp-dark-mode');
                html.classList.remove('dmp-dark-mode');

                if (colorEngine === 'css_filter') {
                    html.classList.remove('dmp-type-filter');
                } else if (colorEngine === 'js_extraction') {
                    this.removeDynamicColors();
                }

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
         * Apply Dynamic Colors (JS Extraction Mode)
         */
        applyDynamicColors: function() {
            var self = this;
            // Optimizing selector to avoid too many elements, but keeping it broad enough
            var elements = document.querySelectorAll('body, body *:not(script):not(style):not(noscript):not([class*="dmp-"])');

            // Batch DOM reads
            var updates = [];

            elements.forEach(function(el) {
                var style = window.getComputedStyle(el);
                var update = { el: el, styles: {} };
                var hasUpdates = false;

                // Mark element as processed
                if (!el.hasAttribute('data-dmp-processed')) {
                    el.setAttribute('data-dmp-processed', 'true');
                    // Store original inline styles to restore later
                    el.setAttribute('data-dmp-inline-bg', el.style.backgroundColor);
                    el.setAttribute('data-dmp-inline-color', el.style.color);
                    el.setAttribute('data-dmp-inline-border', el.style.borderColor);
                }

                // Process Background
                var bgColor = style.backgroundColor;
                if (self.isValidColor(bgColor) && self.isLight(bgColor)) {
                    var darkBg = self.invertColor(bgColor, true);
                    update.styles.backgroundColor = darkBg;
                    hasUpdates = true;
                }

                // Process Text Color
                var textColor = style.color;
                if (self.isValidColor(textColor) && self.isDarkColor(textColor)) {
                    var lightText = self.invertColor(textColor, false);
                    update.styles.color = lightText;
                    hasUpdates = true;
                }

                // Process Border Color
                var borderColor = style.borderColor;
                if (self.isValidColor(borderColor) && self.isDarkColor(borderColor)) {
                     var lightBorder = self.invertColor(borderColor, false);
                     update.styles.borderColor = lightBorder;
                     hasUpdates = true;
                }

                if (hasUpdates) {
                    updates.push(update);
                }
            });

            // Batch DOM writes
            updates.forEach(function(item) {
                for (var prop in item.styles) {
                    item.el.style[prop] = item.styles[prop];
                }
            });
        },

        /**
         * Remove Dynamic Colors
         */
        removeDynamicColors: function() {
            var elements = document.querySelectorAll('[data-dmp-processed]');
            elements.forEach(function(el) {
                // Restore original inline styles (or empty string if none existed)
                el.style.backgroundColor = el.getAttribute('data-dmp-inline-bg') || '';
                el.style.color = el.getAttribute('data-dmp-inline-color') || '';
                el.style.borderColor = el.getAttribute('data-dmp-inline-border') || '';

                // Cleanup attributes
                el.removeAttribute('data-dmp-processed');
                el.removeAttribute('data-dmp-inline-bg');
                el.removeAttribute('data-dmp-inline-color');
                el.removeAttribute('data-dmp-inline-border');
            });
        },

        /**
         * Helper: Check if color is valid and not transparent
         */
        isValidColor: function(color) {
            return color && color !== 'rgba(0, 0, 0, 0)' && color !== 'transparent';
        },

        /**
         * Helper: Check if color is light
         */
        isLight: function(color) {
            var rgb = this.parseColor(color);
            if (!rgb) return false;
            // HSP equation from http://alienryderflex.com/hsp.html
            var hsp = Math.sqrt(
                0.299 * (rgb.r * rgb.r) +
                0.587 * (rgb.g * rgb.g) +
                0.114 * (rgb.b * rgb.b)
            );
            return hsp > 127.5;
        },

        /**
         * Helper: Check if color is dark
         */
        isDarkColor: function(color) {
            return !this.isLight(color);
        },

        /**
         * Helper: Parse RGB/RGBA string
         */
        parseColor: function(color) {
            var match = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)/);
            return match ? { r: parseInt(match[1]), g: parseInt(match[2]), b: parseInt(match[3]) } : null;
        },

        /**
         * Helper: Invert color intelligently
         */
        invertColor: function(color, darken) {
             var rgb = this.parseColor(color);
             if (!rgb) return color;

             // Simple inversion logic for now
             var r, g, b;

             if (darken) {
                 // Make it dark (for backgrounds)
                 // Map 255 -> 26 (approx #1a1a1a), 0 -> 0
                 var factor = 0.1;
                 r = Math.floor(rgb.r * factor);
                 g = Math.floor(rgb.g * factor);
                 b = Math.floor(rgb.b * factor);

                 // If original was white, make it specific dark
                 if (rgb.r > 250 && rgb.g > 250 && rgb.b > 250) {
                     return '#1a1a2e'; // Use plugin theme background
                 }
             } else {
                 // Make it light (for text)
                 // Map 0 -> 229 (approx #e5e5e5), 255 -> 255
                 var min = 200;
                 r = Math.max(min, 255 - rgb.r);
                 g = Math.max(min, 255 - rgb.g);
                 b = Math.max(min, 255 - rgb.b);
             }

             return 'rgb(' + r + ',' + g + ',' + b + ')';
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
            var previousMode = this.isDark;
            var newIsDark = mode === 'dark';

            if (newIsDark === this.isDark) {
                return;
            }

            if (this.config.analyticsEnabled && this.config.trackTimeSpent && this.modeStartTime) {
                var duration = Math.round((Date.now() - this.modeStartTime) / 1000);
                this.trackEvent('time_spent', {
                    mode: previousMode ? 'dark' : 'light',
                    duration: duration
                });
            }

            this.isDark = newIsDark;
            this.modeStartTime = Date.now();
            this.applyMode(true);

            if (this.config.analyticsEnabled && this.config.trackToggleEvents) {
                this.trackEvent('toggle', {
                    from_mode: previousMode ? 'dark' : 'light',
                    to_mode: this.isDark ? 'dark' : 'light'
                });
            }
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;

            // Native checkbox toggles (label-based styles)
            $(document).on('change', '.dmp-toggle-input', function() {
                self.setMode(this.checked ? 'dark' : 'light');
            });

            // Button-based toggles
            $(document).on('click', 'button.dmp-switch, [class*="dmp-switch-icon_"], [class*="dmp-switch-text_"]', function(e) {
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
