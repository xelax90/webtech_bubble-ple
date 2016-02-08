/**
* Created by Waqar Ahmed on 04/02/16.
*/
app.service('networkService',['$http','$mdToast', 'bubbleService', function($http, $mdToast, bubbleService){
    var dialog;
    var bubbleType;

    this.orignalItems;
    this.nodes;
    this.edges;
    this.data;
    
    this.container;
    this.network = null;
    
    var isSemesterView = true;
    
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
        layout: {

        },
		physics: {
			barnesHut: {
				springLength: 240,
				gravitationalConstant: -5000,
			},
			maxVelocity: 25
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
                    callback(edgeData);

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

    this.setNetworkData = function(i, n, e){
        console.log("argument length : " + arguments.length);
        if(arguments.length == 3){
            this.orignalItems = arguments[0];
            this.setData(arguments[1], arguments[2]);
        }
        else{
            this.setData(arguments[0], arguments[1]);
        }
        
    }

     this.setData = function(n, e){
        this.nodes = new vis.DataSet(n);
        this.edges = new vis.DataSet(e);
        this.data = {
            nodes: this.nodes,
            edges: this.edges
        };
    }


    this.initNetwork = function(initializerCallback){
	    //console.log(printStackTrace());
        // initialize your network!

        if(this.nodes == null || this.edges == null){
            console.log("No nodes or edges");
            return;
        }
		
		if(!this.network){
			this.network = new vis.Network(container, this.data, options);
			if(initializerCallback){
				initializerCallback(this.network);
			}
		} else {
			this.network.setData(this.data);
			var that = this;
			setTimeout(function(){that.network.fit({animation: true}); }, 1000);
		}
		globalnetwork = this.network;
		globaldata = this.data;
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

    this.getOrignalItems = function(){
        return this.orignalItems;
    };

    this.setOrignalItems = function(items){
        this.orignalItems = items;
    };

    this.updateOrignalItems = function(item){
        this.orignalItems.push(item);
        console.log("added item");
        console.log(item);
    }
    
    this.getIsSemesterView = function(){
        return isSemesterView;
    }
    
    this.setIsSemesterView = function(isSemester){
        isSemesterView = isSemester;
    }
    
    this.createNode = function(bubble){
        var node = {
            id: bubble.id,
            label: bubble.title,
            title: bubble.title,
            font: {face: 'Verdana, Geneva, sans-serif'},
            bubbleType: bubble.bubbleType
        };

        if (bubble.posX) {
            node.x = bubble.posX;
            node.y = bubble.posY;
        }

        if (bubbleService.isSemester(bubble)) {
            node.color = '#004c99';
            node.font.color = 'white';
            node.font.size = 25;
            node.font.strokeWidth = 1;
            node.font.strokeColor = 'black';
        } else if (bubbleService.isCourse(bubble)) {
            // do not do this in semester
            if (!isSemesterView) {
                node.color = '#004c99';
                node.font.color = 'white';
                node.font.size = 25;
            }
        } else if (bubbleService.isL2PMaterialFolder(bubble)) {
            node.color = '#7BE141';
        } else if (bubbleService.isL2PAssignment(bubble)) {
            node.color = '#ffc966';
        } else if (bubbleService.isL2PMaterialAttachment(bubble)) {
            node.color = '#C2FABC';
            node.cid = bubble.parents[0];
        } else if (bubbleService.isAttachment(bubble)){
            node.color = '#e9fde7';
            node.cid = bubble.parents[0];
        }
        return node;
    }

    // initialize your network!
    //network = new vis.Network(container, data, options);

}]);