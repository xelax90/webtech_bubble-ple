/**
 * Created by albisema on 10/01/16.
 */
'use strict';

angular.module('nodes', [
        'ngRoute',
        'ngMaterial',
        'ngFileUpload'
    ])
    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/courseroom',{
            templateUrl: (applicationBasePath ? applicationBasePath : '') + 'js/angular/nodes/nodes.html',
            controller: 'NodesCtrl'
        });
    }])
<<<<<<< HEAD
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

=======
    .controller('NodesCtrl', ['$location', '$scope', '$timeout', 'Upload', '$mdToast', function($location, $scope, $timeout, Upload, $mdToast){
>>>>>>> a8e15582b0f3e62be2989e53257759c1509c4bf7
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

<<<<<<< HEAD
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
=======

         $scope.onFileSelect = function(file) {

          if(!file) return;

          console.log("in file select");

          console.log(file.name);

          file.upload = Upload.upload({
            url: 'http://bubbleple.localhost/de/admin/bubblePLE/fileAttachments/rest',
            data: {fileattachment: {filename: file, title: file.name}},
          });

          file.upload.then(function (response) {
            $timeout(function () {
              file.result = response.data;
              console.log(response);
              $mdToast.show(
                      $mdToast.simple()
                          .textContent('File uploaded successfully')
                          .position('bottom')
                          .hideDelay(3000)
               );
              addNode(file.name);
            });
          }, function (response) {
            if (response.status > 0)
              $scope.errorMsg = response.status + ': ' + response.data;
            console.log("in response");
            console.log(response);
            $mdToast.show(
                      $mdToast.simple()
                          .textContent('Error Uploading file')
                          .position('bottom')
                          .hideDelay(3000)
                  );
          }, function (evt) {
            // Math.min is to fix IE which reports 200% sometimes
            file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
          });
>>>>>>> a8e15582b0f3e62be2989e53257759c1509c4bf7
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
       };
  
    }]);