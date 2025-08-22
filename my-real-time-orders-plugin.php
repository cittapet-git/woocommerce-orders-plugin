<?php
/**
 * Plugin Name: My Real-Time WooCommerce Orders
 * Description: A plugin to display WooCommerce orders in real-time using a shortcode.
 * Version: 1.2
 * Author: Gemini
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Add a check for the WooCommerce plugin
add_action( 'admin_init', 'my_real_time_orders_check_woocommerce' );
function my_real_time_orders_check_woocommerce() {
    if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        add_action( 'admin_notices', 'my_real_time_orders_woocommerce_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) );
    }
}

function my_real_time_orders_woocommerce_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e( 'The "My Real-Time WooCommerce Orders" plugin requires WooCommerce to be installed and active. The plugin has been deactivated.', 'my-real-time-orders' ); ?></p>
    </div>
    <?php
}

// Register the shortcode [my_woocommerce_orders]
add_shortcode( 'my_woocommerce_orders', 'my_woocommerce_orders_shortcode' );

function my_woocommerce_orders_shortcode( $atts ) {
    // Parse shortcode attributes
    $atts = shortcode_atts( array(
        'limit' => 10,
        'status' => '',
        'show_filters' => 'true',
        'auto_refresh' => 'true',
        'debug' => 'false'
    ), $atts, 'my_woocommerce_orders' );
    
    // Enqueue our script only when the shortcode is used
    wp_enqueue_script( 'my-real-time-orders-js' );
    wp_localize_script( 'my-real-time-orders-js', 'my_ajax_object', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'my_orders_nonce' ),
        'auto_refresh' => $atts['auto_refresh'] === 'true'
    ) );

    // Start output buffering
    ob_start();

    // Debug information
    if ( $atts['debug'] === 'true' ) {
        echo '<div style="background: #f0f0f0; padding: 15px; margin: 10px 0; border: 1px solid #ccc;">';
        echo '<h4>🔍 Debug Information:</h4>';
        echo '<p><strong>WooCommerce Version:</strong> ' . WC()->version . '</p>';
        echo '<p><strong>HPOS Enabled:</strong> ' . ( wc_get_container()->get( \Automattic\WooCommerce\Utilities\OrderUtil::class )->custom_orders_table_usage_is_enabled() ? 'Yes' : 'No' ) . '</p>';
        echo '<p><strong>Orders Count:</strong> ' . wc_get_orders( array( 'limit' => -1, 'return' => 'ids' ) ) . '</p>';
        echo '<p><strong>Orders Table:</strong> ' . ( wc_get_container()->get( \Automattic\WooCommerce\Utilities\OrderUtil::class )->custom_orders_table_usage_is_enabled() ? 'wp_wc_orders' : 'wp_posts' ) . '</p>';
        echo '</div>';
    }

    // The HTML structure for our orders table
    echo '<div id="my-orders-container">';
    echo '<h2>Órdenes Recientes de WooCommerce</h2>';
    
    // Add filters if enabled
    if ( $atts['show_filters'] === 'true' ) {
        echo '<div class="orders-filters" style="margin-bottom: 20px;">';
        echo '<label for="status-filter">Filtrar por estado: </label>';
        echo '<select id="status-filter" style="margin-right: 15px;">';
        echo '<option value="">Todos los estados</option>';
        echo '<option value="pending">Pendiente</option>';
        echo '<option value="processing">Procesando</option>';
        echo '<option value="completed">Completado</option>';
        echo '<option value="cancelled">Cancelado</option>';
        echo '<option value="refunded">Reembolsado</option>';
        echo '</select>';
        
        echo '<label for="limit-filter">Mostrar: </label>';
        echo '<select id="limit-filter" style="margin-right: 15px;">';
        echo '<option value="5">5 órdenes</option>';
        echo '<option value="10" selected>10 órdenes</option>';
        echo '<option value="20">20 órdenes</option>';
        echo '<option value="50">50 órdenes</option>';
        echo '</select>';
        
        echo '<button id="refresh-orders" style="padding: 5px 15px; background: #0073aa; color: white; border: none; border-radius: 3px; cursor: pointer;">Actualizar Ahora</button>';
        echo '</div>';
    }
    
    echo '<div id="orders-loading" style="text-align: center; padding: 20px; display: none;">Cargando órdenes...</div>';
    echo '<table id="my-orders-table" class="shop_table shop_table_responsive my_account_orders" style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
    echo '<thead>';
    echo '<tr style="background-color: #f8f9fa;">';
    echo '<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number" style="padding: 12px; border: 1px solid #ddd; text-align: left;"><span class="nobr">Número de Orden</span></th>';
    echo '<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date" style="padding: 12px; border: 1px solid #ddd; text-align: left;"><span class="nobr">Fecha</span></th>';
    echo '<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status" style="padding: 12px; border: 1px solid #ddd; text-align: left;"><span class="nobr">Estado</span></th>';
    echo '<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total" style="padding: 12px; border: 1px solid #ddd; text-align: left;"><span class="nobr">Total</span></th>';
    echo '<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-customer" style="padding: 12px; border: 1px solid #ddd; text-align: left;"><span class="nobr">Cliente</span></th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    // Get and display the initial orders
    display_orders_rows( $atts['limit'], $atts['status'] );

    echo '</tbody>';
    echo '</table>';
    
    // Add auto-refresh indicator
    if ( $atts['auto_refresh'] === 'true' ) {
        echo '<div id="auto-refresh-info" style="text-align: center; margin-top: 15px; font-size: 12px; color: #666;">';
        echo '🔄 Actualización automática cada 5 segundos';
        echo '</div>';
    }
    
    echo '</div>';

    // Return the buffered content
    return ob_get_clean();
}

// Function to display the table rows for orders
function display_orders_rows( $limit = 10, $status = '' ) {
    // Check if WooCommerce is active
    if ( ! function_exists( 'wc_get_orders' ) ) {
        echo '<tr><td colspan="5" style="text-align: center; padding: 20px; color: red;">Error: WooCommerce no está activo.</td></tr>';
        return;
    }

    // Debug: Check current user capabilities
    $current_user = wp_get_current_user();
    echo '<tr><td colspan="5" style="text-align: center; padding: 20px; color: blue; background: #e6f3ff;">';
    echo '<strong>Debug Info:</strong><br>';
    echo 'Usuario actual: ' . $current_user->user_login . '<br>';
    echo 'Puede ver órdenes: ' . ( current_user_can( 'read_shop_orders' ) ? 'Sí' : 'No' ) . '<br>';
    echo 'Puede ver pedidos: ' . ( current_user_can( 'read_shop_orders' ) ? 'Sí' : 'No' ) . '<br>';
    echo 'Es admin: ' . ( current_user_can( 'manage_options' ) ? 'Sí' : 'No' ) . '<br>';
    echo '</td></tr>';

    // Try alternative methods to get orders
    echo '<tr><td colspan="5" style="text-align: center; padding: 20px; color: purple; background: #f0e6ff;">';
    echo '<strong>Métodos Alternativos:</strong><br>';
    
    // Method 1: Direct database query
    global $wpdb;
    $orders_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_status != 'trash'" );
    echo 'Método 1 (posts): ' . $orders_count . ' órdenes<br>';
    
    // Method 2: Check if HPOS table exists
    $hpos_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_orders" );
    if ( $hpos_count !== null ) {
        echo 'Método 2 (HPOS): ' . $hpos_count . ' órdenes<br>';
    } else {
        echo 'Método 2 (HPOS): Tabla no existe<br>';
    }
    
    // Method 3: Get posts directly
    $posts = get_posts( array(
        'post_type' => 'shop_order',
        'post_status' => array( 'publish', 'pending', 'processing', 'completed', 'cancelled', 'refunded', 'on-hold' ),
        'numberposts' => 5,
        'orderby' => 'date',
        'order' => 'DESC'
    ) );
    echo 'Método 3 (get_posts): ' . count( $posts ) . ' órdenes<br>';
    
    if ( ! empty( $posts ) ) {
        echo 'Primera orden ID: ' . $posts[0]->ID . ' - Estado: ' . $posts[0]->post_status . '<br>';
    }
    
    echo '</td></tr>';

    // SOLUCIÓN PRINCIPAL: Usar directamente get_posts que SÍ funciona
    echo '<tr><td colspan="5" style="text-align: center; padding: 20px; color: #006600; background: #e6ffe6;">';
    echo '<strong>🚀 SOLUCIÓN: Usando método directo que SÍ funciona</strong><br>';
    
    try {
        // Construir argumentos para get_posts
        $post_args = array(
            'post_type' => 'shop_order',
            'post_status' => array( 'publish', 'pending', 'processing', 'completed', 'cancelled', 'refunded', 'on-hold' ),
            'numberposts' => intval( $limit ),
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        // Filtrar por estado si se especifica
        if ( ! empty( $status ) ) {
            // Convertir estados de WooCommerce a post_status
            $status_mapping = array(
                'pending' => 'pending',
                'processing' => 'processing', 
                'completed' => 'completed',
                'cancelled' => 'cancelled',
                'refunded' => 'refunded',
                'on-hold' => 'on-hold'
            );
            
            if ( isset( $status_mapping[ $status ] ) ) {
                $post_args['post_status'] = array( $status_mapping[ $status ] );
            }
        }
        
        $orders = get_posts( $post_args );
        
        if ( ! empty( $orders ) ) {
            echo '✅ Encontradas ' . count( $orders ) . ' órdenes usando método directo<br>';
            
            foreach ( $orders as $order ) {
                // Obtener metadatos de la orden
                $order_total = get_post_meta( $order->ID, '_order_total', true );
                $billing_first_name = get_post_meta( $order->ID, '_billing_first_name', true );
                $billing_last_name = get_post_meta( $order->ID, '_billing_last_name', true );
                $order_status = get_post_meta( $order->ID, '_order_status', true );
                
                // Si no hay _order_status, usar post_status
                if ( empty( $order_status ) ) {
                    $order_status = $order->post_status;
                }
                
                // Limpiar el estado (quitar prefijo wc- si existe)
                $order_status = str_replace( 'wc-', '', $order_status );
                
                echo '<tr class="woocommerce-orders-table__row direct-method" style="border-bottom: 1px solid #eee;">';
                
                // Número de orden
                echo '<td class="woocommerce-orders-table__cell" style="padding: 12px; border: 1px solid #ddd;">';
                echo '<strong>#' . esc_html( $order->ID ) . '</strong>';
                echo '</td>';
                
                // Fecha
                echo '<td class="woocommerce-orders-table__cell" style="padding: 12px; border: 1px solid #ddd;">';
                echo esc_html( get_the_date( 'Y-m-d H:i:s', $order->ID ) );
                echo '</td>';
                
                // Estado con colores
                echo '<td class="woocommerce-orders-table__cell" style="padding: 12px; border: 1px solid #ddd;">';
                $status_color = get_status_color( $order_status );
                echo '<span style="background-color: ' . $status_color . '; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px;">' . esc_html( $order_status ) . '</span>';
                echo '</td>';
                
                // Total
                echo '<td class="woocommerce-orders-table__cell" style="padding: 12px; border: 1px solid #ddd;">';
                echo '<strong>' . esc_html( $order_total ) . '</strong>';
                echo '</td>';
                
                // Cliente
                echo '<td class="woocommerce-orders-table__cell" style="padding: 12px; border: 1px solid #ddd;">';
                echo esc_html( $billing_first_name . ' ' . $billing_last_name );
                echo '</td>';
                
                echo '</tr>';
            }
        } else {
            echo '❌ No se encontraron órdenes con ningún método<br>';
        }
        
    } catch ( Exception $e ) {
        echo '❌ Error en método directo: ' . esc_html( $e->getMessage() ) . '<br>';
    }
    
    echo '</td></tr>';
    
    // Function to get status colors
function get_status_color( $status ) {
    $colors = array(
        'pending' => '#ffba00',
        'processing' => '#73a724',
        'completed' => '#7ad03a',
        'cancelled' => '#a00',
        'refunded' => '#e2401c',
        'failed' => '#a00',
        'on-hold' => '#c6c1c1'
    );
    
    return isset( $colors[ $status ] ) ? $colors[ $status ] : '#c6c1c1';
}

// Register our JavaScript file
add_action( 'wp_enqueue_scripts', 'my_real_time_orders_scripts' );
function my_real_time_orders_scripts() {
    wp_register_script( 'my-real-time-orders-js', plugins_url( 'real-time-orders.js', __FILE__ ), array( 'jquery' ), '1.2', true );
}

// AJAX handler for getting the latest orders
add_action( 'wp_ajax_get_latest_orders', 'get_latest_orders_ajax_handler' );
add_action( 'wp_ajax_nopriv_get_latest_orders', 'get_latest_orders_ajax_handler' );

function get_latest_orders_ajax_handler() {
    // Verify nonce for security
    if ( ! wp_verify_nonce( $_POST['nonce'], 'my_orders_nonce' ) ) {
        wp_send_json_error( 'Invalid nonce' );
    }
    
    $limit = isset( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 10;
    $status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
    
    ob_start();
    display_orders_rows( $limit, $status );
    $html = ob_get_clean();

    wp_send_json_success( array( 'html' => $html ) );
}

// Add admin menu for plugin settings
add_action( 'admin_menu', 'my_real_time_orders_admin_menu' );
function my_real_time_orders_admin_menu() {
    add_options_page(
        'Configuración de Órdenes en Tiempo Real',
        'Órdenes en Tiempo Real',
        'manage_options',
        'my-real-time-orders',
        'my_real_time_orders_admin_page'
    );
}

function my_real_time_orders_admin_page() {
    ?>
    <div class="wrap">
        <h1>Configuración de Órdenes en Tiempo Real</h1>
        <p>Este plugin permite mostrar órdenes de WooCommerce en tiempo real usando el shortcode <code>[my_woocommerce_orders]</code></p>
        
        <h2>Estado del Sistema</h2>
        <div style="background: #f0f0f0; padding: 15px; border-radius: 5px;">
            <p><strong>WooCommerce Version:</strong> <?php echo WC()->version; ?></p>
            <p><strong>HPOS Enabled:</strong> <?php echo wc_get_container()->get( \Automattic\WooCommerce\Utilities\OrderUtil::class )->custom_orders_table_usage_is_enabled() ? 'Sí' : 'No'; ?></p>
            <p><strong>Total de Órdenes:</strong> <?php echo count( wc_get_orders( array( 'limit' => -1, 'return' => 'ids' ) ) ); ?></p>
            <p><strong>Tabla de Órdenes:</strong> <?php echo wc_get_container()->get( \Automattic\WooCommerce\Utilities\OrderUtil::class )->custom_orders_table_usage_is_enabled() ? 'wp_wc_orders' : 'wp_posts'; ?></p>
        </div>
        
        <h2>Uso del Shortcode</h2>
        <p>Puedes usar el shortcode con los siguientes parámetros:</p>
        <ul>
            <li><strong>limit</strong>: Número de órdenes a mostrar (por defecto: 10)</li>
            <li><strong>status</strong>: Filtrar por estado específico</li>
            <li><strong>show_filters</strong>: Mostrar filtros (true/false)</li>
            <li><strong>auto_refresh</strong>: Actualización automática (true/false)</li>
            <li><strong>debug</strong>: Mostrar información de debug (true/false)</li>
        </ul>
        
        <h3>Ejemplos:</h3>
        <ul>
            <li><code>[my_woocommerce_orders]</code> - Muestra 10 órdenes con configuración por defecto</li>
            <li><code>[my_woocommerce_orders limit="20" status="completed"]</code> - Muestra 20 órdenes completadas</li>
            <li><code>[my_woocommerce_orders debug="true"]</code> - Con información de debug</li>
        </ul>
        
        <h2>Base de Datos</h2>
        <p>El plugin utiliza la tabla de órdenes de WooCommerce para obtener la información.</p>
        
        <h2>Debug</h2>
        <p>Si tienes problemas, usa el parámetro <code>debug="true"</code> para ver información del sistema:</p>
        <code>[my_woocommerce_orders debug="true"]</code>
    </div>
    <?php
}
