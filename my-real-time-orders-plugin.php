<?php
/**
 * Plugin Name: My Real-Time WooCommerce Orders (Direct DB)
 * Description: Displays WooCommerce orders from the database using a shortcode, following a direct query approach.
 * Version: 3.0
 * Author: Gemini (Refactored)
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly.

final class My_Real_Time_Orders_Plugin
{
    /**
     * Initializes the plugin by adding WordPress hooks.
     */
    public static function init()
    {
        add_shortcode('my_woocommerce_orders', [self::class, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_scripts']);
        add_action('wp_ajax_get_latest_orders', [self::class, 'ajax_handler']);
    }

    /**
     * Renders the shortcode's initial HTML structure.
     */
    public static function render_shortcode($atts)
    {
        $atts = shortcode_atts(['limit' => 10, 'status' => ''], $atts, 'my_woocommerce_orders');

        // Enqueue the script and pass data to it.
        // It's important to enqueue here to ensure it only loads when the shortcode is present.
        wp_enqueue_script('mrtop-js');
        wp_localize_script('mrtop-js', 'mrtop_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('mrtop_nonce')
        ]);

        ob_start();
        ?>
        <div id="mrtop-container">
            <h2>Órdenes Recientes</h2>
            <table id="mrtop-orders-table" class="shop_table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Cliente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo self::render_table_rows($atts['limit'], $atts['status']); ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Fetches orders directly from the database, like the reference code.
     */
    public static function get_orders($limit = 10, $status = '')
    {
        global $wpdb;
        $orders_table = $wpdb->prefix . 'wc_orders';

        // Base query
        $sql = "SELECT id, date_created_gmt, status, total_amount, billing_first_name, billing_last_name FROM {$orders_table}";

        $params = [];
        // Append status filter if provided
        if (!empty($status)) {
            $sql .= " WHERE status = %s";
            $params[] = 'wc-' . sanitize_text_field($status);
        }

        // Add ordering and limit
        $sql .= " ORDER BY date_created_gmt DESC LIMIT %d";
        $params[] = intval($limit);

        // Prepare and execute the query
        return $wpdb->get_results($wpdb->prepare($sql, ...$params));
    }

    /**
     * Renders the HTML for the table rows from the order data.
     */
    public static function render_table_rows($limit, $status)
    {
        if (!current_user_can('view_woocommerce_reports')) {
            return '<tr><td colspan="5">No tienes permiso para ver esta información.</td></tr>';
        }

        $orders = self::get_orders($limit, $status);

        if (empty($orders)) {
            return '<tr><td colspan="5">No se encontraron órdenes.</td></tr>';
        }

        $html = '';
        foreach ($orders as $order) {
            $status_name = wc_get_order_status_name($order->status);
            $html .= sprintf(
                '<tr><td>#%1$s</td><td>%2$s</td><td>%3$s</td><td>%4$s</td><td>%5$s %6$s</td></tr>',
                esc_html($order->id),
                esc_html(date('Y-m-d H:i', strtotime($order->date_created_gmt))),
                esc_html($status_name),
                wc_price($order->total_amount),
                esc_html($order->billing_first_name),
                esc_html($order->billing_last_name)
            );
        }
        return $html;
    }

    /**
     * Handles the AJAX request to refresh the orders.
     */
    public static function ajax_handler()
    {
        check_ajax_referer('mrtop_nonce', 'nonce');
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        wp_send_json_success(['html' => self::render_table_rows($limit, $status)]);
    }

    /**
     * Enqueues the necessary JavaScript file.
     */
    public static function enqueue_scripts()
    {
        // We register it here, and the shortcode will enqueue and localize it if used.
        wp_register_script('mrtop-js', plugins_url('assets/js/real-time-orders.js', __FILE__), ['jquery'], '3.0', true);
    }
}

My_Real_Time_Orders_Plugin::init();
