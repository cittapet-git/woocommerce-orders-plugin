/**
 * Admin JavaScript for My Real-Time WooCommerce Orders Plugin
 */

jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize admin functionality
    initAdminFeatures();
    
    function initAdminFeatures() {
        // Add copy functionality for shortcodes
        addCopyShortcodeFeature();
        
        // Add collapsible sections
        addCollapsibleSections();
        
        // Add real-time status updates
        addRealTimeStatusUpdates();
        
        // Add export functionality
        addExportFunctionality();
    }
    
    function addCopyShortcodeFeature() {
        $('.copy-shortcode').on('click', function(e) {
            e.preventDefault();
            
            const shortcode = $(this).data('shortcode');
            const textArea = document.createElement('textarea');
            textArea.value = shortcode;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            // Show success message
            showAdminNotice('Shortcode copiado al portapapeles!', 'success');
        });
    }
    
    function addCollapsibleSections() {
        $('.collapsible-section h3').on('click', function() {
            const content = $(this).next('.collapsible-content');
            const icon = $(this).find('.toggle-icon');
            
            content.slideToggle(300);
            
            if (icon.hasClass('dashicons-arrow-down')) {
                icon.removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
            } else {
                icon.removeClass('dashicons-arrow-up').addClass('dashicons-arrow-down');
            }
        });
    }
    
    function addRealTimeStatusUpdates() {
        // Update status every 30 seconds
        setInterval(function() {
            updateAdminStatus();
        }, 30000);
    }
    
    function updateAdminStatus() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'my_orders_admin_status',
                nonce: my_orders_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    updateStatusCards(response.data);
                }
            }
        });
    }
    
    function updateStatusCards(data) {
        if (data.total_orders !== undefined) {
            $('.total-orders .status-number').text(data.total_orders);
        }
        if (data.pending_orders !== undefined) {
            $('.pending-orders .status-number').text(data.pending_orders);
        }
        if (data.completed_orders !== undefined) {
            $('.completed-orders .status-number').text(data.completed_orders);
        }
    }
    
    function addExportFunctionality() {
        $('.export-orders').on('click', function(e) {
            e.preventDefault();
            
            const format = $(this).data('format');
            exportOrders(format);
        });
    }
    
    function exportOrders(format) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'my_orders_export',
                format: format,
                nonce: my_orders_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Create download link
                    const link = document.createElement('a');
                    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(response.data.content);
                    link.download = 'orders-export.' + format;
                    link.click();
                    
                    showAdminNotice('Exportación completada!', 'success');
                } else {
                    showAdminNotice('Error en la exportación: ' + response.data, 'error');
                }
            },
            error: function() {
                showAdminNotice('Error en la exportación', 'error');
            }
        });
    }
    
    function showAdminNotice(message, type) {
        const noticeClass = 'my-orders-admin-' + (type === 'success' ? 'notice' : type === 'warning' ? 'warning' : 'error');
        const notice = $('<div class="' + noticeClass + '">' + message + '</div>');
        
        $('.wrap').prepend(notice);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Add keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+Shift+E for export
        if (e.ctrlKey && e.shiftKey && e.keyCode === 69) {
            e.preventDefault();
            exportOrders('csv');
        }
        
        // Ctrl+Shift+R for refresh
        if (e.ctrlKey && e.shiftKey && e.keyCode === 82) {
            e.preventDefault();
            updateAdminStatus();
        }
    });
    
    // Add tooltips
    $('[data-tooltip]').tooltip({
        position: { my: 'left+5 center', at: 'right center' }
    });
    
    // Add confirmation dialogs
    $('.confirm-action').on('click', function(e) {
        if (!confirm($(this).data('confirm-message') || '¿Estás seguro?')) {
            e.preventDefault();
        }
    });
}); 