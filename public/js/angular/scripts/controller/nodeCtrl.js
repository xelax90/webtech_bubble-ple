  /**
   * * Created by Waqar Ahmed on 04/02/16.
   */
  'use strict';

  app.controller('nodeCtrl', ['$mdSidenav', '$location', '$scope', '$timeout', 'Upload', '$mdToast', '$mdDialog', '$http', '$anchorScroll', 'networkService', '$rootScope', function($mdSidenav, $location, $scope, $timeout, Upload, $mdToast, $mdDialog, $http, $anchorScroll, networkService, $rootScope){

      $scope.myFile;

      $scope.currentCourseId;

      $scope.loadingData = true;
      $scope.breadCrumbsParent = "Personal Learning Environment";
      $scope.breadCrumbsChild;

      $scope.bcSemesterId;
      $scope.bcCourseId;

      $scope.toggleList = function(){
        $mdSidenav('left').toggle();
      };

      $scope.showProgressBar = false;
      var bubbleType = 'Bubble';

      $http.get('admin/bubblePLE/semesters/rest').then(function(response) {
          $scope.semesters = response.data;
          var semId = response.data[0].id;
          $scope.bcSemesterId = semId;

          $scope.loadingData = true;
          getCourses(semId);
          networkService.setmdDialog($mdDialog);
      }, function(errResponse) {
          console.log('Error fetching data!');
          $mdToast.show(
              $mdToast.simple()
                  .textContent('Error fetching semester')
                  .position('bottom')
                  .hideDelay(3000)
          );
      });

      $scope.announceSemester = function(sId){
          $scope.breadCrumbsChild = "";
          $scope.bcSemesterId = sId;

          $scope.loadingData = true;
          getCourses(sId);
          networkService.setmdDialog($mdDialog);
      };
	  
	  var networkInitializer = function(network){
            network.on('doubleClick', onDoubleClick);
            network.on("selectNode", function(params) {
				if (params.nodes.length == 1) {
					if (network.isCluster(params.nodes[0]) == true) {
						network.openCluster(params.nodes[0]);
						network.setOptions({physics:{stabilization:{fit: false}}});
						network.stabilize();
					}
				}
			});
	  }
	  
      //filter courses of one semester
      function getCourses(semesterId){
		  console.log('getCourses');
          $http.get('admin/bubblePLE/filter/parent/'+semesterId).then(function(response) {
              var bubbles = new Array();
              console.log(response);
              var items = response.data.bubbles;
              var edges = response.data.edges;

              $scope.loadingData = false;

              for (var i in items){
                  if ((items[i].bubbleType.search("Semester") != -1) || (isChild(items[i], semesterId))) {
                      if (items[i].bubbleType.search("Semester") != -1){
                          $scope.bcSemesterId = items[i].id;
                          $scope.breadCrumbsParent = items[i].title;
                          bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title, color: '#004c99', font: {color: 'white', size: 25, strokeWidth: 1, strokeColor: 'black', face: 'Verdana, Geneva, sans-serif'}});
                      }
                      else {
                        bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title, font:{face: 'Verdana, Geneva, sans-serif'}});
                      }
                  }
              }
              for (var i = 0; i < edges.length; i++){
                  edges[i].arrows = 'to';
              }

              //var nodes = new vis.DataSet(bubbles);
              //var edges = new vis.DataSet(edges);

              networkService.setNetworkData(items, bubbles, edges);
              networkService.initNetwork(networkInitializer);

              //networkService.getNetwork().setData({nodes: bubbles, edges: edges});

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


       function onDoubleClick(node){
                this.items = networkService.getNodes();
                var nodeId = node.nodes[0];
                var node = this.items._data[nodeId];

                console.log("this double click called");
                console.log(nodeId);

                if (nodeId){
                  if (isCourse(nodeId, networkService.getOrignalItems())){

                    $scope.bcCourseId = node.id;
                    $scope.breadCrumbsChild = node.title;
                    
                    $scope.currentCourseId = nodeId;

                    $scope.loadingData = true;
                    getAttachments(nodeId);
                    return;
                  }
                  else{
                    console.log(getOrignalNode(nodeId));
                    if(getOrignalNode(nodeId).bubbleType.search("MediaAttachment") != -1){
                      console.log("yeah it is a video");
                    }
                    if(isLinkAttachment(nodeId, networkService.getOrignalItems()) != false){
                      console.log("in link attac");
                      window.open(isLinkAttachment(nodeId, networkService.getOrignalItems()), '_blank');
                    }
                    if(isFile(nodeId, networkService.getOrignalItems())){
                      window.open(isFile(nodeId, networkService.getOrignalItems()), '_blank');
                      //window.location.assign(isFile(nodeId, networkService.getOrignalItems()));
                    }
                    if (isL2Plink(nodeId, networkService.getOrignalItems())!= false) {
                      console.log("in l20 link attac");
                        window.location = isL2Plink(nodeId, networkService.getOrignalItems());
                    }
                  }
                }



       }              


      function getOrignalNode(nodeId){
        var allNodes = networkService.getOrignalItems();
        for (var i = 0; i < allNodes.length; i++){
          if(allNodes[i].id == nodeId) return allNodes[i];  
        }
       }

      function isChild(Node, parentId){
            for (var i = 0; i < Node.parents.length; i++){
                if (Node.parents[i] == parentId){
                    return true;
                }
            }
          return false;
      }

      function isCourse(id, items){
          for (var i = 0; i < items.length; i++){
              if (items[i].id == id){
                  if (items[i].bubbleType.search("Course") != -1) {
                      console.log("it is course");
                      return true;
                  }
              }
          }
          return false;
      }

      function getAttachments(courseId){
		  console.log('getAttachments');
          $http.get('admin/bubblePLE/filter/parent/'+courseId).then(function(response) {

              $scope.loadingData = false;
              var bubbles = new Array();
              var items = response.data.bubbles;
              var edges = response.data.edges;
              for (var i = 0; i < items.length; i++){
                  if (items[i].bubbleType.search("Course") != -1){
                      bubbles.push({id: items[i].id, label:items[i].title, title: items[i].title, color: '#004c99', font:{color: 'white', face: 'Verdana, Geneva, sans-serif', size: 25}});
                  }
                  else if (items[i].bubbleType.search("L2PMaterialFolder") != -1) {
                      bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title, color: '#7BE141', font: {face: 'Verdana, Geneva, sans-serif'}});
                  }
                  else if (items[i].bubbleType.search("L2PAssignment") != -1) {
                      bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title,  color: '#ffc966', font: {face: 'Verdana, Geneva, sans-serif'}});
                  }
                  else if (items[i].bubbleType.search("L2PMaterialAttachment") != -1) {
                      bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title, cid: items[i].parents[0], color: '#C2FABC', font: {face: 'Verdana, Geneva, sans-serif'}});
                  } else {
                      bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title, font: {face: 'Verdana, Geneva, sans-serif'}});
                  }
              }
              for (var i = 0; i < edges.length; i++){
                  edges[i].arrows = 'to';
              }

              networkService.setNetworkData(bubbles, edges);
              networkService.initNetwork(networkInitializer);
                //function(item){

                  // if(isLinkAttachment(item.nodes[0], items) != false){
                  //     window.open(isLinkAttachment(item.nodes[0], items), '_blank');
                  // }
                  // if(isFile(item.nodes[0], items)){
                  //   downloadFile(orignalNode.title, orignalNode.filename);
                  // }
                  // if (isL2Plink(item.nodes[0], items)!= false) {
                  //     window.location = isL2Plink(item.nodes[0], items);
                  // }
              //});


              for (var i in bubbles){
                  if (!bubbles[i].cid && !isCourse(bubbles[i].id, items)){
                      var clusterOptionsByData = {
                          joinCondition:function(childOptions) {
                              return childOptions.cid == bubbles[i].id || childOptions.id == bubbles[i].id;
                          },
                          clusterNodeProperties: {id:'cidCluster' + bubbles[i].id, label: bubbles[i].label}
                      };
                      networkService.getNetwork().cluster(clusterOptionsByData);
                  }
              }

          }, function(errResponse) {
              $mdToast.show(
                  $mdToast.simple()
                      .textContent('Error fetching courses')
                      .position('bottom')
                      .hideDelay(3000)
              );
          });
      }

      function isL2Plink(nodeId, items){
          for (var i = 0; i < items.length; i++){
              if (items[i].id == nodeId){
                  if (items[i].bubbleType.search("L2PMaterialAttachment") != -1) {
                      return applicationBasePath + items[i].filename.substring(1);
                      //return items[i].filename.substring(1);
                  }
              }
          }
          return false;
      }
      function isLinkAttachment(nodeId, items){
          for (var i = 0; i < items.length; i++){
              if (items[i].id == nodeId){
                  if (items[i].bubbleType.search("LinkAttachment") != -1) {
                    console.log("returning link");
                    console.log(items[i].url);
                      return items[i].url;
                  }
              }
          }
          return false;
      }
      function isFile(nodeId, items){
          for (var i = 0; i < items.length; i++){
              if (items[i].id == nodeId){
                  if (items[i].bubbleType.search("FileAttachment") != -1) {
                      return applicationBasePath + items[i].filename.substring(1);
                  }
              }
          }
          return false;
      }

      $scope.clickBreadCrumbsParent = function(bcSemesterId){
        $scope.breadCrumbsChild = "";

        $scope.loadingData = true;
        getCourses(bcSemesterId);
      };

      $scope.clickBreadCrumbsChild = function(bcCourseId){
        $scope.loadingData = true;
        getAttachments(bcCourseId);
      };

      $scope.addNewBubble = function (){
          bubbleType = 'Bubble';
          showToast($mdToast, 'Click anywhere to add a Bubble');
          networkService.setBubbleType(bubbleType);
          networkService.getNetwork().addNodeMode();
      };

      $scope.addNewEdge = function (){
          showToast($mdToast, 'Drag edge from parent to child Bubble');
          networkService.getNetwork().addEdgeMode();
      };

      $scope.addLinkBubble = function (){
          bubbleType = 'LinkAttachment';
          networkService.setBubbleType(bubbleType);
          networkService.getNetwork().addNodeMode();
          $mdToast.show(
            $mdToast.simple()
            .textContent("Click anywhere to add a LinkBubble")
            .position('bottom')
            .hideDelay(3000)
          );
      };

      $scope.addDocumentBubble = function (){
          bubbleType = 'FileAttachment';
          networkService.setBubbleType(bubbleType);
          networkService.getNetwork().addNodeMode();
      };


      $scope.deleteSelectedNodeEdge = function (){
          deleteNodeorEdge(networkService, $mdToast, $http);
      };

      $scope.filUpload = function(){
        bubbleType = 'fileAttachment';
          showToast($mdToast, 'Click anywhere to add a Bubble for file');
          networkService.setBubbleType(bubbleType);
          networkService.getNetwork().addNodeMode();
      }

      $scope.mediaUpload = function(){
        bubbleType = 'mediaAttachment';
          showToast($mdToast, 'Click anywhere to add a Bubble for Media');
          networkService.setBubbleType(bubbleType);
          networkService.getNetwork().addNodeMode();
      }

      // for Opening the <form> to add text to node (UI hint : Edit Bubble)
      $scope.openTextBox = function(){
          if(networkService.getNetwork().getSelectedNodes().length > 0){
              var parentEl = angular.element(document.body);
              $mdDialog.show({
                parent: parentEl,
                template:addTextToNodeDialog(),

                clickOutsideToClose: true,
                // for saving the added text to the node
                controller: function($scope, $mdDialog){
                  $scope.addTextToNodes = function(){
                      var selectedNodes = networkService.getNetwork().getSelectedNodes();
                      for(var i = 0; i < selectedNodes.length; i++){
                          networkService.getNodes().update({id: selectedNodes[i], title: $scope.node.text});
                      }
                      $mdDialog.hide();
                  }
                }
              });
         }

      };

      // Dialog Box to share the selected bubble
      $scope.openShareBox = function($event){
          if(networkService.getNetwork().getSelectedNodes().length > 0) {
              $mdDialog.show({
                  targetEvent: $event,
                  template:
                  '<md-dialog aria-label="List dialog">' +
                  '  <md-dialog-content>' +
                  '     <div>Select Recipient User</div>' +
                  '     <br>' +
                  '     <md-input-container style="margin-right: 10px;">' +
                  '       <label>Target User</label>' +
                  '       <md-select ng-model="userId">' +
                  '       <md-option ng-repeat="user in users" value="{{user.id}}">{{user.name}}</md-option>' +
                  '     </md-select>' +
                  '   </md-input-container>' +
                  '  </md-dialog-content>' +
                  '  <md-dialog-actions>' +
                  '    <md-button ng-click="shareBubble()" class="md-primary">' +
                  '      Share' +
                  '    </md-button>' +
                  '    <md-button ng-click="closeDialog()" class="md-primary">' +
                  '      Close Dialog' +
                  '    </md-button>' +
                  '  </md-dialog-actions>' +
                  '</md-dialog>',
                  controller: ShareBubbleDialogController
              });
          } else {
              $mdToast.show(
                  $mdToast.simple()
                      .textContent('Select a Bubble you need to share.')
                      .position('bottom')
                      .hideDelay(3000)
              );
          }

          function ShareBubbleDialogController($scope, $mdDialog) {
              $http.get('admin/bubblePLE/usernames').then(function (response) {
                  $scope.users = response.data;
              }, function (errResponse) {
                  console.log('Error fetching Users!');
                  $mdToast.show(
                      $mdToast.simple()
                          .textContent('Error fetching Users')
                          .position('bottom')
                          .hideDelay(3000)
                  );
              });

              $scope.shareBubble = function(){
                  if($scope.userId){
                      var selectedBubble = networkService.getNetwork().getSelectedNodes();
                      console.log(selectedBubble);
                      angular.forEach(selectedBubble, function (value, key) {
                          $http.get('admin/bubblePLE/share/' + value + '/' + $scope.userId).then(function (response) {
                          //    Bubble Sharing Done
                          }, function (errResponse) {
                              console.log('Error sharing Bubble!');
                              $mdToast.show(
                                  $mdToast.simple()
                                      .textContent('Error sharing Bubble')
                                      .position('bottom')
                                      .hideDelay(3000)
                              );
                          });
                      });
                      $mdDialog.hide();
                      $mdToast.show(
                          $mdToast.simple()
                              .textContent('Bubble(s) shared!')
                              .position('bottom')
                              .hideDelay(3000)
                      );
                  }
              };

              $scope.closeDialog = function () {
                  $mdDialog.hide();
              }
          }
      };


      // for Opening the <form> to change label of the node
      $scope.openNodeChangeBox = function(){
          if(networkService.getNetwork().getSelectedNodes().length > 0){
              var parentEl = angular.element(document.body);
              $mdDialog.show({
                parent: parentEl,
                template:changeLabelDialogTemplate(),

                clickOutsideToClose: true,
                // for saving the added text to the node
                controller: function($scope, $mdDialog){
                  $scope.changeNodeLabel = function(){
                      var selectedNode = networkService.getNetwork().getSelectedNodes()[0];
                      var req = {bubble: {title: $scope.node.text}};
                      $http.post('admin/bubblePLE/bubbles/rest/'+selectedNode, req).then(function(response){
                          console.log(response);
                      });
                      networkService.getNodes().update({id: selectedNode, label: $scope.node.text, title: $scope.node.text});
                      $mdDialog.hide();
                  }
                }
              });
         }

      };

      /* added single and double click event to bubbles */
      //networkService.getNetwork().on('click', onClick);
      //networkService.getNetwork().on('doubleClick', onDoubleClick);

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
        console.log('single click');
      }

      /*If user double click on the bubble then this method will be called*/
      // function onDoubleClick(properties) {
      //     doubleClickTime = new Date();
      //     console.log(properties);

      //     var nodeId = properties.nodes[0];
      //     var node = networkService.getNodes().get(nodeId);
      //     var filename = node.label;
      //     console.log("in double click");
      //     //downloadFile(filename);
      //     fileExist($http, filename);
      // }


      //trigger onFileSelect method on clickUpload button clicked
      // $scope.clickUpload = function(){
      //     document.getElementById('i_file').click();
      // };

      // // Upload actual file to the server
       $scope.onFileSelect = function(file) {
        //uploadFile($scope, $mdToast, $timeout, file, Upload, networkService);
        console.log("in main mehtod");
        $scope.$emit('uploadFileEvent', [file]);
        console.log("emitte");
        console.log(file);
      }

      /* Search node in network */
      $scope.searchNode = function (){
        console.log("searching node");
        console.log(networkService.getNodes());
          $mdDialog.show({
              template: getSearchDialogTemplate(),    //template is in dialogtemplate file
              locals: {
                  items: (networkService.getNodes())
              },
              controller: searchController
          });
      };


      /* Change Color of network */
      $scope.changeColor = function (){

          if (networkService.getNetwork().getSelectedNodes().length == 0){
            showToast($mdToast, 'Please select a node first');
            return;
          }

          $mdDialog.show({
              template: getColorChangeDialogTemplate(),   //template is in dialogtemplate file
              locals: {
                  items: (networkService.getNodes())
              },
              controller: colorChangeCtrl
          });
              
          };
          
          /* Collapsing sidebar */
          $scope.collapse_sidebar = function(){
            var icon_labels = document.getElementsByClassName('icon-label');
            var icons = document.getElementsByClassName('sidebar-menu')[0].getElementsByTagName('md-icon');
            var sidebar = document.getElementsByTagName('md-sidenav')[0]
            var logo_label = document.getElementsByClassName('logo-label')[0]
            for (var i = 0; i < icon_labels.length; i++) { 
                if(icon_labels[i].style.opacity === ''){
                    icon_labels[i].style.opacity = 0;
                    icons[i].style.background = 'none';
                    icons[i].style.color = 'rgba(0,0,0,0.54)';
                    
                } else {
                    icon_labels[i].style.opacity = '';
                    icons[i].style.background = '#5c6bc0';
                    icons[i].style.color = '#ffffff';
                }
            }
            
            if (sidebar.style.width === ''){
                sidebar.style.width = '90px';
                logo_label.style.display = 'none';
            } else {
                sidebar.style.width = '';
                logo_label.style.display = 'inline-block';
            }
            
        };

  }]);