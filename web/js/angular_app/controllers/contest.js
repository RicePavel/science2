
myApp.controller('contestController', function($scope, $http) {
    
    /*
    $scope.showChangeForm = function($id) {
        var url = '?r=contest/get_one_json&contest_id=' + $id;
        $http({
            method: 'GET',
            url: url
        }).then(function success(response) {
            var data = response.data;
  
            for (var key in data) {
                var elem = $('#changeContestForm').find('[name="Contest[' + key + ']"]');
                var value = data[key];
                if (elem.length > 0 && value !== null) {
                    elem.val(value);
                }
            }
            $('#changeFormModal').modal('show');
        });
    }
    */
   
    $scope.showAddForm = function() {
       $('#addFormModalNew').modal('show');
    }
   
    updateContestTable();
   
    $scope.submitAddForm = function($event) {
       var form = $($event.target);
       var formData = new FormData();
       for (var key in $scope.addContestModel) {
           var value = $scope.addContestModel[key];
           if (value !== null) {
               if (key === 'report_exist') {
                   value = (value === true ? '1' : '0');
               }
               formData.append('Contest[' + key + ']', value);
           }
       }
       var locationIdInput = form.find('.locationIdHiddenInput');
       formData.append('Contest[location_id]', locationIdInput.val());
       formData.append('Contest[teacher_id]', form.find('.teacherIdSelect').val());
       formData.append('Contest[audience_id]', form.find('.audienceIdSelect').val());
       var fileElem = form.find('[name=report]')[0];
       if (fileElem && fileElem.files.length) {
           formData.append('report', fileElem.files[0]);
       }
       $.ajax({
            url: '?r=contest/add',
            data: formData,
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                var obj = response;
                if (obj.ok) {
                    $('#addFormModalNew').modal('hide');
                    $scope.addContestModel = {};
                    updateContestTableWithSorting();
                } else {
                   alert(obj.error); 
                }
            }
       });
    }
    
    $scope.sortTable = function(sorting) {
       var type = 'ASC';
       if ($scope.sorting === sorting) {
           if ($scope.type === 'ASC') {
               type = 'DESC';
           }
       }
       var url = '?r=contest/list_json&sorting=' + sorting + '&sorting_type=' + type; 
       $http({
          method: 'GET',
          url: url
       }).then(function success(response) {
          $scope.contestArray = $.parseJSON(response.data);
          $scope.sorting = sorting;
          $scope.type = type;
       }, function error(response) {
           
       });
    }
    
    $scope.deleteContest = function(contestId) {
        if (confirm('подтвердите удаление')) {
            url = '?r=contest/delete&contest_id=' + contestId;
            $http({
                method: 'GET',
                url: url
            }).then(function success(response) {
                var data = response.data;
                if (data.ok) {
                    updateContestTableWithSorting();
                } else {
                    alert(data.error);
                }
            }, function error(response) {

            });
        }
    }
    
    $scope.showChangeForm = function(id) {
        var form = $('#changeContestForm');
        var url = '?r=contest/get_one_json_full&contest_id=' + id;
        $http({
            method: 'GET',
            url: url
        }).then(function success(response) {
            $scope.contestForChange = {};
            $scope.contestForChangeExtra = {};
            $scope.teachers = response.data.teachers;
            $scope.audiences = response.data.audiences;
            $scope.locations = response.data.locations;
            var fileUrl = response.data.fileUrl;
            var model = response.data.contest;
            for (var key in model) {
                if (isNumeric(model[key])) {
                    model[key] = String(model[key]);
                }
            }
            if (model.report_exist === '1') {
                model.report_exist = true;
            } else {
                model.report_exist = false;
            }
            for (var key in model) {
                $scope.contestForChange[key] = model[key];   
            }
            var locationName = '';
            for (var key in $scope.locations) {
                var l = $scope.locations[key];
                if (String(l.location_id) === String(model.location_id)) {
                    locationName = l.name;
                }
            }
            form.find('.locationIdHiddenInput').val(model.location_id);
            $scope.contestForChangeExtra.locationName = locationName;
            $scope.contestForChangeExtra.fileUrl = fileUrl;
            $scope.contestForChangeExtra.reportDeleted = false;
            $('#changeFormModalNew').modal('show');
        }, function error(response) {
            
        });
    }
    
    $scope.deleteReportInChangeModel = function($event) {
        var link = $($event.target);
        link.closest('.contestFileLinkContainer').remove();
        $scope.contestForChangeExtra.reportDeleted = true;
    }
    
    $scope.submitChangeForm = function($event) {
       var form = $($event.target);
       var formData = new FormData();
       for (var key in $scope.contestForChange) {
           var value = $scope.contestForChange[key];
           if (value !== null) {
               if (key === 'report_exist') {
                   value = (value === true ? '1' : '0');
               }
               formData.append('Contest[' + key + ']', value);
           }
       }
       formData.append('Contest[location_id]', form.find('.locationIdHiddenInput').val());
       if ($scope.contestForChangeExtra.reportDeleted === true) {
            formData.append('report_deleted', $scope.contestForChangeExtra.reportDeleted );
       }
       var fileElem = form.find('[name=report]')[0];
       if (fileElem && fileElem.files.length) {
           formData.append('report', fileElem.files[0]);
       }
       $.ajax({
            url: '?r=contest/change',
            data: formData,
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                var obj = response;
                if (obj.ok) {
                    $('#changeFormModalNew').modal('hide');
                    $scope.contestForChange = {};
                    $scope.contestForChangeExtra = {};
                    updateContestTableWithSorting();
                } else {
                   alert(obj.error); 
                }
            }
       });
    }
    
    $scope.testClick = function() {
        $scope.test = '2';
    }
    
    function updateContestTable() {
        var url = '?r=contest/list_json';
        $http({
            method: 'GET',
            url: url
        }).then(function success(response) {
            $scope.contestArray = $.parseJSON(response.data);;
        });
    }
    
    function updateContestTableWithSorting() {
        var url = '';
        if ($scope.sorting !== undefined) {
            url = '?r=contest/list_json&sorting=' + $scope.sorting + '&sorting_type=' + $scope.type;
        } else {
            url = '?r=contest/list_json';
        }
        $http({
           method: 'GET',
           url: url
        }).then(function success(response) {
          $scope.contestArray = $.parseJSON(response.data);
        }, function error(response) {
           
        });
    }
    
});

$(document).ready(function(){
    /*
    $('#addFormModalNew').on('hidden.bs.modal', function(e) {
        var form = $('#addFormModalNew').find('form');
        //form.find('input[type=text]').not('.locationTextInput').val('');
        form.find('input[type=file]').val('');
    })
    */
});

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}


