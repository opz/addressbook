/**
 * Contact controllers
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */
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
  .controller('contactlistCtrl', ['$scope', '$http', function($scope, $http) {
    $scope.addContactVisibility = false;
    $scope.showAddContact = function() { $scope.addContactVisibility = true; };
    $scope.hideAddContact = function() { $scope.addContactVisibility = false; };

    $scope.contact = {};
    $scope.addcontact = function () {
      $http.post('/contact', $scope.contact)
        .then(function(response) {
          $scope.contacts.unshift(angular.copy($scope.contact));
          $scope.contact = {};
        });
    };

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
        //if there are no contacts, make add contact form visible by default
        if ($scope.contacts.length === 0) $scope.addContactVisibility = true;
      });
  }]);
})();
