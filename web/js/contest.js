
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
    
    $('body').on('change', '#changeContestForm .contestFileCheckbox', function() {
        var fileOnServerExist = $('#fileOnServerExist').val();
        var fileInput = $(this).siblings('.contestFileInput');
        var fileLink = $(this).siblings('.contestFileLinkContainer');
        var elem;
        if (fileOnServerExist) {
            elem = fileLink;
        } else {
            elem = fileInput;
        }
        if ($(this).prop('checked')) {
            elem.show();
        } else {
            elem.hide();
        }
    });
    
});

/**
 * click по ссылке удаления файла
 * @returns {Boolean}
 */
function clickFileDeleteLink() {
    var link = $(this);
    var form = $('#changeContestForm');
    var linkContainer = form.find('.contestFileLinkContainer');
    var fileInput = form.find('.contestFileInput');
    var fileExistHiddenInput = form.find('#fileOnServerExist');
    fileExistHiddenInput.val('');
    fileInput.show();
    linkContainer.remove();
    form.find('input[name=report_deleted]').val('1');
    return false;
}

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

