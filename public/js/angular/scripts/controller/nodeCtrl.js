  /**
   * Created by Waqar Ahmed on 04/02/16.
   */
  'use strict';

  app.controller('nodeCtrl', ['$mdSidenav', '$location', '$scope', '$timeout', 'Upload', '$mdToast', '$mdDialog', '$http', '$anchorScroll', 'networkService', function($mdSidenav, $location, $scope, $timeout, Upload, $mdToast, $mdDialog, $http, $anchorScroll, networkService){

          $scope.toggleList = function(){
            $mdSidenav('left').toggle();
          };

          $scope.showProgressBar = false;
          var bubbleType = 'Bubble';
    
          networkService.setmdDialog($mdDialog);

          $scope.addNewBubble = function (){
              bubbleType = 'Bubble';
              networkService.setBubbleType(bubbleType);    
              networkService.getNetwork().addNodeMode();
          };

          $scope.addNewEdge = function (){
              showToast($mdToast, 'Manipulation Mode enabled, drag a node from any Bubble!');
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
              deleteNodeorEdge(networkService, $mdToast);
          };

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
          networkService.getNetwork().on('click', onClick);
          networkService.getNetwork().on('doubleClick', onDoubleClick);

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
          $scope.clickUpload = function(){
              document.getElementById('i_file').click();
          };

          // Upload actual file to the server
           $scope.onFileSelect = function(file) {
            uploadFile($scope, $mdToast, $timeout, file, Upload, networkService);
          
          }

          /* Search node in network */
          $scope.searchNode = function (){
              $mdDialog.show({
                  template: getSearchDialogTemplate(),    //template is in dialogtemplate file
                  locals: {
                      items: (networkService.getNodes()._data)
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
                      items: (networkService.getNodes()._data)
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