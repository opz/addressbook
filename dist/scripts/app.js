(function() {
  'use strict';

  angular.module('addressbookApp', ['ngRoute'])
  .config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when('/', {
      templateUrl: 'templates/contact.html',
      controller: 'contactCtrl'
    })
    .otherwise({
      redirectTo: '/'
    });
  }])
  .run();
})();
