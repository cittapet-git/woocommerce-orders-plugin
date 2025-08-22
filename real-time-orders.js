jQuery(document).ready(function($) {
    'use strict';
    
    // Variables globales
    let currentLimit = 10;
    let currentStatus = '';
    let autoRefreshInterval;
    
    // Function to update orders table
    function updateOrdersTable(showLoading = true) {
        if (showLoading) {
            $('#orders-loading').show();
            $('#my-orders-table').hide();
        }
        
        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'get_latest_orders',
                nonce: my_ajax_object.nonce || '',
                limit: currentLimit,
                status: currentStatus
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    $('#my-orders-table tbody').html(response.data.html);
                    showUpdateStatus('Órdenes actualizadas correctamente');
                } else {
                    showUpdateStatus('Error al actualizar las órdenes');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error updating orders:', error);
                showUpdateStatus('Error de conexión');
            },
            complete: function() {
                $('#orders-loading').hide();
                $('#my-orders-table').show();
            }
        });
    }
    
    // Function to start auto-refresh
    function startAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        
        if (my_ajax_object.auto_refresh) {
            autoRefreshInterval = setInterval(function() {
                updateOrdersTable(false); // Don't show loading for auto-refresh
            }, 5000);
        }
    }
    
    // Function to stop auto-refresh
    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
    }
    
    // Event handlers for filters
    $('#status-filter').on('change', function() {
        currentStatus = $(this).val();
        updateOrdersTable();
    });
    
    $('#limit-filter').on('change', function() {
        currentLimit = parseInt($(this).val());
        updateOrdersTable();
    });
    
    // Manual refresh button
    $('#refresh-orders').on('click', function() {
        $(this).prop('disabled', true).text('Actualizando...');
        updateOrdersTable();
        
        setTimeout(function() {
            $('#refresh-orders').prop('disabled', false).text('Actualizar Ahora');
        }, 2000);
    });
    
    // Show update status
    function showUpdateStatus(message) {
        if (!$('#update-status').length) {
            $('#my-orders-container').append('<div id="update-status" style="text-align: center; margin-top: 10px; font-size: 12px; color: #666;"></div>');
        }
        
        $('#update-status').text(message).fadeIn().delay(3000).fadeOut();
    }
    
    // Initialize
    function init() {
        // Start auto-refresh if enabled
        startAutoRefresh();
        
        // Update when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateOrdersTable();
            }
        });
        
        // Add keyboard shortcuts
        $(document).keydown(function(e) {
            // Ctrl+R or Cmd+R to refresh
            if ((e.ctrlKey || e.metaKey) && e.keyCode === 82) {
                e.preventDefault();
                updateOrdersTable();
            }
        });
        
        // Add hover effects to table rows
        $('#my-orders-table').on('mouseenter', 'tr', function() {
            $(this).css('background-color', '#f8f9fa');
        }).on('mouseleave', 'tr', function() {
            $(this).css('background-color', '');
        });
        
        // Add click to expand order details (future feature)
        $('#my-orders-table').on('click', 'tr', function() {
            if ($(this).find('td').length > 0) {
                $(this).toggleClass('expanded');
            }
        });
    }
    
    // Start initialization when DOM is ready
    init();
    
    // Expose functions globally for debugging
    window.myOrdersPlugin = {
        updateOrders: updateOrdersTable,
        startAutoRefresh: startAutoRefresh,
        stopAutoRefresh: stopAutoRefresh,
        currentLimit: function() { return currentLimit; },
        currentStatus: function() { return currentStatus; }
    };
});