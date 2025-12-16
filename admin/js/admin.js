/**
 * Dark Mode Pro - Admin JavaScript
 *
 * @package Dark_Mode_Pro
 */

(function($) {
    'use strict';

    var DmpAdmin = {
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initColorPickers();
            this.initRangeSliders();
            this.initConditionalFields();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;

            // Tab navigation
            $('.dmp-tab').on('click', function() {
                var tab = $(this).data('tab');
                self.switchTab(tab);
            });

            // Save settings
            $('.dmp-save-btn').on('click', function() {
                self.saveSettings();
            });

            // Reset settings
            $('.dmp-reset-btn').on('click', function() {
                if (confirm(dmpAdmin.strings?.confirmReset || 'Are you sure you want to reset all settings to defaults?')) {
                    self.resetSettings();
                }
            });

            // Test email
            $('.dmp-test-email-btn').on('click', function() {
                self.sendTestEmail();
            });

            // Form change detection
            $('#dmp-settings-form').on('change input', 'input, select, textarea', function() {
                self.markUnsaved();
            });

            // Prevent leaving with unsaved changes
            $(window).on('beforeunload', function() {
                if (self.hasUnsavedChanges) {
                    return 'You have unsaved changes. Are you sure you want to leave?';
                }
            });
        },

        /**
         * Switch tab
         */
        switchTab: function(tab) {
            $('.dmp-tab').removeClass('active');
            $('.dmp-tab[data-tab="' + tab + '"]').addClass('active');

            $('.dmp-tab-content').removeClass('active');
            $('.dmp-tab-content[data-tab="' + tab + '"]').addClass('active');

            // Update URL hash
            history.replaceState(null, null, '#' + tab);
        },

        /**
         * Initialize color pickers
         */
        initColorPickers: function() {
            if (typeof $.fn.wpColorPicker === 'function') {
                $('.dmp-color-picker').wpColorPicker({
                    change: function(event, ui) {
                        DmpAdmin.updateCustomPreview();
                    }
                });
            }
        },

        /**
         * Initialize range sliders
         */
        initRangeSliders: function() {
            $('input[type="range"]').on('input change', function() {
                var $slider = $(this);
                var value = $slider.val();
                var unit = '';

                // Determine unit based on field name
                if ($slider.attr('name').includes('font_size') || $slider.attr('name').includes('letter_spacing') || $slider.attr('name').includes('duration')) {
                    unit = $slider.attr('name').includes('duration') ? 'ms' : 'px';
                }

                $slider.siblings('.dmp-range-value').text(value + unit);
            });
        },

        /**
         * Initialize conditional fields
         */
        initConditionalFields: function() {
            var self = this;

            // Time-based options
            $('input[name="time_based_enabled"]').on('change', function() {
                self.toggleConditional('.dmp-time-based-options', $(this).is(':checked'));
            }).trigger('change');

            // Schedule options
            $('input[name="time_based_mode"]').on('change', function() {
                var showSchedule = $(this).val() === 'schedule' && $('input[name="time_based_enabled"]').is(':checked');
                self.toggleConditional('.dmp-schedule-options', showSchedule);
            }).trigger('change');

            // Typography options
            $('input[name="typography_enabled"]').on('change', function() {
                self.toggleConditional('.dmp-typography-options', $(this).is(':checked'));
            }).trigger('change');

            // Analytics options
            $('input[name="analytics_enabled"]').on('change', function() {
                self.toggleConditional('.dmp-analytics-options', $(this).is(':checked'));
            }).trigger('change');

            // Email options
            $('input[name="email_reports_enabled"]').on('change', function() {
                self.toggleConditional('.dmp-email-options', $(this).is(':checked'));
            }).trigger('change');
        },

        /**
         * Toggle conditional field visibility
         */
        toggleConditional: function(selector, show) {
            var $element = $(selector);
            if (show) {
                $element.slideDown(200);
            } else {
                $element.slideUp(200);
            }
        },

        /**
         * Collect form data
         */
        collectFormData: function() {
            var data = {};
            var $form = $('#dmp-settings-form');

            // Checkboxes
            $form.find('input[type="checkbox"]').each(function() {
                data[$(this).attr('name')] = $(this).is(':checked');
            });

            // Text inputs, selects, textareas
            $form.find('input[type="text"], input[type="email"], input[type="number"], input[type="time"], input[type="range"], select, textarea').not('[name^="custom_colors"]').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    // Handle multi-select
                    if ($(this).is('select[multiple]')) {
                        data[name.replace('[]', '')] = $(this).val() || [];
                    } else {
                        data[name] = $(this).val();
                    }
                }
            });

            // Radio buttons
            $form.find('input[type="radio"]:checked').each(function() {
                data[$(this).attr('name')] = $(this).val();
            });

            // Custom colors
            data.custom_colors = {};
            $form.find('[name^="custom_colors"]').each(function() {
                var key = $(this).attr('name').match(/\[([^\]]+)\]/)[1];
                data.custom_colors[key] = $(this).val();
            });

            return data;
        },

        /**
         * Save partial settings payload
         */
        saveOptionsFragment: function(settings, options) {
            var self = this;
            var opts = $.extend({
                button: null,
                loadingText: '<span class="dashicons dashicons-update spin"></span> Saving...',
                successMessage: '',
                restoreButtonContent: true,
                onSuccess: null,
                onComplete: null
            }, options || {});

            var $btn = opts.button ? $(opts.button) : null;
            var originalHtml = $btn ? $btn.html() : null;

            if ($btn) {
                $btn.prop('disabled', true).html(opts.loadingText);
            }

            return $.ajax({
                url: dmpAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'dmp_save_settings',
                    nonce: dmpAdmin.nonce,
                    settings: JSON.stringify(settings)
                },
                success: function(response) {
                    if (response.success) {
                        if (opts.successMessage) {
                            self.showNotice(opts.successMessage, 'success');
                        }

                        if (typeof opts.onSuccess === 'function') {
                            opts.onSuccess(response.data);
                        }
                    } else {
                        self.showNotice('Error saving settings: ' + (response.data || 'Unknown error'), 'error');
                    }
                },
                error: function() {
                    self.showNotice('Network error. Please try again.', 'error');
                },
                complete: function() {
                    if ($btn) {
                        $btn.prop('disabled', false);

                        if (opts.restoreButtonContent) {
                            $btn.html(originalHtml);
                        }
                    }

                    if (typeof opts.onComplete === 'function') {
                        opts.onComplete();
                    }
                }
            });
        },

        /**
         * Save settings
         */
        saveSettings: function() {
            var self = this;
            var $btn = $('.dmp-save-btn');
            var settings = this.collectFormData();

            this.saveOptionsFragment(settings, {
                button: $btn,
                successMessage: 'Settings saved successfully!',
                onSuccess: function() {
                    self.hasUnsavedChanges = false;
                }
            });
        },

        /**
         * Reset settings
         */
        resetSettings: function() {
            var self = this;

            $.ajax({
                url: dmpAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'dmp_reset_settings',
                    nonce: dmpAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.showNotice('Settings reset to defaults.', 'success');
                        location.reload();
                    } else {
                        self.showNotice('Error resetting settings.', 'error');
                    }
                }
            });
        },

        /**
         * Send test email
         */
        sendTestEmail: function() {
            var self = this;
            var $btn = $('.dmp-test-email-btn');
            var originalText = $btn.text();

            $btn.prop('disabled', true).text('Sending...');

            $.ajax({
                url: dmpAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'dmp_send_test_email',
                    nonce: dmpAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        self.showNotice('Test email sent successfully!', 'success');
                    } else {
                        self.showNotice('Failed to send test email.', 'error');
                    }
                },
                error: function() {
                    self.showNotice('Network error.', 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        },

        /**
         * Update custom preview
         */
        updateCustomPreview: function() {
            var bg = $('#color_background').val();
            var surface = $('#color_surface').val();
            var text = $('#color_text').val();
            var accent = $('#color_accent').val();

            $('#dmp-custom-preview').css('background', bg);
            $('#dmp-custom-preview').html(
                '<div style="background: ' + surface + '; padding: 12px; border-radius: 6px;">' +
                '<div style="color: ' + text + '; font-size: 14px; margin-bottom: 8px;">Preview Text</div>' +
                '<div style="background: ' + accent + '; height: 4px; border-radius: 2px; width: 60%;"></div>' +
                '</div>'
            );
        },

        /**
         * Show notice
         */
        showNotice: function(message, type) {
            var $notice = $('<div class="dmp-notice dmp-notice-' + type + '">' + message + '</div>');

            $('.dmp-admin-header').after($notice);

            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 4000);
        },

        /**
         * Mark as having unsaved changes
         */
        markUnsaved: function() {
            this.hasUnsavedChanges = true;
        },

        /**
         * Has unsaved changes flag
         */
        hasUnsavedChanges: false
    };

    // Initialize on document ready
    $(document).ready(function() {
        DmpAdmin.init();

        // Check for hash in URL to switch to tab
        if (window.location.hash) {
            var tab = window.location.hash.substring(1);
            if ($('.dmp-tab[data-tab="' + tab + '"]').length) {
                DmpAdmin.switchTab(tab);
            }
        }
    });

    // Add spinning animation
    $('<style>.dashicons.spin { animation: dmp-spin 1s linear infinite; } @keyframes dmp-spin { 100% { transform: rotate(360deg); } }</style>').appendTo('head');

    // Expose helper for other admin pages
    window.DmpAdminApp = DmpAdmin;

})(jQuery);
