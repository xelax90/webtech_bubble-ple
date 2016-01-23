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


    .controller('NodesCtrl', ['$location', '$scope', '$timeout', 'Upload', '$mdToast', '$mdDialog', function($location, $scope, $timeout, Upload, $mdToast, $mdDialog){

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

        $scope.addNewNode = function (){
            $mdDialog.show({
                template:
                    '<md-dialog aria-label="List dialog">' +
                    '  <md-dialog-content>'+
                    '    <br>'+
                    '    <md-input-container>'+
                    '        <label>Bubble Name</label>'+
                    '        <input type="text" ng-model="bubbleName">'+
                    '    </md-input-container>'+
                    '    <md-list>'+
                    '      <md-list-item ng-repeat="item in items">'+
                    '       <p>Number {{item.label}}</p>' +
                    '      </md-list-item>' +
                    '    </md-list>'+
                    '    <select ng-model="model" ng-options="item.label in items"></select>'+
                    '  </md-dialog-content>' +
                    '  <md-dialog-actions>' +
                    '    <md-button ng-click="addingNewNode()" class="md-primary">' +
                    '      Add Node' +
                    '    </md-button>' +
                    '    <md-button ng-click="closeDialog()" class="md-primary">' +
                    '      Close Dialog' +
                    '    </md-button>' +
                    '  </md-dialog-actions>' +
                    '</md-dialog>',
                locals: {
                    items: (nodes._data)
                },
                controller: DialogController
            });

            function DialogController($scope, $mdDialog, items) {
                console.log(items);

                $scope.bubbleName = "";
                $scope.items = items;
                $scope.addingNewNode = function() {

                    var selectedNodeId = parseInt(network.getSelectedNodes());
                    console.log("Adding New Node to Node: " + selectedNodeId);

                    var nodeId = new Date().getUTCMilliseconds();            
                    nodes.update({id: nodeId, label: $scope.bubbleName});
                    edges.update({from: nodeId, to: selectedNodeId});

                    $mdToast.show(
                        $mdToast.simple()
                            .textContent('Added NodeName')
                            .position('bottom')
                            .hideDelay(3000)
                    );
                    
                    $scope.bubbleName = "";
                    $mdDialog.hide();
                }
                $scope.closeDialog = function() {
                    $mdDialog.hide();
                }
            }
        };

        $scope.deleteSelectedNode = function (){
            var selectedNodeId = parseInt(network.getSelectedNodes());
            console.log("Deleting Node: " + selectedNodeId);

            var del = network.getSelectedNodes();
            network.deleteSelected();

            $mdToast.show(
                $mdToast.simple()
                    .textContent('Deleted Node: ' + selectedNodeId)
                    .position('bottom')
                    .hideDelay(3000)
            );
        };
        
        // for Opening the <form> to add text to node          
        $scope.openTextBox = function(){
            if(network.getSelectedNodes().length > 0){           
                var parentEl = angular.element(document.body);
                $mdDialog.show({
                  parent: parentEl,
                  template:
                    '<md-dialog>' +
                    '   <md-dialog-content>'+
                    '       <md-input-container>' +
                    '           <textarea ng-model="node.text" >' +
                    '           </textarea>' +
                    '       </md-input-container>'+
                    '   </md-dialog-content>' +
                    '   <md-dialog-actions>' +
                    '       <md-button ng-click="addTextToNodes()" class="md-primary">' +
                    '           Save' +
                    '       </md-button>' +
                    '   </md-dialog-actions>' +
                    '</md-dialog>',

                  clickOutsideToClose: true,
                  // for saving the added text to the node
                  controller: function($scope, $mdDialog){
                    $scope.addTextToNodes = function(){
                        var selectedNodes = network.getSelectedNodes();
                        for(var i = 0; i < selectedNodes.length; i++){
                            nodes.update({id: selectedNodes[0], title: $scope.node.text});
                        }
                        $mdDialog.hide();
                    }
                  }
                });  
           } 
            
        };
        

        //trigger onFileSelect method on clickUpload button clicked
        $scope.clickUpload = function(){
            document.getElementById('i_file').click();
        };


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