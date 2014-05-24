(function() {
  'use strict';

  angular.module('addressbookApp', ['ngRoute'])
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
    .otherwise({
      redirectTo: '/'
    });
  }])
  .run();
})();
