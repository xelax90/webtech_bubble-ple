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
    $routeProvider.otherwise({redirectTo: '/bubbles'});
    $routeProvider.when('/bubbles',{
            templateUrl: (applicationBasePath ? applicationBasePath : '') + 'js/angular/nodes/nodes.html',
            controller: 'nodeCtrl'
        });
    $routeProvider.when('/login',{
            templateUrl: (applicationBasePath ? applicationBasePath : '') + 'js/angular/login/login.html',
            controller: 'loginCtrl'
        });
}]);

app.directive('fileModel', ['$parse', 'fileService', function ($parse, fileService) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs, rootScope) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;


            
            element.bind('change', function(){
                modelSetter(scope, element[0].files[0]);
                    console.log("binding file");
                    fileService.push(element[0].files[0]);
            });
        }
    };
}]);

app.service('fileUpload', ['$http', function ($http) {
    this.uploadFileToUrl = function(file, req, uploadUrl){
        var fd = new FormData();
        fd.append('file', file);
        fd.append('file', file);
        $http.post(uploadUrl, fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(){
        })
        .error(function(){
        });
    }
}]);

app.factory('fileService', function() {
    var files = [];
    return files;
});