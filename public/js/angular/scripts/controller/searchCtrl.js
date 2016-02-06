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

        var arr = [];

        if($scope.searchTitle === "") return;

        console.log('searching for ' + $scope.searchTitle);
        console.log(items._data);
        items = items._data;
        var i = 0;
        var isFound = false;
        for(var k in items){
          ///if(items[k].label.toLowerCase() === $scope.searchTitle.toLowerCase()){
           if(items[k].label.toLowerCase().indexOf($scope.searchTitle.toLowerCase()) > -1){
            console.log("hurray found");
            arr[i] = items[k].id;

            // networkService.getNetwork().selectNodes([items[k].id], true);
            // networkService.getNetwork().focus(items[k].id);
            isFound = true;
            i++;
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
  else{
    console.log(arr);
     networkService.getNetwork().selectNodes(arr, true);
     if(arr.length == 1)
      networkService.getNetwork().focus(arr[0]);
  }

  $mdDialog.hide();
};

$scope.closeDialog = function() {
    $mdDialog.hide();
};
}