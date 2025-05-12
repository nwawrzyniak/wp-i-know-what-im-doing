jQuery(document).ready(function($) {
    // Handle delete button clicks
    $(document).on('click', '.wpikwiad-delete-option', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var optionName = $button.data('option');
        
        // Confirm deletion
        if (!confirm(wpikwiadData.confirmMessage)) {
            return;
        }
        
        // Disable button and show loading state
        $button.prop('disabled', true).text('Deleting...');
        
        // Send AJAX request
        $.ajax({
            url: wpikwiadData.ajaxurl,
            type: 'POST',
            data: {
                action: 'wpikwiad_delete_option',
                nonce: wpikwiadData.nonce,
                option_name: optionName
            },
            success: function(response) {
                if (response.success) {
                    // Remove the entire row
                    $button.closest('tr').fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    // Show error and reset button
                    alert(response.data || 'Failed to delete option');
                    $button.prop('disabled', false).text('Delete');
                }
            },
            error: function() {
                // Show error and reset button
                alert('An error occurred while deleting the option');
                $button.prop('disabled', false).text('Delete');
            }
        });
    });
}); 
