
jQuery(document).ready(function ($) {
    'use strict';

    // Only run if the container with the ID 'mrtop-container' exists on the page.
    if ($('#mrtop-container').length === 0) {
        return;
    }

    /**
     * Function to refresh the orders via AJAX.
     */
    function refreshOrders() {
        // Check if the mrtop_ajax object is available
        if (typeof mrtop_ajax === 'undefined') {
            console.error('AJAX object is not available.');
            return;
        }

        $.ajax({
            url: mrtop_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_latest_orders',
                nonce: mrtop_ajax.nonce,
                // In a real scenario, you would have filters to get these values.
                // For this simplified version, we keep them static.
                limit: 10,
                status: ''
            },
            success: function (response) {
                if (response.success) {
                    // Update the table body with the new HTML
                    $('#mrtop-orders-table tbody').html(response.data.html);
                } else {
                    // Optionally handle the error case
                    console.error('Failed to refresh orders.');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error: ' + textStatus, errorThrown);
            }
        });
    }

    // Set an interval to call the refreshOrders function every 5000 milliseconds (5 seconds).
    setInterval(refreshOrders, 5000);
});
