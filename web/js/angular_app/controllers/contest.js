
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
   
    
    var url = '?r=contest/list_json';
    $http({
        method: 'GET',
        url: url
    }).then(function success(response) {
        $scope.contestArray = response.data;
    });
    
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
          $scope.contestArray = response.data;
          $scope.sorting = sorting;
          $scope.type = type;
       }, function error(response) {
           
       });
    }
    
    $scope.submitChangeForm = function(id) {
        showContestChangeForm(id);
    }
    
    $scope.testClick = function() {
        $scope.test = '2';
    }
    
    
    
});


