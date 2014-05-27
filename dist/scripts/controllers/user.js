/**
 * User controllers
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */
(function() {
  'use strict';

  angular.module('addressbookApp')
  .controller('userCtrl', ['$scope', '$rootScope', '$http', '$location',
    function($scope, $rootScope, $http, $location) {
      $scope.user = {};
      $scope.confirmpassword = '';

      //create new user from model
      $scope.signup = function() {
        $http.post('/user', $scope.user)
          .then(function(response) {
            $location.path('/login');
          });
      };

      //login with user model
      $scope.login = function() {
        $http.get('/user', { params: {
          email: $scope.user.email,
          password: $scope.user.password
        } })
          .then(function(response) {
            if (response.data.hasOwnProperty('id')) {
              $rootScope.auth = response.data;
              $location.path('/contactlist');
            }
          });
      };
    }]);
})();
