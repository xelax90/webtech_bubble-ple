 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
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