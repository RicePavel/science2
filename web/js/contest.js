
$(document).ready(function() {
    $('#addFormModal').on('hidden.bs.modal', function(e) {
        $('#addFormModal .alert-danger').hide(); 
    });
    $('#changeFormModal').on('hidden.bs.modal', function(e) {
        $('#changeFormModal .alert-danger').hide(); 
    });
    $('.contestFileCheckbox').on('change', function() {
        var fileInput = $(this).siblings('.contestFileInput');
        if ($(this).prop('checked')) {
            fileInput.show();
        } else {
            fileInput.hide();
        }
    });
});
