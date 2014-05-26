(function() {
  'use strict';

  angular.module('addressbookApp')
  .controller('authCtrl', ['$scope', '$rootScope', '$http', function($scope, $rootScope, $http) {
    $scope.user = {};

    $scope.login = function(username, password) {
      $http.post('auth', $scope.user)
        .then(function(response) {

        });
    };
  }]);
})();
