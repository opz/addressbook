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
    $scope.addcontact = function () {
      $http.post('/contact', $scope.contact)
        .then(function(response) {
          console.log(response);
        });
    };
  }])
  .controller('contactlistCtrl', ['$scope', '$http', function($scope, $http) {
    $scope.removecontact = function(i) {
      var id = $scope.contacts[i].id;
      $http.delete('/contact/' + id)
        .then(function() {
          $scope.contacts.splice(i, 1);
        });
    };

    $http.get('/contacts')
      .then(function(response) {
        $scope.contacts = response.data;
      });
  }]);
})();
