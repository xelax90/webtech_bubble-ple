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

app.directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs, rootScope) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;


            
            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                    console.log("binding file");
                });
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