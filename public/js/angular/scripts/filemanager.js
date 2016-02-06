 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
   function uploadFile($scope, $mdToast, $timeout, file, Upload, networkService){
   	if(!file) return;

   	console.log("in file select");

   	console.log(file.name);

   	$scope.showProgressBar = true;

   	file.upload = Upload.upload({
   		url: '/admin/bubblePLE/fileAttachments/rest',
   		data: {fileattachment: {filename: file, title: file.name}},
   	});


   	file.upload.progress(function(evt){
   		console.log('percent: ' +parseInt(100.0 * evt.loaded / evt.total));
   	});


   	file.upload.then(function (response) {
   		$timeout(function () {
   			file.result = response.data;
   			console.log(response);
   			showToast($mdToast, 'File Uploaded Successfully');
   			console.log(response.data.item.filename);

   			var filePath = String(response.data.item.filename);
   			var res = filePath.split("/files/fileattachment/");
   			addFileNode(res[1], filePath, networkService);
   			$scope.showProgressBar = false;
   		});
   	}, function (response) {
   		if (response.status > 0)
   			$scope.errorMsg = response.status + ': ' + response.data;
   		console.log("in response");
   		console.log(response);
   		showToast($mdToast, 'Error Uploading File');
   		$scope.showProgressBar = false;
   	}, function (evt) {
	            // Math.min is to fix IE which reports 200% sometimes
	            file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
	            console.log(file.progress);
	            $scope.progressBarValue = file.progress;
	        });
   }


   function addFileNode(name, path, networkService){
   	var iconCode ;
   	var newId = networkService.getNodes().length + 1;
   	console.log("adding file node : " + name);
   	
   	if(getFileExtension(path) == 'jpeg' || getFileExtension(path) == 'jpg' || getFileExtension(path) == 'png'){
   		networkService.getNodes().update({id : newId, label : name, title : 'Uploaded File', shape : 'circularImage', image : path});
   	}
   	else{

   		if(getFileExtension(path) == 'pdf'){iconCode = '\uf1c1';}
   		else if(getFileExtension(path) == 'doc'){iconCode = '\uf1c2';}
   		else if(getFileExtension(path) == 'excel'){iconCode = '\uf1c3';}
   		else if(getFileExtension(path) == 'ppt'){iconCode = '\uf1c4';}
   		else if(getFileExtension(path) == 'txt'){iconCode = '\uf15c';}
   		else if(getFileExtension(path) == 'rar'){iconCode = '\uf1c6';}
   		else if(getFileExtension(path) == 'mp3'){iconCode = '\uf1c7';}
   		else if(getFileExtension(path) == 'mp4'){iconCode = '\uf1c8';}
   		else if(getFileExtension(path) == 'cpp'){iconCode = '\uf1c9';}
   		else{iconCode = '\uf15b';}

   		networkService.getNodes().update({id: newId, label: name, title : 'Uploaded File', shape: 'icon', icon: {face: 'FontAwesome', code: iconCode, size: 50, color: '#f0a30a'}});
   	}
   	var selectedNode = networkService.getNetwork().getSelectedNodes();
   	console.log("nodes lenght : " + selectedNode.length);
   	if(selectedNode.length > 0){
   		for(var i= 0; i < selectedNode.length; i++){
   			console.log(selectedNode[i]);
   			networkService.getEdges().update({from: newId, to: selectedNode[i]});
   		}
   	}
   }


   function addNode(name, networkService){
   	var newId = nodes.length + 1;
   	networkService.getNodes().update({id: newId, label: name, title: 'Uploaded file'});

   	var selectedNode = networkService.getNetwork().getSelectedNodes();
   	console.log("nodes lenght : " + selectedNode.length);
   	if(selectedNode.length > 0){
   		for(var i= 0; i < selectedNode.length; i++){
   			console.log(selectedNode[i]);
   			networkService.getEdges().update({from: newId, to: selectedNode[i]});
   		}
   	}
   };




   /*Check if file exist then download. It is to make sure that the click bubble is a file not a text*/
   function fileExist($http, filename){

   	var fileattachmentPath = "/files/fileattachment/";
   	var completePath = fileattachmentPath + filename;

            //head is use just to check whether file exist or not instead of get which is used to get the content
            $http.head(completePath)
            .success(function(data, status){
            	if(status == 200 ){
            		console.log("file found");
            		downloadFile(filename);
            	}else{
            		return false;
            	}
            })
            .error(function(data,status){
            	console.log("error");
            	if(status==200){}
            		else{}
            			return false;
            	});
        }



        /*Serve file to download*/
        function downloadFile(name, filePath){

        	// var fileattachmentPath = "/files/fileattachment/";
        	// var completePath = fileattachmentPath + filename;

        	var hiddenElement = document.createElement('a');
        	hiddenElement.href = filePath;
        	hiddenElement.target = '_blank';
        	hiddenElement.download = name;
        	hiddenElement.click();
        }


