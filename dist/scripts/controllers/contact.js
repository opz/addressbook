/**
 * Contact controllers
 *
 * @author Will Shahda <will.shahda@gmail.com>
 */
(function() {
  'use strict';

  angular.module('addressbookApp')
  .controller('contactlistCtrl', ['$scope', '$rootScope', '$http',
    function($scope, $rootScope, $http) {
      $scope.contacts = [];
      $scope.contactgroups = [];

      //opens and closes "add contact" form
      $scope.addContactVisibility = false;
      $scope.showAddContact = function() { $scope.addContactVisibility = true; };
      $scope.hideAddContact = function() { $scope.addContactVisibility = false; };

      //opens and closes "edit contact" form
      $scope.showEditContact = function(i) { $scope.contacts[i].editContactVisibility = true; };
      $scope.hideEditContact = function(i) { $scope.contacts[i].editContactVisibility = false; };

      //pull contact group list
      $http.get('/user/' + $rootScope.auth.id + '/contactgroups')
        .then(function(response) {
          $scope.contactgroups = response.data;
        });

      //add a new contact from model
      $scope.contact = { groups: [] };
      $scope.addcontact = function() {
        $http.post('/user/' + $rootScope.auth.id + '/contact', $scope.contact)
          .then(function(response) {
            $scope.contact.id = response.data.id;
            $scope.contacts.unshift(angular.copy($scope.contact));
            $scope.addContactVisibility = false;
            $scope.contact = {};
          });
      };

      //attach an existing group to a contact
      $scope.addgroup = function(i, group) {
        var cid = $scope.contacts[i].id;
        $http.post('/user/' + $rootScope.auth.id + '/contact/' + cid + '/contactgroup', group)
          .then(function(response) {
            $scope.contacts[i].groups.unshift(angular.copy(group));
          });
      };

      //edits an existing contact
      $scope.editcontact = function(i) {
        $http.put('/user/' + $rootScope.auth.id + '/contact', $scope.contacts[i])
          .then(function(response) {
            $scope.contacts[i].editContactVisibility = false;
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

      //remove contact group from a contact
      $scope.removecontactgroup = function(i, j) {
        var cid = $scope.contacts[i].id;
        var gid = $scope.contacts[i].groups[j].id;
        $http.delete('/user/' + $rootScope.auth.id + '/contact/' + cid + '/contactgroup/' + gid)
          .then(function(response) {
            $scope.contacts[i].groups.splice(j, 1);
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
