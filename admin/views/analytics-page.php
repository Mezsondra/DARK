<?php
/**
 * Analytics Page View
 *
 * @package Dark_Mode_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('dmp_options', dark_mode_pro()->get_default_options());
$analytics = new DMP_Analytics();
$data = $analytics->get_dashboard_data(30);
$summary = $data['summary'];
$toggle_stats = $data['toggle_stats'];
$mode_preferences = $data['mode_preferences'];
$device_breakdown = $data['device_breakdown'];
$page_stats = $data['page_stats'];
$daily_trends = $data['daily_trends'];
$time_distribution = $data['time_distribution'];
?>

<div class="wrap dmp-admin-wrap">
    <div class="dmp-admin-header">
        <div class="dmp-header-content">
            <h1>
                <span class="dashicons dashicons-chart-area"></span>
                <?php esc_html_e('Analytics Dashboard', 'dark-mode-pro'); ?>
            </h1>
            <p><?php esc_html_e('Track dark mode usage and user preferences.', 'dark-mode-pro'); ?></p>
        </div>
        <div class="dmp-header-actions">
            <select id="dmp-date-range" class="dmp-date-select">
                <option value="7"><?php esc_html_e('Last 7 days', 'dark-mode-pro'); ?></option>
                <option value="30" selected><?php esc_html_e('Last 30 days', 'dark-mode-pro'); ?></option>
                <option value="90"><?php esc_html_e('Last 90 days', 'dark-mode-pro'); ?></option>
            </select>
            <button type="button" class="button dmp-export-btn">
                <span class="dashicons dashicons-download"></span>
                <?php esc_html_e('Export CSV', 'dark-mode-pro'); ?>
            </button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=dark-mode-pro')); ?>" class="button">
                <span class="dashicons dashicons-arrow-left-alt"></span>
                <?php esc_html_e('Back to Settings', 'dark-mode-pro'); ?>
            </a>
        </div>
    </div>

    <?php if (empty($options['analytics_enabled'])): ?>
    <div class="dmp-notice dmp-notice-warning">
        <p>
            <strong><?php esc_html_e('Analytics is disabled.', 'dark-mode-pro'); ?></strong>
            <?php esc_html_e('Enable analytics in settings to start tracking usage data.', 'dark-mode-pro'); ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=dark-mode-pro#analytics')); ?>"><?php esc_html_e('Enable Analytics', 'dark-mode-pro'); ?></a>
        </p>
    </div>
    <?php endif; ?>

    <div class="dmp-admin-content">
        <!-- Summary Cards -->
        <div class="dmp-stats-grid">
            <div class="dmp-stat-card">
                <div class="dmp-stat-icon">
                    <span class="dashicons dashicons-controls-repeat"></span>
                </div>
                <div class="dmp-stat-content">
                    <span class="dmp-stat-value"><?php echo number_format($summary['total_toggles']); ?></span>
                    <span class="dmp-stat-label"><?php esc_html_e('Total Toggles', 'dark-mode-pro'); ?></span>
                </div>
            </div>

            <div class="dmp-stat-card">
                <div class="dmp-stat-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="dmp-stat-content">
                    <span class="dmp-stat-value"><?php echo number_format($summary['unique_users']); ?></span>
                    <span class="dmp-stat-label"><?php esc_html_e('Unique Users', 'dark-mode-pro'); ?></span>
                </div>
            </div>

            <div class="dmp-stat-card dmp-stat-highlight">
                <div class="dmp-stat-icon">
                    <span class="dashicons dashicons-moon"></span>
                </div>
                <div class="dmp-stat-content">
                    <span class="dmp-stat-value"><?php echo $summary['dark_preference_percentage']; ?>%</span>
                    <span class="dmp-stat-label"><?php esc_html_e('Prefer Dark Mode', 'dark-mode-pro'); ?></span>
                </div>
            </div>

            <div class="dmp-stat-card">
                <div class="dmp-stat-icon">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div class="dmp-stat-content">
                    <span class="dmp-stat-value"><?php echo gmdate('i:s', $summary['avg_dark_time_seconds']); ?></span>
                    <span class="dmp-stat-label"><?php esc_html_e('Avg. Dark Mode Time', 'dark-mode-pro'); ?></span>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="dmp-charts-row">
            <div class="dmp-card dmp-chart-card">
                <h2><?php esc_html_e('Daily Trends', 'dark-mode-pro'); ?></h2>
                <canvas id="dmp-trends-chart" height="300"></canvas>
            </div>

            <div class="dmp-card dmp-chart-card">
                <h2><?php esc_html_e('Toggle Distribution', 'dark-mode-pro'); ?></h2>
                <canvas id="dmp-toggle-chart" height="300"></canvas>
            </div>
        </div>

        <!-- Second Row -->
        <div class="dmp-charts-row">
            <div class="dmp-card dmp-chart-card">
                <h2><?php esc_html_e('Hourly Activity', 'dark-mode-pro'); ?></h2>
                <canvas id="dmp-hourly-chart" height="250"></canvas>
            </div>

            <div class="dmp-card dmp-chart-card">
                <h2><?php esc_html_e('Device Breakdown', 'dark-mode-pro'); ?></h2>
                <canvas id="dmp-device-chart" height="250"></canvas>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="dmp-tables-row">
            <div class="dmp-card">
                <h2><?php esc_html_e('Top Pages', 'dark-mode-pro'); ?></h2>
                <?php if (!empty($page_stats)): ?>
                <table class="dmp-data-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Page', 'dark-mode-pro'); ?></th>
                            <th><?php esc_html_e('Toggles', 'dark-mode-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($page_stats as $page): ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url($page['url']); ?>" target="_blank">
                                    <?php echo esc_html(wp_parse_url($page['url'], PHP_URL_PATH) ?: '/'); ?>
                                </a>
                            </td>
                            <td><?php echo number_format($page['toggles']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="dmp-no-data"><?php esc_html_e('No page data available yet.', 'dark-mode-pro'); ?></p>
                <?php endif; ?>
            </div>

            <div class="dmp-card">
                <h2><?php esc_html_e('Mode Preference Sources', 'dark-mode-pro'); ?></h2>
                <table class="dmp-data-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Source', 'dark-mode-pro'); ?></th>
                            <th><?php esc_html_e('Dark', 'dark-mode-pro'); ?></th>
                            <th><?php esc_html_e('Light', 'dark-mode-pro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php esc_html_e('System Preference', 'dark-mode-pro'); ?></td>
                            <td><?php echo number_format($mode_preferences['system']['dark']); ?></td>
                            <td><?php echo number_format($mode_preferences['system']['light']); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('Manual Toggle', 'dark-mode-pro'); ?></td>
                            <td><?php echo number_format($mode_preferences['manual']['dark']); ?></td>
                            <td><?php echo number_format($mode_preferences['manual']['light']); ?></td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e('Scheduled', 'dark-mode-pro'); ?></td>
                            <td><?php echo number_format($mode_preferences['scheduled']['dark']); ?></td>
                            <td><?php echo number_format($mode_preferences['scheduled']['light']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.dmp-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.dmp-stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.dmp-stat-highlight {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: #fff;
}

.dmp-stat-icon {
    width: 60px;
    height: 60px;
    background: #f0f0f5;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dmp-stat-highlight .dmp-stat-icon {
    background: rgba(255,255,255,0.1);
}

.dmp-stat-icon .dashicons {
    font-size: 28px;
    width: 28px;
    height: 28px;
    color: #1a1a2e;
}

.dmp-stat-highlight .dmp-stat-icon .dashicons {
    color: #fff;
}

.dmp-stat-value {
    display: block;
    font-size: 32px;
    font-weight: 700;
    line-height: 1;
}

.dmp-stat-label {
    display: block;
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.dmp-stat-highlight .dmp-stat-label {
    color: rgba(255,255,255,0.7);
}

.dmp-charts-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.dmp-chart-card {
    min-height: 350px;
}

.dmp-tables-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.dmp-data-table {
    width: 100%;
    border-collapse: collapse;
}

.dmp-data-table th,
.dmp-data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.dmp-data-table th {
    font-weight: 600;
    background: #f8f9fa;
}

.dmp-data-table td a {
    color: #1a1a2e;
    text-decoration: none;
}

.dmp-data-table td a:hover {
    color: #e94560;
}

.dmp-no-data {
    color: #999;
    text-align: center;
    padding: 30px;
}

.dmp-date-select {
    padding: 6px 12px;
    border-radius: 4px;
}

.dmp-notice {
    background: #fff;
    border-left: 4px solid #dba617;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

@media (max-width: 1200px) {
    .dmp-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .dmp-charts-row,
    .dmp-tables-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Trends Chart
    var trendsCtx = document.getElementById('dmp-trends-chart');
    if (trendsCtx) {
        var trends = <?php echo wp_json_encode($daily_trends); ?>;
        var labels = Object.keys(trends);
        var darkData = labels.map(function(d) { return trends[d].dark || 0; });
        var lightData = labels.map(function(d) { return trends[d].light || 0; });

        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: labels.map(function(d) {
                    return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }),
                datasets: [
                    {
                        label: '<?php echo esc_js(__('To Dark Mode', 'dark-mode-pro')); ?>',
                        data: darkData,
                        borderColor: '#1a1a2e',
                        backgroundColor: 'rgba(26, 26, 46, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: '<?php echo esc_js(__('To Light Mode', 'dark-mode-pro')); ?>',
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
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Toggle Chart
    var toggleCtx = document.getElementById('dmp-toggle-chart');
    if (toggleCtx) {
        new Chart(toggleCtx, {
            type: 'doughnut',
            data: {
                labels: ['<?php echo esc_js(__('To Dark', 'dark-mode-pro')); ?>', '<?php echo esc_js(__('To Light', 'dark-mode-pro')); ?>'],
                datasets: [{
                    data: [<?php echo intval($toggle_stats['to_dark']); ?>, <?php echo intval($toggle_stats['to_light']); ?>],
                    backgroundColor: ['#1a1a2e', '#e94560']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Hourly Chart
    var hourlyCtx = document.getElementById('dmp-hourly-chart');
    if (hourlyCtx) {
        var hourlyData = <?php echo wp_json_encode($time_distribution); ?>;
        var hours = Array.from({length: 24}, (_, i) => i + ':00');
        var darkHourly = Object.values(hourlyData).map(h => h.dark || 0);
        var lightHourly = Object.values(hourlyData).map(h => h.light || 0);

        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: hours,
                datasets: [
                    {
                        label: '<?php echo esc_js(__('Dark', 'dark-mode-pro')); ?>',
                        data: darkHourly,
                        backgroundColor: '#1a1a2e'
                    },
                    {
                        label: '<?php echo esc_js(__('Light', 'dark-mode-pro')); ?>',
                        data: lightHourly,
                        backgroundColor: '#e94560'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    }

    // Device Chart
    var deviceCtx = document.getElementById('dmp-device-chart');
    if (deviceCtx) {
        var devices = <?php echo wp_json_encode($device_breakdown); ?>;
        new Chart(deviceCtx, {
            type: 'pie',
            data: {
                labels: ['Desktop', 'Mobile', 'Tablet', 'Other'],
                datasets: [{
                    data: [devices.desktop, devices.mobile, devices.tablet, devices.other],
                    backgroundColor: ['#1a1a2e', '#e94560', '#4da8da', '#a0a0a0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Export CSV
    $('.dmp-export-btn').on('click', function() {
        var days = $('#dmp-date-range').val();

        $.ajax({
            url: dmpAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'dmp_export_analytics',
                nonce: dmpAdmin.nonce,
                days: days
            },
            success: function(response) {
                if (response.success && response.data.csv) {
                    var blob = new Blob([response.data.csv], { type: 'text/csv' });
                    var url = window.URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = response.data.filename;
                    a.click();
                    window.URL.revokeObjectURL(url);
                }
            }
        });
    });
});
</script>
