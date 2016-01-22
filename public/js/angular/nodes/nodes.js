/**
 * Created by albisema on 10/01/16.
 */
'use strict';

angular.module('nodes', [
        'ngRoute',
        'ngMaterial',
        'angularFileUpload'
    ])
    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/courseroom',{
            templateUrl: (applicationBasePath ? applicationBasePath : '') + 'js/angular/nodes/nodes.html',
            controller: 'NodesCtrl'
        });
    }])
    .directive('uploadfile', function () {
        return {
          restrict: 'A',
          link: function(scope, element) {

            element.bind('click', function(e) {
                angular.element(e.target).siblings('#i_file').trigger('click');
            });
          }
        };
    })

    .controller('NodesCtrl',['$location', '$scope','$timeout', '$upload', function($location, $scope, $timeout, $upload){

        var nodes = new vis.DataSet([
            {id: 1, label: 'Node 1'},
            {id: 2, label: 'Node 2'},
            {id: 3, label: 'Node 3'},
            {id: 4, label: 'Node 4'},
            {id: 5, label: 'Node 5'}
        ]);

        // create an array with edges
        var edges = new vis.DataSet([
            {from: 1, to: 3},
            {from: 1, to: 2},
            {from: 2, to: 4},
            {from: 2, to: 5}
        ]);

        var a = $location.search();
        //$scope.courseName = nodes[a.courseId -1].label;

        // create a network
        var container = document.getElementById('bubbles');

        // provide the data in the vis format
        var data = {
            nodes: nodes,
            edges: edges
        };
        var options = {};

        // initialize your network!
        var network = new vis.Network(container, data, options);

        //trigger onFileSelect method on clickUpload button clicked
        $scope.clickUpload = function(){
            document.getElementById('i_file').click();
        };

        //upload File
         $scope.uploadResult = [];

         $scope.onFileSelect = function($files) {
        //$files: an array of files selected, each file has name, size, and type.

        console.log("in file select");

        for (var i = 0; i < $files.length; i++) {
             var $file = $files[i];
             $upload.upload({
                 url: (applicationBasePath ? applicationBasePath : '') + 'php/upload.php',
                 file: $file,
                 progress: function(e){}
             }).then(function(response) {
                 // file is uploaded successfully
                 $timeout(function() {
                 $scope.uploadResult.push(response.data);
                 console.log($scope.uploadResult);
                console.log("file uploaded : " + $file.name);
                addNode($file.name);
                 });

             }); 
        }
      }


      function addNode(name){
                var newId = nodes.length + 1;
                nodes.update({id: newId, label: name, title: 'Uploaded file'});

                var selectedNode = network.getSelectedNodes();
                console.log("nodes lenght : " + selectedNode.length);
                if(selectedNode.length > 0){
                    for(var i= 0; i < selectedNode.length; i++){
                        console.log(selectedNode[i]);
                        edges.update({from: newId, to: selectedNode[i]});
                    }
                }
                // $mdToast.show(
                //     $mdToast.simple()
                //         .textContent('File uploaded : ' + name)
                //         .position('bottom')
                //         .hideDelay(3000)
                // );
     };
    }]);