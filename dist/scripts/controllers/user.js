/**
 * User controllers
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */
(function() {
  'use strict';

  angular.module('addressbookApp')
  .controller('userCtrl', ['$scope', '$http', '$location', function($scope, $http, $location) {
    $scope.user = {};
    $scope.confirmpassword = '';

    $scope.signup = function() {
      $http.post('/user', $scope.user)
        .then(function(response) {
          $location.path('/login');
        });
    };
  }]);
})();
