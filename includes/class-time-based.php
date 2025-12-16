<?php
/**
 * Time-Based Activation Class
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * DMP_Time_Based Class
 */
class DMP_Time_Based {

    /**
     * Get time-based modes
     */
    public static function get_modes() {
        return array(
            'sunset' => array(
                'name' => __('Sunset/Sunrise', 'dark-mode-pro'),
                'description' => __('Automatically switch based on sunset and sunrise times for visitor location.', 'dark-mode-pro'),
            ),
            'schedule' => array(
                'name' => __('Custom Schedule', 'dark-mode-pro'),
                'description' => __('Set specific times for dark mode activation.', 'dark-mode-pro'),
            ),
            'system' => array(
                'name' => __('System Preference', 'dark-mode-pro'),
                'description' => __('Follow the visitor\'s system dark mode preference.', 'dark-mode-pro'),
            ),
        );
    }

    /**
     * Get sun times for coordinates
     */
    public static function get_sun_times($latitude = null, $longitude = null, $date = null) {
        if ($date === null) {
            $date = current_time('timestamp');
        }

        // Default coordinates (approximate center of US)
        if ($latitude === null) {
            $latitude = 39.8283;
        }
        if ($longitude === null) {
            $longitude = -98.5795;
        }

        $sun_info = date_sun_info($date, $latitude, $longitude);

        return array(
            'sunrise' => date('H:i', $sun_info['sunrise']),
            'sunset' => date('H:i', $sun_info['sunset']),
            'civil_twilight_begin' => date('H:i', $sun_info['civil_twilight_begin']),
            'civil_twilight_end' => date('H:i', $sun_info['civil_twilight_end']),
            'nautical_twilight_begin' => date('H:i', $sun_info['nautical_twilight_begin']),
            'nautical_twilight_end' => date('H:i', $sun_info['nautical_twilight_end']),
        );
    }

    /**
     * Check if dark mode should be active based on time
     */
    public static function should_be_dark($mode, $options) {
        switch ($mode) {
            case 'sunset':
                return self::check_sunset_mode($options);

            case 'schedule':
                return self::check_schedule_mode($options);

            case 'system':
                // System preference is handled client-side
                return null;

            default:
                return false;
        }
    }

    /**
     * Check sunset mode
     */
    private static function check_sunset_mode($options) {
        $sun_times = self::get_sun_times();
        $current_time = current_time('H:i');

        $sunrise = $sun_times['sunrise'];
        $sunset = $sun_times['sunset'];

        // Dark mode is active after sunset and before sunrise
        if ($current_time >= $sunset || $current_time < $sunrise) {
            return true;
        }

        return false;
    }

    /**
     * Check schedule mode
     */
    private static function check_schedule_mode($options) {
        $start = isset($options['schedule_start']) ? $options['schedule_start'] : '19:00';
        $end = isset($options['schedule_end']) ? $options['schedule_end'] : '07:00';
        $current_time = current_time('H:i');

        // Handle overnight schedules (e.g., 19:00 to 07:00)
        if ($start > $end) {
            // Dark mode is active from start to midnight, and from midnight to end
            if ($current_time >= $start || $current_time < $end) {
                return true;
            }
        } else {
            // Normal schedule (e.g., 09:00 to 17:00)
            if ($current_time >= $start && $current_time < $end) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get timezone options
     */
    public static function get_timezone_options() {
        return array(
            'visitor' => __('Visitor\'s Timezone', 'dark-mode-pro'),
            'site' => __('Site Timezone', 'dark-mode-pro'),
        );
    }

    /**
     * Generate JavaScript for client-side time checking
     */
    public static function get_client_script($options) {
        $mode = isset($options['time_based_mode']) ? $options['time_based_mode'] : 'sunset';

        ob_start();
        ?>
        (function() {
            const dmpTimeBasedMode = '<?php echo esc_js($mode); ?>';
            const dmpScheduleStart = '<?php echo esc_js($options['schedule_start'] ?? '19:00'); ?>';
            const dmpScheduleEnd = '<?php echo esc_js($options['schedule_end'] ?? '07:00'); ?>';
            const dmpUseVisitorTimezone = <?php echo ($options['use_visitor_timezone'] ?? true) ? 'true' : 'false'; ?>;

            function getSunTimes() {
                // This would ideally use geolocation API
                // For now, return approximate times
                return {
                    sunrise: '06:30',
                    sunset: '18:30'
                };
            }

            function getCurrentTime() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                return hours + ':' + minutes;
            }

            function timeToMinutes(time) {
                const [hours, minutes] = time.split(':').map(Number);
                return hours * 60 + minutes;
            }

            function shouldBeDark() {
                const currentTime = getCurrentTime();
                const currentMinutes = timeToMinutes(currentTime);

                if (dmpTimeBasedMode === 'system') {
                    return window.matchMedia('(prefers-color-scheme: dark)').matches;
                }

                if (dmpTimeBasedMode === 'sunset') {
                    const sunTimes = getSunTimes();
                    const sunriseMinutes = timeToMinutes(sunTimes.sunrise);
                    const sunsetMinutes = timeToMinutes(sunTimes.sunset);

                    return currentMinutes >= sunsetMinutes || currentMinutes < sunriseMinutes;
                }

                if (dmpTimeBasedMode === 'schedule') {
                    const startMinutes = timeToMinutes(dmpScheduleStart);
                    const endMinutes = timeToMinutes(dmpScheduleEnd);

                    if (startMinutes > endMinutes) {
                        return currentMinutes >= startMinutes || currentMinutes < endMinutes;
                    } else {
                        return currentMinutes >= startMinutes && currentMinutes < endMinutes;
                    }
                }

                return false;
            }

            window.dmpTimeBased = {
                shouldBeDark: shouldBeDark,
                getCurrentTime: getCurrentTime,
                getSunTimes: getSunTimes
            };
        })();
        <?php
        return ob_get_clean();
    }
}
