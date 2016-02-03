function showToast($mdToast, message){
  $mdToast.show(
    $mdToast.simple()
    .textContent(message)
    .position('bottom')
    .hideDelay(3000)
    );
}


function getFileExtension(filename){
  return filename.substr(filename.lastIndexOf('.')+1);
}