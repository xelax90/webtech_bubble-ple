 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
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
    $routeProvider.otherwise({redirectTo: '/courses'});
    $routeProvider.when('/courses',{
            templateUrl: (applicationBasePath ? applicationBasePath : '') + 'js/angular/courses/courses.html',
            controller: 'courseCtrl'
        });
    $routeProvider.when('/courseroom',{
            templateUrl: (applicationBasePath ? applicationBasePath : '') + 'js/angular/nodes/nodes.html',
            controller: 'nodeCtrl'
        });
}]);
