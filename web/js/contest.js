
$(document).ready(function() {
    $('#addFormModal').on('hidden.bs.modal', function(e) {
        $('#addFormModal .alert-danger').hide(); 
    });
    $('#changeFormModal').on('hidden.bs.modal', function(e) {
        $('#changeFormModal .alert-danger').hide(); 
    });
    
});



/**
 * показать форму изменения по щелчку на элементе списка
 * @param {type} contestId
 * @returns {undefined}
 */
function showContestChangeForm(contestId) {
    var url = '?r=contest/get_change_form&contest_id=' + contestId;
    $.ajax({
        url: url,
        success: function(response) {
            $('#changeFormModal .modal-body').html(response);
            $('#changeFormModal').modal('show');
        }
    });
}



