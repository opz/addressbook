(function() {
  'use strict';

  angular.module('addressbookApp', ['ngRoute', 'ngResource'])
  .config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when('/', {
      templateUrl: 'templates/home.html',
      controller: 'homeCtrl'
    })
    .when('/addcontact', {
      templateUrl: 'templates/addcontact.html',
      controller: 'addcontactCtrl'
    })
    .when('/contact', {
      templateUrl: 'templates/contact.html',
      controller: 'contactCtrl'
    })
    .when('/contactlist', {
      templateUrl: 'templates/contactlist.html',
      controller: 'contactlistCtrl'
    })
    .otherwise({
      redirectTo: '/'
    });
  }])
  .run(function($rootScope) {
    $rootScope.navbar = [
      { name: 'Contacts', route: '/contactlist' },
      { name: 'Add Contact', route: '/addcontact' }
    ];
  });
})();
