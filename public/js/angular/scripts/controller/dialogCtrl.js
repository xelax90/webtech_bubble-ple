 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
function dialogController($scope, $mdDialog, $mdToast, $http, items, callBack, type, networkService) {
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
         var req;
         var url;
         if (type == 'Bubble') {
             req = {bubble: { title: $scope.bubbleName}};
             url= '/admin/bubblePLE/bubbles/rest';
         }
         if (type == 'LinkAttachment'){
             req = {linkattachment: { title: $scope.bubbleName, url: $scope.url}};
             url = '/admin/bubblePLE/linkAttachments/rest';
         }
         $http.post(url, req).then(function(response){
             console.log(response);
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
