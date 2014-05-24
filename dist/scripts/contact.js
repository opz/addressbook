(function() {
  'use strict';

  angular.module('addressbookApp')
  .controller('contactCtrl', ['$scope', '$http', function($scope, $http) {
    $http.get('/contact/1')
      .then(function(response) {
        $scope.first = response.data.first;
        $scope.last = response.data.last;
        $scope.email = response.data.email;
      });
  }]);
})();
