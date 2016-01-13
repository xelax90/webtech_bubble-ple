/**
 * Created by albisema on 10/01/16.
 */

'use strict';

angular.module('courses', [
    'ngRoute',
    'ngMaterial'
])
    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/courses',{
            templateUrl: 'js/angular/courses/courses.html',
            controller: 'CourseCtrl'
        });
}])
    .controller('CourseCtrl',['$location', '$scope', '$mdMedia', '$mdDialog', function($location, $scope, $mdMedia, $mdDialog){

        var nodes = new vis.DataSet([
            {id: 1, label: 'eLearning', title: 'Press for eLearning PLE'},
            {id: 2, label: 'Web Technologies', title: 'Press for Web Technologies PLE'},
            {id: 3, label: 'Computer Vision', title: 'Press for Computer Vision PLE'}
        ]);
        $scope.inpshow = false;
        $scope.switchInput = function(){
            if (!$scope.inpshow)
                $scope.inpshow = true;
            else $scope.inpshow = false;
        };

        $scope.addCourse = function(){
            nodes.update({id: nodes.length+1, label: $scope.courseName, title: 'Press for '+$scope.courseName + ' PLE'});
            $scope.switchInput();
            $scope.courseName = null;
        };

/* create an array with edges
        var edges = new vis.DataSet([
            {from: 1, to: 3},
            {from: 1, to: 2},
            {from: 2, to: 4},
            {from: 2, to: 5}
        ]);
        */

// create a network
        var container = document.getElementById('mynetwork');

// provide the data in the vis format
        var data = {
            nodes: nodes,
            //edges: edges
        };

        var locales = {
            en: {
                edit: 'Edit',
                del: 'Delete selected',
                back: 'Back',
                addNode: 'Add Node',
                addEdge: 'Add Edge',
                editNode: 'Edit Node',
                editEdge: 'Edit Edge',
                addDescription: 'Click in an empty space to place a new node.',
                edgeDescription: 'Click on a node and drag the edge to another node to connect them.',
                editEdgeDescription: 'Click on the control points and drag them to a node to connect to it.',
                createEdgeError: 'Cannot link edges to a cluster.',
                deleteClusterError: 'Clusters cannot be deleted.',
                editClusterError: 'Clusters cannot be edited.'
            }
        };

        var options = {
            autoResize: true,
            height: '100%',
            width: '100%',
            locale: 'en',
            locales: locales,
            clickToUse: false,
            interaction:{
                dragNodes:true,
                dragView: true,
                hideEdgesOnDrag: false,
                hideNodesOnDrag: false,
                hover: true,
                hoverConnectedEdges: true,
                keyboard: {
                    enabled: true,
                    speed: {x: 10, y: 10, zoom: 0.02},
                    bindToWindow: true
                },
                multiselect: true,
                navigationButtons: false,
                selectable: true,
                selectConnectedEdges: true,
                tooltipDelay: 300,
                zoomView: true
                }
        };

// initialize your network!
        var network = new vis.Network(container, data, options);
        network.setOptions(options);
        network.startSimulation();
        network.on('showPopup', function(){});
        network.on('click', function(node){
            if (node.nodes[0]){
                $scope.$apply(function(){
                    $location.path('/courseroom').search({courseId: node.nodes[0]});
                });
            }
        })

    }]);
