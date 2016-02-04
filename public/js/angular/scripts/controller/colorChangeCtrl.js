 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
/*Controller to look into nodes to search for node*/
function colorChangeCtrl($scope, $mdDialog, items, networkService) {
  $scope.searchTitle = "";
  $scope.items = items;


  /* This method will be called when user clicked on search button */
  $scope.change = function() {

    var selectedNode = networkService.getNetwork().getSelectedNodes();
    console.log("nodes lenght : " + selectedNode.length);
    if(selectedNode.length > 0){
      for(var i= 0; i < selectedNode.length; i++){
        console.log($scope.priority);
        if($scope.priority == "Very_Important")
          networkService.getNodes().update({id : selectedNode[i], color : v_importantColor});
        else if($scope.priority == "Important")
          networkService.getNodes().update({id : selectedNode[i], color : importantColor});
        else
          networkService.getNodes().update({id : selectedNode[i], color : baseColor});
      }
    }
    $mdDialog.hide();
  };

  $scope.closeDialog = function() {
    $mdDialog.hide();
  };
}
