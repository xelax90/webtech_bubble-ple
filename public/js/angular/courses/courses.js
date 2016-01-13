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
            templateUrl: 'courses/courses.html',
            controller: 'CourseCtrl'
        });
}])
    .controller('CourseCtrl',['$location', '$scope', function($location, $scope){
        var nodes = new vis.DataSet([
            {id: 1, label: 'eLearning', title: 'Press for eLearning PLE'},
            {id: 2, label: 'Web Technologies', title: 'Press for Web Technologies PLE'},
            {id: 3, label: 'Computer Vision', title: 'Press for Computer Vision PLE'}
        ]);

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

    }])

    .controller('DemoCtrl',['$scope', '$mdDialog', '$mdMedia', function($mdMedia, $scope, $mdDialog) {
        $scope.showAdvanced = function(ev) {
            var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
            $mdDialog.show({
                    controller: DialogController,
                    templateUrl: 'courses/modal.html',
                    parent: angular.element(document.body),
                    targetEvent: ev,
                    clickOutsideToClose:true,
                    fullscreen: useFullScreen
                })
                .then(function(answer) {
                    $scope.status = 'You said the information was "' + answer + '".';
                }, function() {
                    $scope.status = 'You cancelled the dialog.';
                });
            $scope.$watch(function() {
                return $mdMedia('xs') || $mdMedia('sm');
            }, function(wantsFullScreen) {
                $scope.customFullscreen = (wantsFullScreen === true);
            });
        };
}]);
