 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
function dialogController($scope, $mdDialog, $mdToast, $http, items, callBack, type, networkService, Upload, $timeout, fileUpload, $rootScope, fileService) {
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
         

         // if(type == 'fileattachment'){
         //    console.log("in type file fileAttachments"); 
         //    this.url = '/admin/bubblePLE/fileAttachments/rest';
                 
         // }

         if(type == 'mediaAttachment'){
            console.log("in type file fileAttachments");
            //req = {mediaAttachment: { title: $scope.bubbleName, fileLink : $scope.fileLink , url: $scope.mediaUrl}};
            //url = '/admin/bubblePLE/mediaAttachments/rest';       
         }
         
         else{
             $http.post(url, req).then(function(response){
                 console.log(response);
                 networkService.updateOrignalItems(response.data.item);
                 var node = networkService.createNode(response.data.item);
                 $mdToast.show(
                     $mdToast.simple()
                         .textContent('Bubble Added')
                         .position('bottom')
                         .hideDelay(3000)
                 );
                 callBack(node);

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
        var file = fileService[0];
        //$scope.bubbleName = file.name;
        console.log(file);

        if(type == 'fileAttachment'){
            data = {fileattachment: {filename: file, title: file.name}};
            this.url = '/admin/bubblePLE/fileAttachments/rest';
        }
        else if(type == 'mediaAttachment'){
            data = {mediaattachment: { title: $scope.bubbleName, fileLink : $scope.mediaUrl , filename: file}};
            this.url = '/admin/bubblePLE/mediaAttachments/rest'; 
        }
        console.log(data);
        console.log(this.url);

        var text;

        if($scope.bubbleName === ""){
            $scope.bubbleName = file.name;
        }

        if(file == null){
            $http.post(this.url, data).then(function(response){
                 console.log(response);
                 networkService.updateOrignalItems(response.data.item);
                 var node = networkService.createNode(response.data.item);
                 $mdToast.show(
                     $mdToast.simple()
                         .textContent('ADDED MEDIA')
                         .position('bottom')
                         .hideDelay(3000)
                 );
                 callBack(node);

             }, function(errResponse){
                 $mdToast.show(
                     $mdToast.simple()
                         .textContent('Error adding Bubble!')
                         .position('bottom')
                         .hideDelay(3000)
                 );
             });
        }
        else{

        file.upload = Upload.upload({
            url: this.url,
            data: data,
        });

        file.upload.then(function (response) {
        $timeout(function () {
            file.result = response.data;
            console.log(response);
            networkService.updateOrignalItems(response.data.item);
            showToast($mdToast, 'File Uploaded Successfully');
            console.log(response.data.item.filename);
            $scope.showProgressBar = false;
            var node = networkService.createNode(response.data.item);
            $mdToast.show(
                $mdToast.simple()
                    .textContent('Bubble Added')
                    .position('bottom')
                    .hideDelay(3000)
            );
            callBack(node);

        });
    }, function (response) {
        console.log("in error response");
        console.log(response);
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




        // $http.post(this.url, data, {
        //     transformRequest: angular.identity,
        //     headers: {'Content-Type': undefined}
        // })
        // .success(function(response){
        //     console.log("file uploaded");
        //     callBack(items);
        //     console.log(response);
        // })
        // .error(function(response){
        //     console.log("erro");
        //     console.log(response);
        // });
    };
}
