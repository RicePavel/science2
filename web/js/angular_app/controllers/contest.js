
myApp.controller('contestController', function($scope, $http) {
      
    $scope.showAddForm = function() {
       $('#addFormModalNew').modal('show');
    }
   
    
   
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
                    updateContestTableWithParameters();
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
       $scope.sorting = sorting;
       $scope.type = type;
       updateContestTableWithParameters();
        /*
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
        */
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
                    updateContestTableWithParameters();
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
            var model = response.data.contest;
            var fileUrl = response.data.fileUrl;
            var teachers = response.data.teachers;
            var audiences = response.data.audiences;
            var locations = response.data.locations;
            $scope._loadDataIntoChangeForm(model, fileUrl, teachers, audiences, locations);
        }, function error(response) {
            
        });
    }
    
    $scope.deleteReportInChangeModel = function($event) {
        var link = $($event.target);
        link.closest('.contestFileLinkContainer').remove();
        $scope.contestForChangeExtra.reportDeleted = true;
    }
    
    $scope.submitChangeForm = function($event) {
       var formData = $scope._getDataFromChangeForm($event);
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
                    updateContestTableWithParameters();
                } else {
                   alert(obj.error); 
                }
            }
       });
    }
    
    $scope._updateAdditionalData = function() {
        $http({
            method: 'GET',
            url: '?r=contest/get_additional_data'
        }).then(function success(response) {
            var data = $.parseJSON(response.data);
            $scope.teachers = data.teachers;
            $scope.audiences = data.audiences;
            $scope.locations = data.locations;
        }, function error() {
            
        });
    }
    
    $scope._getDataFromChangeForm = function($event) {
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
       return formData;
    }
    
    $scope._loadDataIntoChangeForm = function(model, fileUrl, teachers, audiences, locations) {
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
        
        var locationName = '';
        for (var key in locations) {
            var l = locations[key];
            if (String(l.location_id) === String(model.location_id)) {
                locationName = l.name;
            }
        }
        
        $scope.teachers = teachers;
        $scope.audiences = audiences;
        $scope.locations = locations;
        $scope.contestForChange = {};
        $scope.contestForChangeExtra = {};
        for (var key in model) {
            $scope.contestForChange[key] = model[key];   
        }
        $scope.contestForChangeExtra.fileUrl = fileUrl;
        $scope.contestForChangeExtra.reportDeleted = false;
        $scope.contestForChangeExtra.locationName = locationName;
        
        var form = $('#changeContestForm');
        form.find('.locationIdHiddenInput').val(model.location_id);
       
        $('#changeFormModalNew').modal('show');
    }
    
    $scope.updateContestTable = function() {
        
    }
    
    $scope._getDataForUpdate = function() {
       var data = {};
       if ($scope.sorting !== undefined) {
            data.sorting = $scope.sorting;
            data.sorting_type = $scope.type;
       }
       var selectionData = $scope.selectionData;
       if (selectionData) {
           for (var key in selectionData) {
               var value = selectionData[key];
               
               if (value === true) {
                   value = '1';
               }
               if (value === false) {
                   continue;
               }
               
               if (value !== null && value !== undefined && value !== '') {
                   data['selection[' + key + ']'] = value;
               }
           }
       }
       data.paginationExist = $scope.paginationExist;
       data.pageNumber = $scope.pageNumber;
       return data;
    };
    
    $scope._getDataForUpdateInString = function() {
        var data = $scope._getDataForUpdate();
        var str = '';
        var idx = 0;
        for (var key in data) {
            str += '&' + key + '=' + data[key];
            idx++;
        }
        return str;
    }
    
    $scope.applySelection = function() {
        $scope.pageNumber = 1;
        updateContestTableWithParameters();
    }
    
    $scope.paginationExist = '1';
    $scope.pageNumber = 1;
    updateContestTableWithParameters();
    $scope._updateAdditionalData();
    
    $scope.changeInRating = function(contest) {
        var id = contest.contest_id;
        var url = '?r=contest/change_in_rating&contest_id=' + id;
        $http({
            method: 'GET',
            url: url
        }).then(function success(response) {
            var data = $.parseJSON(response.data);
            if (data.ok) {
                contest.in_rating = data.in_rating;
            } else {
                alert(data.error);
            }
        }, function error(response){
            
        });
    }
    
    $scope.nextPage = function() {
        if ($scope.pageNumbersArray !== undefined) {
            var maxNumber = $scope.pageNumbersArray[$scope.pageNumbersArray.length - 1];
            if ($scope.pageNumber !== undefined && $scope.pageNumber < maxNumber) {
                $scope.pageNumber++;
                updateContestTableWithParameters();
            }
        }
    }
    
    $scope.prevPage = function() {
        if ($scope.pageNumber !== undefined && $scope.pageNumber > 1) {
            $scope.pageNumber--;
            updateContestTableWithParameters();
        }
    }
    
    $scope.showPage = function(number) {
        $scope.pageNumber = number;
        updateContestTableWithParameters();
    }
    
    function updateContestTableWithParameters() {
       var url = '?r=contest/list_json';
       url += $scope._getDataForUpdateInString();
       $http({
           method: 'GET',
           url: url
       }).then(function success(response) {
           var data = $.parseJSON(response.data);
           $scope.contestArray = data.contestArray;
           var pageCount = data.pageCount;
           $scope.pageNumbersArray = [];
           for (var i = 1; i <= pageCount; i++) {
               $scope.pageNumbersArray.push(i);
           }
       }, function error(response) {
           
       });
    }
    
});

$(document).ready(function(){
    $('#addFormModalNew').on('hidden.bs.modal', function(e) {
        var form = $('#addFormModalNew').find('form');
        form.find('input[type=text]').not('.locationTextInput').val('');
        form.find('input[type=file]').val('');
    })
});

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}


