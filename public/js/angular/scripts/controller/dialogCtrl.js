 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
function dialogController($scope, $mdDialog, $mdToast, items, callBack, networkService) {
    $scope.addingNewNode = function() {
        var data = items;
        data.label = $scope.bubbleName;
        showToast($mdToast, 'Bubble Added: ' +  $scope.bubbleName);

        $scope.bubbleName = "";
        $mdDialog.hide();
        callBack(data);
    };
    $scope.closeDialog = function() {
        networkService.getNetwork().disableEditMode();
        $mdDialog.hide();
    };
}