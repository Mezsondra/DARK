<?php
/**
 * Analytics Class
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * DMP_Analytics Class
 */
class DMP_Analytics {

    /**
     * Table name
     */
    private $table_name;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'dmp_analytics';
    }

    /**
     * Track event
     */
    public function track_event($event_type, $event_data = array()) {
        global $wpdb;

        $user_id = get_current_user_id();
        $session_id = $this->get_session_id();
        $page_url = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '';
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '';
        $ip_hash = $this->hash_ip();

        $result = $wpdb->insert(
            $this->table_name,
            array(
                'event_type' => sanitize_text_field($event_type),
                'event_data' => wp_json_encode($event_data),
                'user_id' => $user_id,
                'session_id' => $session_id,
                'page_url' => $page_url,
                'user_agent' => $user_agent,
                'ip_hash' => $ip_hash,
                'created_at' => current_time('mysql'),
            ),
            array('%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
        );

        return $result !== false;
    }

    /**
     * Get session ID
     */
    private function get_session_id() {
        if (!isset($_COOKIE['dmp_session_id'])) {
            return wp_generate_uuid4();
        }
        return sanitize_text_field($_COOKIE['dmp_session_id']);
    }

    /**
     * Hash IP for privacy
     */
    private function hash_ip() {
        $ip = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }

        // Hash with daily salt for privacy
        return hash('sha256', $ip . date('Y-m-d'));
    }

    /**
     * Get dashboard data
     */
    public function get_dashboard_data($days = 30) {
        global $wpdb;

        $start_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return array(
            'summary' => $this->get_summary($start_date),
            'toggle_stats' => $this->get_toggle_stats($start_date),
            'mode_preferences' => $this->get_mode_preferences($start_date),
            'time_distribution' => $this->get_time_distribution($start_date),
            'device_breakdown' => $this->get_device_breakdown($start_date),
            'page_stats' => $this->get_page_stats($start_date),
            'daily_trends' => $this->get_daily_trends($start_date),
        );
    }

    /**
     * Get summary stats
     */
    private function get_summary($start_date) {
        global $wpdb;

        // Total toggles
        $total_toggles = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name}
            WHERE event_type = 'toggle' AND created_at >= %s",
            $start_date
        ));

        // Unique users
        $unique_users = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT ip_hash) FROM {$this->table_name}
            WHERE created_at >= %s",
            $start_date
        ));

        // Dark mode preference percentage
        $dark_preference = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name}
            WHERE event_type = 'mode_preference'
            AND JSON_EXTRACT(event_data, '$.mode') = 'dark'
            AND created_at >= %s",
            $start_date
        ));

        $total_preferences = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name}
            WHERE event_type = 'mode_preference' AND created_at >= %s",
            $start_date
        ));

        $dark_percentage = $total_preferences > 0
            ? round(($dark_preference / $total_preferences) * 100, 1)
            : 0;

        // Average time in dark mode
        $avg_dark_time = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(JSON_EXTRACT(event_data, '$.duration')) FROM {$this->table_name}
            WHERE event_type = 'time_spent'
            AND JSON_EXTRACT(event_data, '$.mode') = 'dark'
            AND created_at >= %s",
            $start_date
        ));

        return array(
            'total_toggles' => intval($total_toggles),
            'unique_users' => intval($unique_users),
            'dark_preference_percentage' => $dark_percentage,
            'avg_dark_time_seconds' => round(floatval($avg_dark_time), 0),
        );
    }

    /**
     * Get toggle statistics
     */
    private function get_toggle_stats($start_date) {
        global $wpdb;

        // To dark toggles
        $to_dark = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name}
            WHERE event_type = 'toggle'
            AND JSON_EXTRACT(event_data, '$.to_mode') = 'dark'
            AND created_at >= %s",
            $start_date
        ));

        // To light toggles
        $to_light = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name}
            WHERE event_type = 'toggle'
            AND JSON_EXTRACT(event_data, '$.to_mode') = 'light'
            AND created_at >= %s",
            $start_date
        ));

        return array(
            'to_dark' => intval($to_dark),
            'to_light' => intval($to_light),
            'total' => intval($to_dark) + intval($to_light),
        );
    }

    /**
     * Get mode preferences
     */
    private function get_mode_preferences($start_date) {
        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT
                JSON_EXTRACT(event_data, '$.source') as source,
                JSON_EXTRACT(event_data, '$.mode') as mode,
                COUNT(*) as count
            FROM {$this->table_name}
            WHERE event_type = 'mode_preference' AND created_at >= %s
            GROUP BY source, mode",
            $start_date
        ));

        $preferences = array(
            'system' => array('dark' => 0, 'light' => 0),
            'manual' => array('dark' => 0, 'light' => 0),
            'scheduled' => array('dark' => 0, 'light' => 0),
        );

        foreach ($results as $row) {
            $source = trim($row->source, '"');
            $mode = trim($row->mode, '"');
            if (isset($preferences[$source][$mode])) {
                $preferences[$source][$mode] = intval($row->count);
            }
        }

        return $preferences;
    }

    /**
     * Get time distribution
     */
    private function get_time_distribution($start_date) {
        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT
                HOUR(created_at) as hour,
                JSON_EXTRACT(event_data, '$.to_mode') as mode,
                COUNT(*) as count
            FROM {$this->table_name}
            WHERE event_type = 'toggle' AND created_at >= %s
            GROUP BY hour, mode
            ORDER BY hour",
            $start_date
        ));

        $distribution = array();
        for ($i = 0; $i < 24; $i++) {
            $distribution[$i] = array('dark' => 0, 'light' => 0);
        }

        foreach ($results as $row) {
            $hour = intval($row->hour);
            $mode = trim($row->mode, '"');
            if (isset($distribution[$hour][$mode])) {
                $distribution[$hour][$mode] = intval($row->count);
            }
        }

        return $distribution;
    }

    /**
     * Get device breakdown
     */
    private function get_device_breakdown($start_date) {
        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT user_agent, COUNT(*) as count
            FROM {$this->table_name}
            WHERE created_at >= %s
            GROUP BY user_agent",
            $start_date
        ));

        $devices = array(
            'desktop' => 0,
            'mobile' => 0,
            'tablet' => 0,
            'other' => 0,
        );

        foreach ($results as $row) {
            $ua = strtolower($row->user_agent);
            $count = intval($row->count);

            if (strpos($ua, 'mobile') !== false || strpos($ua, 'android') !== false) {
                if (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false) {
                    $devices['tablet'] += $count;
                } else {
                    $devices['mobile'] += $count;
                }
            } elseif (strpos($ua, 'tablet') !== false || strpos($ua, 'ipad') !== false) {
                $devices['tablet'] += $count;
            } elseif (strpos($ua, 'windows') !== false || strpos($ua, 'macintosh') !== false || strpos($ua, 'linux') !== false) {
                $devices['desktop'] += $count;
            } else {
                $devices['other'] += $count;
            }
        }

        return $devices;
    }

    /**
     * Get page statistics
     */
    private function get_page_stats($start_date) {
        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT page_url, COUNT(*) as toggle_count
            FROM {$this->table_name}
            WHERE event_type = 'toggle' AND created_at >= %s AND page_url != ''
            GROUP BY page_url
            ORDER BY toggle_count DESC
            LIMIT 10",
            $start_date
        ));

        $pages = array();
        foreach ($results as $row) {
            $pages[] = array(
                'url' => $row->page_url,
                'toggles' => intval($row->toggle_count),
            );
        }

        return $pages;
    }

    /**
     * Get daily trends
     */
    private function get_daily_trends($start_date) {
        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT
                DATE(created_at) as date,
                JSON_EXTRACT(event_data, '$.to_mode') as mode,
                COUNT(*) as count
            FROM {$this->table_name}
            WHERE event_type = 'toggle' AND created_at >= %s
            GROUP BY date, mode
            ORDER BY date",
            $start_date
        ));

        $trends = array();

        foreach ($results as $row) {
            $date = $row->date;
            $mode = trim($row->mode, '"');

            if (!isset($trends[$date])) {
                $trends[$date] = array('dark' => 0, 'light' => 0);
            }

            $trends[$date][$mode] = intval($row->count);
        }

        return $trends;
    }

    /**
     * Send email report
     */
    public function send_email_report() {
        $options = get_option('dmp_options', array());

        if (empty($options['email_reports_enabled'])) {
            return false;
        }

        $recipients = $options['email_report_recipients'] ?? get_option('admin_email');
        if (empty($recipients)) {
            return false;
        }

        $frequency = $options['email_report_frequency'] ?? 'weekly';
        $days = $frequency === 'daily' ? 1 : ($frequency === 'monthly' ? 30 : 7);

        $data = $this->get_dashboard_data($days);
        $period = $frequency === 'daily' ? __('Daily', 'dark-mode-pro')
                : ($frequency === 'monthly' ? __('Monthly', 'dark-mode-pro') : __('Weekly', 'dark-mode-pro'));

        $subject = sprintf(
            __('[%s] Dark Mode Pro %s Analytics Report', 'dark-mode-pro'),
            get_bloginfo('name'),
            $period
        );

        $message = $this->generate_email_html($data, $period, $days);

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
        );

        return wp_mail($recipients, $subject, $message, $headers);
    }

    /**
     * Generate email HTML
     */
    private function generate_email_html($data, $period, $days) {
        $summary = $data['summary'];
        $toggle_stats = $data['toggle_stats'];

        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: #fff; padding: 30px; border-radius: 10px 10px 0 0; }
                .header h1 { margin: 0; font-size: 24px; }
                .header p { margin: 10px 0 0; opacity: 0.8; }
                .content { background: #fff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; }
                .stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin: 20px 0; }
                .stat-card { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; }
                .stat-value { font-size: 32px; font-weight: bold; color: #1a1a2e; }
                .stat-label { font-size: 14px; color: #666; margin-top: 5px; }
                .section { margin: 30px 0; }
                .section h2 { font-size: 18px; color: #1a1a2e; border-bottom: 2px solid #e94560; padding-bottom: 10px; }
                .footer { background: #f8f9fa; padding: 20px; border-radius: 0 0 10px 10px; text-align: center; font-size: 12px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0; }
                th { background: #f8f9fa; font-weight: 600; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1><?php esc_html_e('Dark Mode Pro Analytics', 'dark-mode-pro'); ?></h1>
                    <p><?php printf(esc_html__('%s Report - Last %d days', 'dark-mode-pro'), $period, $days); ?></p>
                </div>

                <div class="content">
                    <div class="stat-grid">
                        <div class="stat-card">
                            <div class="stat-value"><?php echo number_format($summary['total_toggles']); ?></div>
                            <div class="stat-label"><?php esc_html_e('Total Toggles', 'dark-mode-pro'); ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo number_format($summary['unique_users']); ?></div>
                            <div class="stat-label"><?php esc_html_e('Unique Users', 'dark-mode-pro'); ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $summary['dark_preference_percentage']; ?>%</div>
                            <div class="stat-label"><?php esc_html_e('Prefer Dark Mode', 'dark-mode-pro'); ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo gmdate('i:s', $summary['avg_dark_time_seconds']); ?></div>
                            <div class="stat-label"><?php esc_html_e('Avg. Dark Mode Time', 'dark-mode-pro'); ?></div>
                        </div>
                    </div>

                    <div class="section">
                        <h2><?php esc_html_e('Toggle Breakdown', 'dark-mode-pro'); ?></h2>
                        <table>
                            <tr>
                                <th><?php esc_html_e('Direction', 'dark-mode-pro'); ?></th>
                                <th><?php esc_html_e('Count', 'dark-mode-pro'); ?></th>
                                <th><?php esc_html_e('Percentage', 'dark-mode-pro'); ?></th>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('To Dark Mode', 'dark-mode-pro'); ?></td>
                                <td><?php echo number_format($toggle_stats['to_dark']); ?></td>
                                <td><?php echo $toggle_stats['total'] > 0 ? round(($toggle_stats['to_dark'] / $toggle_stats['total']) * 100, 1) : 0; ?>%</td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('To Light Mode', 'dark-mode-pro'); ?></td>
                                <td><?php echo number_format($toggle_stats['to_light']); ?></td>
                                <td><?php echo $toggle_stats['total'] > 0 ? round(($toggle_stats['to_light'] / $toggle_stats['total']) * 100, 1) : 0; ?>%</td>
                            </tr>
                        </table>
                    </div>

                    <?php if (!empty($data['page_stats'])): ?>
                    <div class="section">
                        <h2><?php esc_html_e('Top Pages', 'dark-mode-pro'); ?></h2>
                        <table>
                            <tr>
                                <th><?php esc_html_e('Page', 'dark-mode-pro'); ?></th>
                                <th><?php esc_html_e('Toggles', 'dark-mode-pro'); ?></th>
                            </tr>
                            <?php foreach (array_slice($data['page_stats'], 0, 5) as $page): ?>
                            <tr>
                                <td><?php echo esc_html($page['url']); ?></td>
                                <td><?php echo number_format($page['toggles']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="footer">
                    <p><?php printf(
                        esc_html__('This report was generated by Dark Mode Pro for %s', 'dark-mode-pro'),
                        get_bloginfo('name')
                    ); ?></p>
                    <p><?php esc_html_e('To change your report settings, visit the Dark Mode Pro settings page in your WordPress admin.', 'dark-mode-pro'); ?></p>
                </div>
            </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Export data as CSV
     */
    public function export_csv($days = 30) {
        global $wpdb;

        $start_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE created_at >= %s ORDER BY created_at DESC",
            $start_date
        ), ARRAY_A);

        if (empty($results)) {
            return '';
        }

        $csv = array();
        $csv[] = array_keys($results[0]);

        foreach ($results as $row) {
            $csv[] = array_values($row);
        }

        $output = fopen('php://temp', 'r+');
        foreach ($csv as $line) {
            fputcsv($output, $line);
        }
        rewind($output);
        $csv_string = stream_get_contents($output);
        fclose($output);

        return $csv_string;
    }

    /**
     * Clean old data
     */
    public function clean_old_data($days = 90) {
        global $wpdb;

        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $wpdb->query($wpdb->prepare(
            "DELETE FROM {$this->table_name} WHERE created_at < %s",
            $cutoff_date
        ));
    }
}
