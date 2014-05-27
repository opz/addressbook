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
  .controller('contactlistCtrl', ['$scope', '$rootScope', '$http',
    function($scope, $rootScope, $http) {
      //opens and closes "add contact" form
      $scope.addContactVisibility = false;
      $scope.showAddContact = function() { $scope.addContactVisibility = true; };
      $scope.hideAddContact = function() { $scope.addContactVisibility = false; };

      //add a new contact from model
      $scope.contact = {};
      $scope.addcontact = function () {
        $http.post('/user/' + $rootScope.auth.id + '/contact', $scope.contact)
          .then(function(response) {
            $scope.contacts.unshift(angular.copy($scope.contact));
            $scope.contact = {};
          });
      };

      //remove contact at index
      $scope.removecontact = function(i) {
        var id = $scope.contacts[i].id;
        $http.delete('/user/' + $rootScope.auth.id + '/contact/' + id)
          .then(function() {
            $scope.contacts.splice(i, 1);
          });
      };

      //get list of contacts
      $http.get('/user/' + $rootScope.auth.id + '/contacts')
        .then(function(response) {
          $scope.contacts = response.data;
          //if there are no contacts, make add contact form visible by default
          if ($scope.contacts.length === 0) $scope.addContactVisibility = true;
        });
    }]);
})();
