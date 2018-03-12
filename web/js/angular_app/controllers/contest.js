
myApp.controller('contestController', function($scope, $http) {
    
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
    
    $scope.testClick = function() {
        $scope.test = '2';
    }
    
    
    
});


