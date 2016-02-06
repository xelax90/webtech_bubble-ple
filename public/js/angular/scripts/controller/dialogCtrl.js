 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
function dialogController($scope, $mdDialog, $mdToast, $http, items, callBack, type, networkService, Upload, $timeout, fileUpload, $rootScope) {
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

     $scope.myFile = {};


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
         

         if(type == 'fileattachment'){
            console.log("in type file fileAttachments");       
         }
         
         else{
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
        }

         $scope.bubbleName = "";
         $mdDialog.hide();

     };
     $scope.closeDialog = function() {
         networkService.getNetwork().disableEditMode();
         $mdDialog.hide();

     };

     $scope.clickUpload = function(){
        console.log("click uplaod button click");
          setTimeout(function() {
            document.getElementById('i_file').click();
        }, 0);
          //$mdDialog.hide();
      };

     

      $scope.$on('uploadFileEvent',function(event,data){    
        $scope.myData = data;
        console.log("recevide");
        console.log(data);
    });

      $scope.$on('myFile', function (event, myFile) {
    $scope.myFile = myFile;
    console.log("adsdsad");
});

      $scope.uploadFile = function(){
        $mdDialog.hide();
        var file = $scope.myFile;
        var fd = new FormData();
        console.log($parent.myFile);
        this.url = '/admin/bubblePLE/fileAttachments/rest';
        data = {fileattachment: {filename: file, title: $scope.bubbleName}};
        $http.post(this.url, data, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
        .success(function(response){
            console.log("file uploaded");
            callBack(items);
            console.log(response);
        })
        .error(function(response){
            console.log("erro");
            console.log(response);
        });
    };



      // Upload actual file to the server
        function uploadFileToServer(file) {
        //uploadFile($scope, $mdToast, $timeout, file, Upload, networkService);
        console.log("file selected");
        


        return ;
        $scope.showProgressBar = true;
        console.log("hiding dialog");
            $mdDialog.hide();
            console.log("dialog hidden");
            file.upload = Upload.upload({
                url: '/admin/bubblePLE/fileAttachments/rest',
                data: {fileattachment: {filename: file, title: $scope.bubbleName}},
            });


            file.upload.progress(function(evt){
                console.log('percent: ' +parseInt(100.0 * evt.loaded / evt.total));
            });


            file.upload.then(function (response) {
                $timeout(function () {
                    file.result = response.data;
                    console.log(response);
                    showToast($mdToast, 'File Uploaded Successfully');
                    $scope.showProgressBar = false;
                    callBack(items);
                });
            }, function (response) {
                if (response.status > 0)
                    $scope.errorMsg = response.status + ': ' + response.data;
                    showToast($mdToast, 'Error Uploading File');
                    $scope.showProgressBar = false;
            }, function (evt) {
                        // Math.min is to fix IE which reports 200% sometimes
                        file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
                        console.log(file.progress);
                        $scope.progressBarValue = file.progress;
                    });
      }
}
