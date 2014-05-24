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
  }])
  .controller('addcontactCtrl', ['$scope', '$http', function($scope, $http) {
    $scope.$watch('contact', function() {
      console.log($scope.contact);
    }, true);

    $scope.addcontact = function () {
      $http.post('/contact', { 'contact': $scope.contact})
        .then(function(response) {
          console.log(response);
        });
    };
  }]);
})();
