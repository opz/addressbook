(function() {
  'use strict';

  angular.module('addressbookApp', ['ngRoute', 'ngResource'])
  .config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when('/', {
      templateUrl: 'templates/home.html',
      controller: 'homeCtrl'
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
    ];
  });
})();
