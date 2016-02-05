'use strict';

// Declare app level module which depends on views, and components
var app = angular.module(
    'myApp', 
    [
        'ngRoute',
        'ngMaterial',
        'ngFileUpload'
])
.config(['$routeProvider', '$mdThemingProvider', function($routeProvider, $mdThemingProvider) {
    $routeProvider.otherwise({redirectTo: '/'});
    $routeProvider.when('/',{
            templateUrl: (applicationBasePath ? applicationBasePath : '') + 'js/angular/nodes/nodes.html',
            controller: 'nodeCtrl'
        });
}]);