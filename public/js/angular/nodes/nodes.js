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

        $scope.showProgressBar = false;
        var options = {
            autoResize: true,
            height: '100%',
            width: '100%',
            locale: 'en',
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
                navigationButtons: true,
                selectable: true,
                selectConnectedEdges: true,
                tooltipDelay: 300,
                zoomView: true
            }
        };

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

        //var a = $location.search();
        //console.log(a);
        //$scope.courseName = nodes[a.courseId].label;

        // create a network
        var container = document.getElementById('bubbles');

        // provide the data in the vis format
        var data = {
            nodes: nodes,
            edges: edges
        };

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
                    // '    <md-list>'+
                    // '      <md-list-item ng-repeat="item in items">'+
                    // '       <p>Number {{item.label}}</p>' +
                    // '      </md-list-item>' +
                    // '    </md-list>'+
                    // '    <select ng-model="model" ng-options="item.label in items"></select>'+
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
                            .textContent('Bubble Added: ' +  $scope.bubbleName)
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

        $scope.addNewEdge = function (){
            var selectedNodes = network.getSelectedNodes();
            console.log("Adding Edge: " + selectedNodes[0] + ", " + selectedNodes[1]);

            if(selectedNodes.length < 2){
              $mdToast.show(
                  $mdToast.simple()
                      .textContent('Please select at least 2 Bubbles!')
                      .position('bottom')
                      .hideDelay(3000)
              );
              return false;
            }

            edges.update({from: selectedNodes[0], to: selectedNodes[1]});
            $mdToast.show(
                $mdToast.simple()
                    .textContent('Bubbles ' + selectedNodes + ' are now Connected!')
                    .position('bottom')
                    .hideDelay(3000)
            );
        };

        $scope.deleteSelectedNode = function (){
            var selectedNodeId = parseInt(network.getSelectedNodes());
            console.log("Deleting Node: " + selectedNodeId);

            if(selectedNodeId){
              var del = network.getSelectedNodes();
              network.deleteSelected();

              $mdToast.show(
                  $mdToast.simple()
                      .textContent('Deleted Node: ' + selectedNodeId)
                      .position('bottom')
                      .hideDelay(3000)
              );
            } else {
              $mdToast.show(
                  $mdToast.simple()
                      .textContent('Please select a Node!')
                      .position('bottom')
                      .hideDelay(3000)
              );
            }
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
                    '           <textarea ng-model="node.text" placeholder="add text">' +
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
                            nodes.update({id: selectedNodes[i], title: $scope.node.text});
                        }
                        $mdDialog.hide();
                    }
                  }
                });
           }

        };
        
        
        // for Opening the <form> to change label of the node
        $scope.openNodeChangeBox = function(){
            if(network.getSelectedNodes().length > 0){
                var parentEl = angular.element(document.body);
                $mdDialog.show({
                  parent: parentEl,
                  template:
                    '<md-dialog>' +
                    '   <md-dialog-content>'+
                    '       <md-input-container>' +
                    '           <textarea ng-model="node.text" placeholder="change label">' +
                    '           </textarea>' +
                    '       </md-input-container>'+
                    '   </md-dialog-content>' +
                    '   <md-dialog-actions>' +
                    '       <md-button ng-click="changeNodeLabel()" class="md-primary">' +
                    '           Save' +
                    '       </md-button>' +
                    '   </md-dialog-actions>' +
                    '</md-dialog>',

                  clickOutsideToClose: true,
                  // for saving the added text to the node
                  controller: function($scope, $mdDialog){
                    $scope.changeNodeLabel = function(){
                        var selectedNodes = network.getSelectedNodes();
                        for(var i = 0; i < selectedNodes.length; i++){
                            nodes.update({id: selectedNodes[i], label: $scope.node.text});
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

          $scope.showProgressBar = true;

          file.upload = Upload.upload({
            url: 'http://bubbleple.localhost/de/admin/bubblePLE/fileAttachments/rest',
            data: {fileattachment: {filename: file, title: file.name}},
          });


          file.upload.progress(function(evt){
              console.log('percent: ' +parseInt(100.0 * evt.loaded / evt.total));
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
              $scope.showProgressBar = false;
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
            $scope.showProgressBar = false;
          }, function (evt) {
            // Math.min is to fix IE which reports 200% sometimes
            file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
            console.log(file.progress);
            $scope.progressBarValue = file.progress;
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