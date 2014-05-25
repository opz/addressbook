(function() {
  'use strict';

  angular.module('addressbookApp')
  .factory('contact', ['$resource', function($resource) {
    return $resource('/contact/:cid',
      {
        cid: '@cid',
        first: '@first',
        last: '@last',
        email: '@email',
        address: '@address',
        phone: '@phone',
        notes: '@notes'
      });
  }]);
})();
