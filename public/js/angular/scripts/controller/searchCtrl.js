 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
   /*Controller to look into nodes to search for node*/
   function searchController($scope, $mdDialog, items, $mdToast, networkService) {
    console.log(items);
    console.log(items[1].label);
    $scope.searchTitle = "";
    $scope.items = items;

    console.log(networkService.getNetwork());

    /* This method will be called when user clicked on search button */
    $scope.search = function() {

        if($scope.searchTitle == "") return;

        var i = 1;
        var isFound = false;
        for(var item in items){
          if($scope.items[i].label == $scope.searchTitle){
            console.log("hurray found");
            networkService.getNetwork().selectNodes([$scope.items[i].id], true);
            networkService.getNetwork().focus($scope.items[i].id);
            isFound = true;
        }
        i++;
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