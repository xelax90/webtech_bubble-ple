/**
* Created by Waqar Ahmed on 04/02/16.
*/
app.service('networkService', function(){
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
        manipulation:
        {
            enabled: false,
            addNode: function(data, callback){
                $mdDialog.show({
                    template: getTemplate(bubbleType),                                
                    controller: dialogController
                });
            }
        }
    };


    options.nodes = {
      color : baseColor
  };

  var nodes = new vis.DataSet([
    {id: 1, label: 'Node 1'},
    {id: 2, label: 'Node 2'},
    {id: 3, label: 'Node 3'},
    {id: 4, label: 'Node 4'},
    {id: 5, label: 'Node 5'}
    ]);

            // create an array with edges
            var edges = new vis.DataSet([
                {from: 1, to: 3},
                {from: 1, to: 2},
                {from: 2, to: 4},
                {from: 2, to: 5}
                ]);

            //var a = $location.search();
            //console.log(a);
            //$scope.courseName = nodes[a.courseId].label;

            // create a network
            var container = document.getElementById('bubbles');

            // provide the data in the vis format
            var data = {
                nodes: nodes,
                edges: edges
            };

            // initialize your network!
            var network = new vis.Network(container, data, options);

            this.getNetwork = function(){
            	return network;
            }

            this.getNodes = function(){
            	return nodes;
            }

            this.getEdges = function(){
            	return edges;
            }
        });