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

    .directive('scrollToItem', function() {                                                      
        return {                                                                                 
            restrict: 'A',                                                                       
            scope: {                                                                             
                scrollTo: "@"                                                                    
            },                                                                                   
            link: function(scope, $elm, attr) {

                $elm.on('click', function() {
                    $('html,body').animate({scrollTop: $(scope.scrollTo).offset().top }, "slow");
                });
            }
        };
    })


    .controller('NodesCtrl', ['$location', '$scope', '$timeout', 'Upload', '$mdToast', '$mdDialog', '$http', '$anchorScroll', function($location, $scope, $timeout, Upload, $mdToast, $mdDialog, $http, $anchorScroll){

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
                    },
            manipulation:
                {
                    enabled: false,
                    addNode: function(data, callback){
                        $mdDialog.show({
                            template:
                                '<md-dialog aria-label="List dialog">' +
                                '  <md-dialog-content>'+
                                '    <br>'+
                                '    <md-input-container>'+
                                '        <label>Bubble Name</label>'+
                                '        <input type="text" ng-model="bubbleName">'+
                                '    </md-input-container>'+
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
                            controller: DialogController
                        });

                        function DialogController($scope, $mdDialog) {
                            $scope.addingNewNode = function() {
                                data.label = $scope.bubbleName;
                                $mdToast.show(
                                    $mdToast.simple()
                                        .textContent('Bubble Added: ' +  $scope.bubbleName)
                                        .position('bottom')
                                        .hideDelay(3000)
                                );

                                $scope.bubbleName = "";
                                $mdDialog.hide();
                                callback(data);
                            };
                            $scope.closeDialog = function() {
                                network.disableEditMode();
                                $mdDialog.hide();
                                
                            };

                        }

                    }
                }
        };
        
         var baseColor = {
          border: '#2B7CE9',
          background: '#97C2FC',
          highlight: {
            border: '#2B7CE9',
            background: '#D2E5FF'
          },
          hover: {
            border: '#2B7CE9',
            background: '#D2E5FF'
          }
        };


        var importantColor = {
          border: '#BCDB3A',
          background: '#D2F931',
          highlight: {
            border: '#D7E13C',
            background: '#F0FD32'
          },
          hover: {
            border: '#D7E13C',
            background: '#F0FD32'
          }
        };

        var v_importantColor = {
          border: '#D21E1E',
          background: '#F90000',
          highlight: {
            border: '#E43C3C',
            background: '#FF3232'
          },
          hover: {
            border: '#E43C3C',
            background: '#FF3232'
          }
        };


        options.nodes = {
          color : baseColor
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


        $scope.addNewBubble = function (){
              network.addNodeMode();
        };

        $scope.addNewEdge = function (){
            network.addEdgeMode();
        };
        
        $scope.addLinkBubble = function (){
            network.addNodeMode();
        };
        

        $scope.deleteSelectedNodeEdge = function (){
            var selectedNodeId = network.getSelectedNodes();
            var selectedEdgeId = network.getSelectedEdges();

            console.log("Deleting Node: " + selectedNodeId);
            console.log("Deleting Node: " + selectedEdgeId);

            var toastMessage = '';
            if(selectedEdgeId){
                network.deleteSelected();
                toastMessage += 'Deleted ' + selectedEdgeId.length + ' Edge(s) and ';
            } if(selectedNodeId){
                network.deleteSelected();
                toastMessage += 'Bubble(s) ' + selectedNodeId;
            } if( selectedNodeId.length>0 || selectedEdgeId.length>0 ){
                console.log(":"+selectedNodeId + ":" + selectedEdgeId+":");
                $mdToast.show(
                    $mdToast.simple()
                        .textContent(toastMessage)
                        .position('bottom')
                        .hideDelay(3000)
                );
            } else {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent('Please select a Bubble or an Edge!')
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
              console.log(response.data.item.filename);

              var filename = String(response.data.item.filename);
              var res = filename.split("/files/fileattachment/");
              addNode(res[1]);
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

       /* added single and double click event to bubbles */
        network.on('click', onClick);
        network.on('doubleClick', onDoubleClick);

        var doubleClickTime = 0;
        var threshold = 200;

        /*When user click on bubble then this method will be called to check whether user click once or twice*/
        function onClick(properties) {
            var t0 = new Date();
            if (t0 - doubleClickTime > threshold) {
                setTimeout(function () {
                    if (t0 - doubleClickTime > threshold) {
                        doOnClick(properties);
                    }
                },threshold);
            }
        }

        /*If user single click on the bubble then this method will be called*/
        function doOnClick(properties) {
        }

        /*If user double click on the bubble then this method will be called*/
        function onDoubleClick(properties) {
            doubleClickTime = new Date();
            console.log(properties);

            var nodeId = properties.nodes[0];
            var node = nodes.get(nodeId);
            var filename = node.label;

            //downloadFile(filename);
            fileExist(filename);
        }

        /*Serve file to download*/
        function downloadFile(filename){

          var fileattachmentPath = "/files/fileattachment/";
          var completePath = fileattachmentPath + filename;

            console.log("file exists ...");
            var hiddenElement = document.createElement('a');
            hiddenElement.href = completePath;
            hiddenElement.target = '_blank';
            hiddenElement.download = filename;
            hiddenElement.click();
        }

        /*Check if file exist then download. It is to make sure that the click bubble is a file not a text*/
        function fileExist(filename){

          var fileattachmentPath = "/files/fileattachment/";
          var completePath = fileattachmentPath + filename;

          //head is use just to check whether file exist or not instead of get which is used to get the content
          $http.head(completePath)
                     .success(function(data, status){
                        if(status == 200 ){
                          console.log("file found");
                          downloadFile(filename);
                        }else{
                            return false;
                        }
                     })
                     .error(function(data,status){
                      console.log("error");
                        if(status==200){}
                        else{}
                          return false;
                     });
        }

        /* Search node in network */
        $scope.searchNode = function (){
            $mdDialog.show({
                template:
                    '<md-dialog aria-label="List dialog">' +
                    '  <md-dialog-content>'+
                    '    <br>'+
                    '    <md-input-container>'+
                    '        <label>Enter title to search</label>'+
                    '        <input type="text" ng-model="searchTitle">'+
                    '    </md-input-container>'+
                    '  </md-dialog-content>' +
                    '  <md-dialog-actions>' +
                    '    <md-button ng-click="search()" class="md-primary">' +
                    '      Search' +
                    '    </md-button>' +
                    '    <md-button ng-click="closeDialog()" class="md-primary">' +
                    '      Cancel' +
                    '    </md-button>' +
                    '  </md-dialog-actions>' +
                    '</md-dialog>',
                locals: {
                    items: (nodes._data)
                },
                controller: searchController
            });

            /*Controller to look into nodes to search for node*/
            function searchController($scope, $mdDialog, items) {
                console.log(items);
                console.log(items[1].label);
                $scope.searchTitle = "";
                $scope.items = items;
                console.log(nodes);

                /* This method will be called when user clicked on search button */
                $scope.search = function() {

                    if($scope.searchTitle == "") return;

                    var i = 1;
                    var isFound = false;
                    for(i = 1; i <= nodes.length; i++){
                      if($scope.items[i].label == $scope.searchTitle){
                        console.log("hurray found");
                        network.selectNodes([$scope.items[i].id], true);
                        network.focus($scope.items[i].id);
                        isFound = true;
                      }
                    }

                    if(!isFound){
                      $mdToast.show(
                        $mdToast.simple()
                            .textContent("Sorry. Unable to find the searched bubble")
                            .position('bottom')
                            .hideDelay(3000)
                    );
                    }

                    $mdDialog.hide();
                };

                $scope.closeDialog = function() {
                    $mdDialog.hide();
                };
            }
        };


                /* Change Color of network */
        $scope.changeColor = function (){

            if (network.getSelectedNodes().length == 0){
              $mdToast.show(
                      $mdToast.simple()
                          .textContent('Please select a node first')
                          .position('bottom')
                          .hideDelay(3000)
               );
              return;
            } 

            $mdDialog.show({
                template:
                    '<md-dialog aria-label="List dialog">' +
                    '  <md-dialog-content>'+
                    '    <br>'+
                    '      <h2>Select Priority</h2>'+
                    '         <md-radio-group ng-model="priority">'+
                    '            <md-radio-button value="Very_Important" class="md-primary"> Very Important </md-radio-button>'+
                    '            <md-radio-button value="Important"> Important </md-radio-button>'+
                    '            <md-radio-button value="Normal"> Normal </md-radio-button>'+
                    '          </md-radio-group>'+
                    '  </md-dialog-content>' +
                    '  <md-dialog-actions>' +
                    '    <md-button ng-click="change()" class="md-primary">' +
                    '      Ok' +
                    '    </md-button>' +
                    '    <md-button ng-click="closeDialog()" class="md-primary">' +
                    '      Cancel' +
                    '    </md-button>' +
                    '  </md-dialog-actions>' +
                    '</md-dialog>',
                locals: {
                    items: (nodes._data)
                },
                controller: changeColorController
            });

            /*Controller to look into nodes to search for node*/
            function changeColorController($scope, $mdDialog, items) {
                $scope.searchTitle = "";
                $scope.items = items;
                
                console.log(nodes);

                /* This method will be called when user clicked on search button */
                $scope.change = function() {

                  var selectedNode = network.getSelectedNodes();
                  console.log("nodes lenght : " + selectedNode.length);
                  if(selectedNode.length > 0){
                      for(var i= 0; i < selectedNode.length; i++){
                          console.log($scope.priority);
                          if($scope.priority == "Very_Important")
                            nodes.update({id : selectedNode[i], color : v_importantColor});
                          else if($scope.priority == "Important")
                            nodes.update({id : selectedNode[i], color : importantColor});
                          else
                            nodes.update({id : selectedNode[i], color : baseColor});
                      }
                  }
                    $mdDialog.hide();
                };

                $scope.closeDialog = function() {
                    $mdDialog.hide();
                };
            }
        };

    }]);