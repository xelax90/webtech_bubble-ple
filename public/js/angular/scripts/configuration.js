 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
   var baseColor = {
    border: '#2B7CE9',
    background: '#97C2FC',
    highlight: {
      border: '#2B7CE9',
      background: '#D2E5FF'
    },
    hover: {
      border: '#2B7CE9',
      background: '#D2E5FF'
    }
  };


  var importantColor = {
    border: '#BCDB3A',
    background: '#D2F931',
    highlight: {
      border: '#D7E13C',
      background: '#F0FD32'
    },
    hover: {
      border: '#D7E13C',
      background: '#F0FD32'
    }
  };

  var v_importantColor = {
    border: '#D21E1E',
    background: '#F90000',
    highlight: {
      border: '#E43C3C',
      background: '#FF3232'
    },
    hover: {
      border: '#E43C3C',
      background: '#FF3232'
    }
  };



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
                dialog.show({
                    template: getTemplate(bubbleType),
                    locals: {
                      items: (data),
                      callBack : (callback)
                  },                                
                    controller: dialogController
                });
            }
        }
    };


function getNetworkOptions(){
  return options;
}
