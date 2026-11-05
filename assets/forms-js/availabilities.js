/**
 * Doctor Availabilities page.
 *
 * One modal (#availability-modal) is used for both "Add" and "Edit":
 *   - Add  -> the form is cleared and posts action "create_availability"
 *   - Edit -> the form is filled from the server and posts "update_availability"
 *
 * showAlert() is defined in the admin footer layout.
 */
$(document).ready(function () {
    var $modal = $('#availability-modal');
    var $form = $('#availability-form');
    var endpoint = $form.attr('action');

    // ---- Add: open the modal with an empty form ----
    $('#add-availability-btn').on('click', function () {
        openForm('create_availability', 'Add Availability');
        $modal.modal('show');
    });

    // ---- Edit: load the row from the server, then open the modal ----
    $('.edit-availability-btn').on('click', function () {
        var id = $(this).data('id');

        $.getJSON(endpoint, { action: 'get_availability', id: id }, function (response) {
            if (!response.success) {
                alert(response.message);
                return;
            }
            openForm('update_availability', 'Update Availability');

            $('#availability_id').val(response.data.id);
            $('#availability_day').val(response.data.day);
            $('#availability_from').val(response.data.session_from.substring(0, 5)); // HH:MM
            $('#availability_to').val(response.data.session_to.substring(0, 5));
            $('#availability_doctor').val(response.data.doctor_id);
            $('#availability_is_active').val(response.data.is_active);
            $modal.modal('show');
        });
    });

    // ---- Save (create or update) ----
    $('#save-availability').on('click', function () {
        var form = $form[0];
        if (!form.reportValidity()) return;

        $.ajax({
            url: endpoint,
            type: 'POST',
            data: new FormData(form),
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                showAlert(response.message, response.success ? 'primary' : 'danger', 'availability-alert');
                if (response.success) {
                    setTimeout(function () { location.reload(); }, 800);
                }
            },
            error: function () {
                showAlert('Something went wrong..!', 'danger', 'availability-alert');
            }
        });
    });

    // ---- Delete: ask first, then call the server ----
    $('.delete-availability-btn').on('click', function () {
        $('#delete_availability_id').val($(this).data('id'));
        $('#delete-alert-container').empty();
        $('#availability-delete-modal').modal('show');
    });

    $('#confirm-delete-availability').on('click', function () {
        var $button = $(this).prop('disabled', true).text('Deleting...');

        $.post(endpoint, { action: 'delete_availability', id: $('#delete_availability_id').val() }, function (response) {
            if (response.success) {
                location.reload();
            } else {
                showAlert(response.message, 'danger', 'delete-alert-container');
                $button.prop('disabled', false).text('Delete');
            }
        }, 'json').fail(function () {
            showAlert('Something went wrong..!', 'danger', 'delete-alert-container');
            $button.prop('disabled', false).text('Delete');
        });
    });

    /**
     * Prepares the shared modal: clears the form, sets the action and the title.
     */
    function openForm(action, title) {
        $form[0].reset();
        $('#availability_id').val('');
        $('#availability-alert').empty();
        $('#availability_action').val(action);
        $('#availability-modal-title').text(title);
    }
});
