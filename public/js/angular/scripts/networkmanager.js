 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
function deleteNodeorEdge(networkService, $mdToast, $http){
	var selectedNodeId = networkService.getNetwork().getSelectedNodes();
    var selectedEdgeId = networkService.getNetwork().getSelectedEdges();

    console.log("Deleting Node: " + selectedNodeId);
    console.log("Deleting Node: " + selectedEdgeId);

    var toastMessage = '';
    if(selectedEdgeId){
        $http.delete('admin/bubblePLE/edges/rest/'+ selectedEdgeId).then(function(response){
            networkService.getNetwork().deleteSelected();
            toastMessage += 'Deleted ' + selectedEdgeId.length + ' Edge(s) and ';
        }, function(errResponse){
            $mdToast.simple()
                .textContent('Error while deleting edge!')
                .position('bottom')
                .hideDelay(3000);
        });
    }
    if(selectedNodeId){
         $http.delete('admin/bubblePLE/bubbles/rest/'+ selectedNodeId).then(function(response){
             networkService.getNetwork().deleteSelected();
             toastMessage += 'Bubble(s) ' + selectedNodeId;
             showToast($mdToast, 'Deleted: '+ toastMessage);
         }, function(errResponse){
             $mdToast.simple()
                 .textContent('Error while deleting node!')
                 .position('bottom')
                 .hideDelay(3000);
         });
    }
}