 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
function dialogController($scope, $mdDialog, $mdToast, $http, items, callBack, networkService) {
    //$scope.addingNewNode = function() {
    //    var data = items;
    //    data.label = $scope.bubbleName;
    //    showToast($mdToast, 'Bubble Added: ' +  $scope.bubbleName);
    //
    //    $scope.bubbleName = "";
    //    $mdDialog.hide();
    //    callBack(data);
    //};
    //$scope.closeDialog = function() {
    //    networkService.getNetwork().disableEditMode();
    //    $mdDialog.hide();
    //};

     $scope.addingNewNode = function() {
         var req = {course: { title: $scope.bubbleName}};
         $http.post('/admin/bubblePLE/courses/rest', req).then(function(response){
             items.id = response.data.item.id;
             items.label = response.data.item.title;
             items.title = response.data.item.title;
             $mdToast.show(
                 $mdToast.simple()
                     .textContent('Bubble Added')
                     .position('bottom')
                     .hideDelay(3000)
             );
             callBack(items);

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
         networkService.getNetwork().disableEditMode();
         $mdDialog.hide();

     };
}