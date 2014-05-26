(function() {
  'use strict';

  angular.module('addressbookApp')
  .controller('userCtrl', ['$scope', '$http', function($scope, $http) {
    $scope.user = {};
    $scope.confirmpassword = '';

    $scope.signup = function() {
      if ($scope.signupform.$valid) {
        $http.post('/user', $scope.user)
          .then(function(response) {
          });
      } else {
        console.log($scope.signupform.$error);
      }
    };
  }]);
})();
