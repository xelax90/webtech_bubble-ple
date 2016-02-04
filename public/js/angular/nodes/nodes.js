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
        $routeProvider.when('/',{
            templateUrl: (applicationBasePath ? applicationBasePath : '') + 'js/angular/nodes/nodes.html',
            controller: 'NodesCtrl'
        });
    }])
    .controller('NodesCtrl', ['$mdSidenav', '$location', '$scope', '$timeout', 'Upload', '$mdToast', '$mdDialog', '$http', '$anchorScroll', function($mdSidenav, $location, $scope, $timeout, Upload, $mdToast, $mdDialog, $http, $anchorScroll){

        $scope.toggleList = function(){
          $mdSidenav('left').toggle();
        };

        $scope.showProgressBar = false;
        var bubbleType = 'Bubble';
        var options = {
            autoResize: true,
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
                    enabled: false,
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
                            template: getTemplate(bubbleType),                                
                            controller: DialogController
                        });

                        function DialogController($scope, $mdDialog) {
                            $scope.addingNewNode = function() {
                                var req = {course: { title: $scope.bubbleName}};
                                $http.post('/admin/bubblePLE/courses/rest', req).then(function(response){
                                    data.id = response.data.item.id;
                                    data.label = response.data.item.title;
                                    data.title = response.data.item.title;
                                    $mdToast.show(
                                        $mdToast.simple()
                                            .textContent('Bubble Added: ' +  data.title)
                                            .position('bottom')
                                            .hideDelay(3000)
                                    );
                                    callback(data);

                                }, function(errResponse){
                                    $mdToast.show(
                                        $mdToast.simple()
                                            .textContent('Error adding Bubble!')
                                            .position('bottom')
                                            .hideDelay(3000)
                                    );
                                });

                                $scope.bubbleName = "";
                                $mdDialog.hide();

                            };
                            $scope.closeDialog = function() {
                                network.disableEditMode();
                                $mdDialog.hide();
                                
                            };
                        }

                    },

                    addEdge: function(edgeData,callback) {
                        edgeData.arrows = 'to';
                        var req = {edge: {from: edgeData.from, to: edgeData.to}};
                        $http.post('/admin/bubblePLE/edges/rest', req).then(function(response){
                            console.log(response);
                            $mdToast.show(
                                $mdToast.simple()
                                    .textContent('Bubbles connected.')
                                    .position('bottom')
                                    .hideDelay(3000)
                            );
                            callback(data);

                        }, function(errResponse){
                            $mdToast.show(
                                $mdToast.simple()
                                    .textContent('Error connectiong Bubbles!')
                                    .position('bottom')
                                    .hideDelay(3000)
                            );
                        });
                        callback(edgeData);

                    }
                }
        };

        $http.get('/admin/bubblePLE/semesters/rest').then(function(response) {
            var semId = response.data[0].id;
            getCourses(semId);
        }, function(errResponse) {
            console.log('Error fetching data!');
            $mdToast.show(
                $mdToast.simple()
                    .textContent('Error fetching semester')
                    .position('bottom')
                    .hideDelay(3000)
            );
        });

        //filter courses of one semester
        function getCourses(semesterId){
            $http.get('/admin/bubblePLE/filter/parent/'+semesterId).then(function(response) {
                var bubbles = new Array();
                var items = response.data.bubbles;
                var edges = response.data.edges;
                console.log(items);
                for (var i = 0; i < items.length; i++){
                    if ((items[i].bubbleType.search("Semester") != -1) || (items[i].bubbleType.search("Course")) != -1) {
                        bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title});
                    }
                }
                for (var i = 0; i < edges.length; i++){
                    edges[i].arrows = 'to';
                }
                var nodes = new vis.DataSet(bubbles);

                var edges = new vis.DataSet(edges);

                // create a network
                var container = document.getElementById('bubbles');

                // provide the data in the vis format
                var data = {
                    nodes: nodes,
                    edges: edges
                };

                // initialize your network!
                var network = new vis.Network(container, data, options);
                network.on('doubleClick', function(node){
                    if (node.nodes[0]){
                        if (isCourse(node.nodes[0], items)){
                            getAttachments(node.nodes[0]);
                        }
                    }
                });
                visualize(nodes, edges, network);
            }, function(errResponse) {
                console.log('Error fetching data!');
                $mdToast.show(
                    $mdToast.simple()
                        .textContent('Error fetching courses')
                        .position('bottom')
                        .hideDelay(3000)
                );
            });
        }

        function isCourse(id, items){
            for (var i = 0; i < items.length; i++){
                if (items[i].id == id){
                    if (items[i].bubbleType.search("Course") != -1) {
                        return true;
                    }
                }
            }
            return false;
        }

        function getAttachments(courseId){
            $http.get('/admin/bubblePLE/filter/parent/'+courseId).then(function(response) {
                var bubbles = new Array();
                var items = response.data.bubbles;
                var edges = response.data.edges;
                for (var i = 0; i < items.length; i++){
                    bubbles.push({id: items[i].id});
                    bubbles[i].label = items[i].title;
                    bubbles[i].title = items[i].title;
                }
                for (var i = 0; i < edges.length; i++){
                    edges[i].arrows = 'to';
                }
                var nodes = new vis.DataSet(bubbles);

                var edges = new vis.DataSet(edges);

                // create a network
                var container = document.getElementById('bubbles');

                // provide the data in the vis format
                var data = {
                    nodes: nodes,
                    edges: edges
                };

                // initialize your network!
                var network = new vis.Network(container, data, options);
                //network.on('doubleClick', function(node){
                //    if (node.nodes[0]){
                //        if (isCourse(node.nodes[0], items)){
                //            getAttachments(node.nodes[0]);
                //        }
                //    }
                //});
                visualize(nodes, edges, network);
            }, function(errResponse) {
                console.log('Error fetching data!');
                $mdToast.show(
                    $mdToast.simple()
                        .textContent('Error fetching courses')
                        .position('bottom')
                        .hideDelay(3000)
                );
            });
        }

        
        function getTemplate(type){
            var template = "";
            if(type === 'Bubble'){
                template =  '<md-dialog aria-label="List dialog">' +
                            '  <md-dialog-content>'+
                            '    <br>'+
                            '    <md-input-container>'+
                            '        <label>Bubble Name</label>'+
                            '        <input type="text" ng-model="bubbleName">'+
                            '    </md-input-container>'+
                            '  </md-dialog-content>' +
                            '  <md-dialog-actions>' +
                            '    <md-button ng-click="addingNewNode()" class="md-primary">' +
                            '      Add Bubble' +
                            '    </md-button>' +
                            '    <md-button ng-click="closeDialog()" class="md-primary">' +
                            '      Cancel' +
                            '    </md-button>' +
                            '  </md-dialog-actions>' +
                            '</md-dialog>';
            
            } else if(type === 'LinkAttachment') {
                template =  '<md-dialog aria-label="List dialog">' +
                            '  <md-dialog-content>'+
                            '    <br>'+
                            '    <md-input-container>'+
                            '        <label>Title</label>'+
                            '        <input type="text" ng-model="bubbleName">'+
                            '    </md-input-container>'+
                            '    <md-input-container>'+
                            '        <label>URL</label>'+
                            '        <input type="text" ng-model="url">'+
                            '    </md-input-container>'+
                            '  </md-dialog-content>' +
                            '  <md-dialog-actions>' +
                            '    <md-button ng-click="addingNewNode()" class="md-primary">' +
                            '      Add Link' +
                            '    </md-button>' +
                            '    <md-button ng-click="closeDialog()" class="md-primary">' +
                            '      Cancel' +
                            '    </md-button>' +
                            '  </md-dialog-actions>' +
                            '</md-dialog>';
            } 
            
            return template;
        }
        
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

    function visualize(nodes, edges, network){

        //var a = $location.search();
        //console.log(a);
        //$scope.courseName = nodes[a.courseId].label;

        $scope.addNewBubble = function (){
            bubbleType = 'Bubble';    
            network.addNodeMode();
        };

        $scope.addNewEdge = function (){
            $mdToast.show(
                $mdToast.simple()
                    .textContent("Drag a node from any Bubble!")
                    .position('bottom')
                    .hideDelay(3000)
            );

            network.addEdgeMode();
        };
        
        $scope.addLinkBubble = function (){
            bubbleType = 'LinkAttachment';
            network.addNodeMode();
        };
        
        $scope.addDocumentBubble = function (){
            bubbleType = 'FileAttachment';
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
                    '           <label>Add Note</label>'+ //Additional Line
                    '           <textarea ng-model="node.text" placeholder="Add Note">' +
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
            url: '/admin/bubblePLE/fileAttachments/rest',
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

              var filePath = String(response.data.item.filename);
              var res = filePath.split("/files/fileattachment/");
              addFileNode(res[1], filePath);
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


         function getFileExtension(filename){
          return filename.substr(filename.lastIndexOf('.')+1);
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

       function addFileNode(name, path){
                  var iconCode ;
                  var newId = nodes.length + 1;
                  console.log("adding file node : " + name);
                  
                  if(getFileExtension(path) == 'jpeg' || getFileExtension(path) == 'jpg' || getFileExtension(path) == 'png'){
                    nodes.update({id : newId, label : name, title : 'Uploaded File', shape : 'circularImage', image : path});
                  }
                  else{

                    if(getFileExtension(path) == 'pdf'){iconCode = '\uf1c1';}
                    else if(getFileExtension(path) == 'doc'){iconCode = '\uf1c2';}
                    else if(getFileExtension(path) == 'excel'){iconCode = '\uf1c3';}
                    else if(getFileExtension(path) == 'ppt'){iconCode = '\uf1c4';}
                    else if(getFileExtension(path) == 'txt'){iconCode = '\uf15c';}
                    else if(getFileExtension(path) == 'rar'){iconCode = '\uf1c6';}
                    else if(getFileExtension(path) == 'mp3'){iconCode = '\uf1c7';}
                    else if(getFileExtension(path) == 'mp4'){iconCode = '\uf1c8';}
                    else if(getFileExtension(path) == 'cpp'){iconCode = '\uf1c9';}
                    else{iconCode = '\uf15b';}

                    nodes.update({id: newId, label: name, title : 'Uploaded File', shape: 'icon', icon: {face: 'FontAwesome', code: iconCode, size: 50, color: '#f0a30a'}});
                  }
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
        }

    }]);