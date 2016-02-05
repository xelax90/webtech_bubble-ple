/**
* Created by Waqar Ahmed on 04/02/16.
*/
app.service('networkService',['$http','$mdToast', function($http, $mdToast){
     
    var dialog;
    var bubbleType;

    this.nodes;
    this.edges;
    this.data;
    
    this.container;
    this.network = null;

    var options = {
        autoResize: true,
        locale: 'en',
        clickToUse: false,
        interaction:{
            dragNodes:true,
            dragView: true,
            hideEdgesOnDrag: false,
            hideNodesOnDrag: false,
            hover: true,
            hoverConnectedEdges: true,
            keyboard: {
                enabled: false,
                speed: {x: 10, y: 10, zoom: 0.02},
                bindToWindow: true
            },
            multiselect: true,
            navigationButtons: true,
            selectable: true,
            selectConnectedEdges: true,
            tooltipDelay: 300,
            zoomView: true
        },
        manipulation:{
            enabled: false,
            addNode: function(data, callback){
                dialog.show({
                    template: getTemplate(bubbleType),
                    locals: {
                          items: (data),
                          callBack : (callback),
                          type: (bubbleType)
                  },                                
                    controller: dialogController
                });
            },
            addEdge: function(edgeData,callback) {
                edgeData.arrows = 'to';
                var req = {edge: {from: edgeData.from, to: edgeData.to}};
                $http.post('/admin/bubblePLE/edges/rest', req).then(function(response){
                    console.log(response);
                    $mdToast.show(
                        $mdToast.simple()
                            .textContent('Bubbles connected.')
                            .position('bottom')
                            .hideDelay(3000)
                    );
                    callback(data);

                }, function(errResponse){
                    $mdToast.show(
                        $mdToast.simple()
                            .textContent('Error connectiong Bubbles!')
                            .position('bottom')
                            .hideDelay(3000)
                    );
                });
                callback(edgeData);

            }
        }
    };

    

    this.setmdDialog = function(mdDialog){
        dialog = mdDialog;
    };

    this.setBubbleType = function(type){
        bubbleType = type;
    };

    options.nodes = {
      color : baseColor
     };

    //nodes = new vis.DataSet([
    //    {id: 1, label: 'Node 1'},
    //    {id: 2, label: 'Node 2'},
    //    {id: 3, label: 'Node 3'},
    //    {id: 4, label: 'Node 4'},
    //    {id: 5, label: 'Node 5'}
    //]);

     //create an array with edges
    //edges = new vis.DataSet([]);

     //create a network
    container = document.getElementById('bubbles');

    this.setNetworkData = function(n, e){
        this.nodes = new vis.DataSet(n);
        this.edges = new vis.DataSet(e);
        this.data = {
            nodes: this.nodes,
            edges: this.edges
        };
        console.log("set network data");
    }

    this.initNetwork = function(){
        // initialize your network!
        console.log("in init network");

        if(this.nodes == null || this.edges == null){
            console.log("No nodes or edges");
            return;
        }

        console.log("initiliazing network");
        this.network = new vis.Network(container, this.data, options);
        //this.network.setData({nodes: n, edges: e});
        console.log("initialized");
    }

    this.getNetwork = function(){
    	return this.network;
    };

    this.getNodes = function(){
    	return this.nodes;
    };

    this.getEdges = function(){
    	return this.edges;
    };

    // initialize your network!
    //network = new vis.Network(container, data, options);

}]);