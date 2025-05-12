jQuery(document).ready(function($) {
    console.log('WP I Know What I\'m Doing: Script initialization');
    console.log('wpikwiadData available:', typeof wpikwiadData !== 'undefined');
    if (typeof wpikwiadData !== 'undefined') {
        console.log('wpikwiadData contents:', wpikwiadData);
    }

    var $modal = $('#wpikwiad-delete-modal');
    var $optionName = $modal.find('.wpikwiad-modal-option-name');
    var $confirmButton = $modal.find('.wpikwiad-modal-confirm');
    var $cancelButton = $modal.find('.wpikwiad-modal-cancel');
    var $closeButton = $modal.find('.wpikwiad-modal-close');
    var currentButton = null;

    // Function to show modal
    function showModal(optionName, $button) {
        console.log('Showing modal for option:', optionName);
        currentButton = $button;
        $optionName.text(optionName);
        $modal.fadeIn(200);
    }

    // Function to hide modal
    function hideModal() {
        console.log('Hiding modal');
        $modal.fadeOut(200);
        currentButton = null;
    }

    // Close modal when clicking the X, Cancel button, or outside the modal
    $closeButton.add($cancelButton).on('click', hideModal);
    $modal.on('click', function(e) {
        if ($(e.target).is($modal)) {
            hideModal();
        }
    });

    // Handle delete button clicks
    $(document).on('click', '.wpikwiad-delete-option', function(e) {
        e.preventDefault();
        var $button = $(this);
        var optionName = $button.data('option');
        console.log('Delete button clicked for option:', optionName);
        showModal(optionName, $button);
    });

    // Handle confirm button click
    $confirmButton.on('click', function() {
        if (!currentButton) {
            console.error('No current button set for deletion');
            return;
        }

        var $button = currentButton;
        var optionName = $button.data('option');
        console.log('Confirm button clicked for option:', optionName);
        
        // Disable button and show loading state
        $button.prop('disabled', true).text('Deleting...');
        hideModal();
        
        // Send AJAX request
        console.log('Sending delete request for option:', optionName);
        console.log('AJAX URL:', wpikwiadData.ajaxurl);
        console.log('Nonce:', wpikwiadData.nonce);
        
        $.ajax({
            url: wpikwiadData.ajaxurl,
            type: 'POST',
            data: {
                action: 'wpikwiad_delete_option',
                nonce: wpikwiadData.nonce,
                option_name: optionName
            },
            success: function(response) {
                console.log('Delete response:', response);
                if (response.success) {
                    console.log('Option deleted successfully, removing row');
                    // Remove the entire row
                    $button.closest('tr').fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    // Show error and reset button
                    console.error('Delete failed:', response.data);
                    alert(response.data || 'Failed to delete option');
                    $button.prop('disabled', false).text('Delete');
                }
            },
            error: function(xhr, status, error) {
                // Show error and reset button
                console.error('AJAX error:', {xhr: xhr, status: status, error: error});
                console.error('Response text:', xhr.responseText);
                alert('An error occurred while deleting the option');
                $button.prop('disabled', false).text('Delete');
            }
        });
    });
}); 
