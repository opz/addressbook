/**
 * @author Will Shahda <will.shahda@gmail.com>
 */
(function() {
  'use strict';

  angular.module('addressbookApp', [
    'ngRoute',
    'ngResource',
    'ngAnimate',
    'ng-match'
  ])
  .config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when('/', {
      templateUrl: 'templates/home.html',
      controller: 'homeCtrl'
    })
    .when('/login', {
      templateUrl: 'templates/login.html',
      controller: 'userCtrl'
    })
    .when('/signup', {
      templateUrl: 'templates/signup.html',
      controller: 'userCtrl'
    })
    .when('/contactlist', {
      templateUrl: 'templates/contactlist.html',
      controller: 'contactlistCtrl'
    })
    .otherwise({
      redirectTo: '/'
    });
  }])
  .run(['$rootScope', '$location', function($rootScope, $location) {
    //restrict private pages unless user is logged in
    $rootScope.$on('$routeChangeStart', function(event, next, current) {
      if ($rootScope.auth && $rootScope.auth.hasOwnProperty('id')) {
        $rootScope.navbar = [
          { name: 'Contacts', route: '/contactlist' },
        ];
      } else if (next.templateUrl !== 'templates/home.html'
        && next.templateUrl !== 'templates/login.html'
        && next.templateUrl !== 'templates/signup.html') {
          $location.path('/login');
        }
    });

    //logout user
    $rootScope.logout = function() {
      $rootScope.auth = null;
      $location.path('/login');
    };
  }]);
})();
