
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
    
    /**
     * форма изменения contest
     */
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
    
    /**
     * форма добавления contest
     */
    var location = {};
    location.downButton = '.locationDownButton';
    location.removeButton = '.locationRemoveButton';
    location.list = '.locationList';
    location.listElement = '.locationListElement';
    location.textInput = '.locationTextInput';
    location.hiddenInput = '.locationIdHiddenInput';
    location.formGroup = '.locationFormGroup';
    $('body').on('click', location.downButton, function() {
        var form = $(this).closest('form');
        var ul = form.find(location.list);
        form.find(location.listElement).show();
        ul.toggle();
    });
    $('body').on('click', location.textInput, function() {
        $(this).closest('form').find(location.list).show();
    });
    $('body').on('input', location.textInput, function() {
        var inputText = $(this).val().toLowerCase();
        var form = $(this).closest('form');
        if (inputText === '') {
           form.find(location.listElement).show();
        } else {
           form.find(location.listElement).each(function(index, domElement) {
               var elementText = $(domElement).text().toLowerCase();
               if (elementText.indexOf(inputText) === -1) {
                   $(domElement).hide();
               } else {
                   $(domElement).show(); 
               }
           });
        }
    });
    $('body').on('click', location.removeButton, function() {
        var form = $(this).closest('form');
        form.find(location.textInput).val('');
        form.find(location.hiddenInput).val('');
        form.find(location.list).show();
        form.find(location.listElement).show();
    });
    $('body').on('click', location.listElement, function() {
        var name = $(this).html();
        var id = $(this).attr('data-id');
        var form = $(this).closest('form');
        form.find(location.textInput).val(name);
        form.find(location.hiddenInput).val(id);
        form.find(location.list).hide();
    });
    $(document).click(function(event){
        var target = event.target;
        var formGroup = $(target).closest(location.formGroup);
        if (formGroup.length > 0) {
            
        } else {
            $(location.list).hide();
        }
    });
    $('body').on('submit', '#addContestForm', function() {
        return checkLocation(this);
    });
    $('body').on('submit', '#changeContestForm', function() {
        return checkLocation(this);
    });
    function checkLocation(form) {
       var locationId = $(form).find(location.hiddenInput).val();
       if (locationId == '') {
           $(form).find(location.formGroup).addClass('has-error');
           alert('задайте корректное место проведения');
           return false;
       } 
    }
    
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

function submitTestFileForm(self) {
    var formData = new FormData();
    var fileInput = $(self).find('[name=myFile]');
    var fileElem = fileInput[0];
    var textInput = $(self).find('[name=myText]');
    formData.append('myText', textInput.val());
    if (fileElem.files.length) {
        formData.append('myFile', fileElem.files[0]);
    }
    $.ajax({
        url: '?r=contest/add_test',
        data: formData,
        type: 'POST',
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(reponse) {
            console.log('success response');
        }
    });
    return false;
}

