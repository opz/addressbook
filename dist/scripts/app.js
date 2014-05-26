(function() {
  'use strict';

  angular.module('addressbookApp', ['ngRoute',
    'ngResource',
    'ngAnimate',
    'ng-match'])
  .config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when('/', {
      templateUrl: 'templates/home.html',
      controller: 'homeCtrl'
    })
    .when('/login', {
      templateUrl: 'templates/login.html',
      controller: 'authCtrl'
    })
    .when('/signup', {
      templateUrl: 'templates/signup.html',
      controller: 'userCtrl'
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
