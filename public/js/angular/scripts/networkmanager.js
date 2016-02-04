 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
function deleteNodeorEdge(networkService, $mdToast){
	var selectedNodeId = networkService.getNetwork().getSelectedNodes();
            var selectedEdgeId = networkService.getNetwork().getSelectedEdges();

            console.log("Deleting Node: " + selectedNodeId);
            console.log("Deleting Node: " + selectedEdgeId);

            var toastMessage = '';
            if(selectedEdgeId){
                networkService.getNetwork().deleteSelected();
                toastMessage += 'Deleted ' + selectedEdgeId.length + ' Edge(s) and ';
            } if(selectedNodeId){
                networkService.getNetwork().deleteSelected();
                toastMessage += 'Bubble(s) ' + selectedNodeId;
            } if( selectedNodeId.length>0 || selectedEdgeId.length>0 ){
                console.log(":"+selectedNodeId + ":" + selectedEdgeId+":");
                showToast($mdToast, toastMessage);
            } else {
                showToast($mdToast, 'Please select a Bubble or an Edge!');
            }
}