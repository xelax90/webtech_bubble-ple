  /**
   * * Created by Waqar Ahmed on 04/02/16.
   */
  'use strict';

  app.controller('nodeCtrl', ['$mdSidenav', '$location', '$scope', '$timeout', 'Upload', '$mdToast', '$mdDialog', '$http', '$anchorScroll', 'networkService', '$rootScope', function($mdSidenav, $location, $scope, $timeout, Upload, $mdToast, $mdDialog, $http, $anchorScroll, networkService, $rootScope){

      $scope.loadingData = true;
      $scope.breadCrumbs = "Personalized Learning Environment";

      $scope.toggleList = function(){
        $mdSidenav('left').toggle();
      };

      $scope.showProgressBar = false;
      var bubbleType = 'Bubble';

      $http.get('admin/bubblePLE/semesters/rest').then(function(response) {
          var semId = response.data[0].id;
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

      function navigateCourse(courseID){
        console.log("navigateCourse: " + courseID);
        if(courseID){          
          getAttachments(courseID);
        }
      }

      //filter courses of one semester
      function getCourses(semesterId){
          $http.get('admin/bubblePLE/filter/parent/'+semesterId).then(function(response) {
              var bubbles = new Array();
              console.log(response);
              var items = response.data.bubbles;
              var edges = response.data.edges;

              $scope.loadingData = false;
              $scope.breadCrumbs = items[0].title;

              for (var i = 0; i < items.length; i++){
                  if ((items[i].bubbleType.search("Semester") != -1) || (isChild(items[i], semesterId))) {
                      if (items[i].bubbleType.search("Semester") != -1){
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


              //networkService.getNetwork().setData({nodes: bubbles, edges: edges});
              networkService.setNetworkData(bubbles, edges);
              networkService.initNetwork();

              

              networkService.getNetwork().on('doubleClick', function(node){
                var index = items.map(function(el) {
                  return el.id;
                }).indexOf( parseInt(node.nodes[0]) );
                console.log(items[index].title);
                $scope.breadCrumbs += " > " + items[index].title;

                  if (node.nodes[0]){
                      if (isCourse(node.nodes[0], items)){
                          getAttachments(node.nodes[0]);
                      }
                  }
              });
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
                      return true;
                  }
              }
          }
          return false;
      }

      function getAttachments(courseId){
          $http.get('admin/bubblePLE/filter/parent/'+courseId).then(function(response) {
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
                      bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title, color: '#ffc966', font: {face: 'Verdana, Geneva, sans-serif'}});
                  }
                  else if (items[i].bubbleType.search("L2PMaterialAttachment") != -1) {
                      bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title, color: '#C2FABC', font: {face: 'Verdana, Geneva, sans-serif'}});
                  } else {
                      bubbles.push({id: items[i].id, label: items[i].title, title: items[i].title, font: {face: 'Verdana, Geneva, sans-serif'}});
                  }
              }
              console.log(items);
              for (var i = 0; i < edges.length; i++){
                  edges[i].arrows = 'to';
              }

              //networkService.getNetwork().setData({nodes: bubbles, edges: edges});
              networkService.setNetworkData(bubbles, edges);
              networkService.initNetwork();
              networkService.getNetwork().on('doubleClick', function(item){
                  if (isL2Plink(item.nodes[0], items)!= false) {
                      window.location = isL2Plink(item.nodes[0], items);
                  }
              });

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
                  }
              }
          }
          return false;
      }

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
                      var selectedNodes = networkService.getNetwork().getSelectedNodes();
                      for(var i = 0; i < selectedNodes.length; i++){
                          networkService.getNodes().update({id: selectedNodes[i], label: $scope.node.text});
                      }
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
      function onDoubleClick(properties) {
          doubleClickTime = new Date();
          console.log(properties);

          var nodeId = properties.nodes[0];
          var node = networkService.getNodes().get(nodeId);
          var filename = node.label;
          console.log("in double click");
          //downloadFile(filename);
          fileExist($http, filename);
      }


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