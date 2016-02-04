 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
   /*Controller to look into nodes to search for node*/
   function searchController($scope, $mdDialog, items, $mdToast, networkService) {
    $scope.searchTitle = "";
    $scope.items = items;

    console.log(items);

    /* This method will be called when user clicked on search button */
    $scope.search = function() {

        if($scope.searchTitle === "") return;

        console.log('searching for ' + $scope.searchTitle);

        var i = 1;
        var isFound = false;
        for(var k in items){
          if(items[k].label == $scope.searchTitle){
            console.log("hurray found");
            networkService.getNetwork().selectNodes([items[k].id], true);
            networkService.getNetwork().focus(items[k].id);
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