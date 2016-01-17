/**
 * Created by albisema on 10/01/16.
 */
'use strict';

angular.module('nodes', [
        'ngRoute',
        'ngMaterial'
    ])
    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/courseroom',{
            templateUrl: (applicationBasePath ? applicationBasePath : '') + 'js/angular/nodes/nodes.html',
            controller: 'NodesCtrl'
        });
    }])
    .controller('NodesCtrl',['$location', '$scope', function($location, $scope){
        var nodes = new Object([
            {id: 1, label: 'eLearning', title: 'Press for eLearning PLE'},
            {id: 2, label: 'Web Technologies', title: 'Press for Web Technologies PLE'},
            {id: 3, label: 'Computer Vision', title: 'Press for Computer Vision PLE'}
        ]);
        var a = $location.search();
        $scope.courseName = nodes[a.courseId -1].label;
    }]);