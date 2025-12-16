<?php
/**
 * Dashboard Widget Class
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * DMP_Dashboard_Widget Class
 */
class DMP_Dashboard_Widget {

    /**
     * Instance
     */
    private static $instance = null;

    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action('wp_dashboard_setup', array($this, 'add_dashboard_widget'));
    }

    /**
     * Add dashboard widget
     */
    public function add_dashboard_widget() {
        $options = get_option('dmp_options', array());

        if (empty($options['analytics_enabled'])) {
            return;
        }

        wp_add_dashboard_widget(
            'dmp_analytics_widget',
            __('Dark Mode Analytics', 'dark-mode-pro'),
            array($this, 'render_widget'),
            null,
            null,
            'normal',
            'high'
        );
    }

    /**
     * Render widget
     */
    public function render_widget() {
        $analytics = new DMP_Analytics();
        $data = $analytics->get_dashboard_data(7);
        $summary = $data['summary'];
        ?>
        <div class="dmp-dashboard-widget">
            <div class="dmp-widget-stats">
                <div class="dmp-widget-stat">
                    <span class="dmp-stat-value"><?php echo number_format($summary['total_toggles']); ?></span>
                    <span class="dmp-stat-label"><?php esc_html_e('Toggles (7 days)', 'dark-mode-pro'); ?></span>
                </div>
                <div class="dmp-widget-stat">
                    <span class="dmp-stat-value"><?php echo number_format($summary['unique_users']); ?></span>
                    <span class="dmp-stat-label"><?php esc_html_e('Unique Users', 'dark-mode-pro'); ?></span>
                </div>
                <div class="dmp-widget-stat">
                    <span class="dmp-stat-value"><?php echo $summary['dark_preference_percentage']; ?>%</span>
                    <span class="dmp-stat-label"><?php esc_html_e('Prefer Dark', 'dark-mode-pro'); ?></span>
                </div>
            </div>

            <div class="dmp-widget-chart">
                <canvas id="dmp-widget-chart" height="150"></canvas>
            </div>

            <p class="dmp-widget-footer">
                <a href="<?php echo esc_url(admin_url('admin.php?page=dark-mode-pro-analytics')); ?>">
                    <?php esc_html_e('View Full Analytics', 'dark-mode-pro'); ?> &rarr;
                </a>
            </p>
        </div>

        <style>
            .dmp-dashboard-widget {
                margin: -12px;
                padding: 15px;
            }
            .dmp-widget-stats {
                display: flex;
                gap: 15px;
                margin-bottom: 20px;
            }
            .dmp-widget-stat {
                flex: 1;
                text-align: center;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
            }
            .dmp-stat-value {
                display: block;
                font-size: 24px;
                font-weight: bold;
                color: #1a1a2e;
            }
            .dmp-stat-label {
                display: block;
                font-size: 12px;
                color: #666;
                margin-top: 5px;
            }
            .dmp-widget-chart {
                margin: 15px 0;
            }
            .dmp-widget-footer {
                text-align: right;
                margin: 0;
                padding-top: 10px;
                border-top: 1px solid #eee;
            }
        </style>

        <script>
        jQuery(document).ready(function($) {
            if (typeof Chart === 'undefined') {
                // Load Chart.js if not available
                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
                script.onload = initChart;
                document.head.appendChild(script);
            } else {
                initChart();
            }

            function initChart() {
                var ctx = document.getElementById('dmp-widget-chart');
                if (!ctx) return;

                var trends = <?php echo wp_json_encode($data['daily_trends']); ?>;
                var labels = Object.keys(trends);
                var darkData = labels.map(function(d) { return trends[d].dark || 0; });
                var lightData = labels.map(function(d) { return trends[d].light || 0; });

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels.map(function(d) {
                            return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        }),
                        datasets: [
                            {
                                label: '<?php esc_attr_e('To Dark', 'dark-mode-pro'); ?>',
                                data: darkData,
                                borderColor: '#1a1a2e',
                                backgroundColor: 'rgba(26, 26, 46, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: '<?php esc_attr_e('To Light', 'dark-mode-pro'); ?>',
                                data: lightData,
                                borderColor: '#e94560',
                                backgroundColor: 'rgba(233, 69, 96, 0.1)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 12, padding: 10 }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }
        });
        </script>
        <?php
    }
}

// Initialize
DMP_Dashboard_Widget::get_instance();
