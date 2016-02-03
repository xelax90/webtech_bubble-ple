 function getTemplate(type){
            var template = "";
            if(type === 'Bubble'){
                template =  '<md-dialog aria-label="List dialog">' +
                            '  <md-dialog-content>'+
                            '    <br>'+
                            '    <md-input-container>'+
                            '        <label>Bubble Name</label>'+
                            '        <input type="text" ng-model="bubbleName">'+
                            '    </md-input-container>'+
                            '  </md-dialog-content>' +
                            '  <md-dialog-actions>' +
                            '    <md-button ng-click="addingNewNode()" class="md-primary">' +
                            '      Add Bubble' +
                            '    </md-button>' +
                            '    <md-button ng-click="closeDialog()" class="md-primary">' +
                            '      Cancel' +
                            '    </md-button>' +
                            '  </md-dialog-actions>' +
                            '</md-dialog>';
            
            } else if(type === 'LinkAttachment') {
                template =  '<md-dialog aria-label="List dialog">' +
                            '  <md-dialog-content>'+
                            '    <br>'+
                            '    <md-input-container>'+
                            '        <label>Title</label>'+
                            '        <input type="text" ng-model="bubbleName">'+
                            '    </md-input-container>'+
                            '    <md-input-container>'+
                            '        <label>URL</label>'+
                            '        <input type="text" ng-model="url">'+
                            '    </md-input-container>'+
                            '  </md-dialog-content>' +
                            '  <md-dialog-actions>' +
                            '    <md-button ng-click="addingNewNode()" class="md-primary">' +
                            '      Add Link' +
                            '    </md-button>' +
                            '    <md-button ng-click="closeDialog()" class="md-primary">' +
                            '      Cancel' +
                            '    </md-button>' +
                            '  </md-dialog-actions>' +
                            '</md-dialog>';
            } 
            
            return template;
        }


        function getSearchDialogTemplate(){
            return '<md-dialog aria-label="List dialog">' +
                    '  <md-dialog-content>'+
                    '    <br>'+
                    '    <md-input-container>'+
                    '        <label>Enter title to search</label>'+
                    '        <input type="text" ng-model="searchTitle">'+
                    '    </md-input-container>'+
                    '  </md-dialog-content>' +
                    '  <md-dialog-actions>' +
                    '    <md-button ng-click="search()" class="md-primary">' +
                    '      Search' +
                    '    </md-button>' +
                    '    <md-button ng-click="closeDialog()" class="md-primary">' +
                    '      Cancel' +
                    '    </md-button>' +
                    '  </md-dialog-actions>' +
                    '</md-dialog>';
        }

        function getColorChangeDialogTemplate(){
            return '<md-dialog aria-label="List dialog">' +
                    '  <md-dialog-content>'+
                    '    <br>'+
                    '      <h2>Select Priority</h2>'+
                    '         <md-radio-group ng-model="priority">'+
                    '            <md-radio-button value="Very_Important" class="md-primary"> Very Important </md-radio-button>'+
                    '            <md-radio-button value="Important"> Important </md-radio-button>'+
                    '            <md-radio-button value="Normal"> Normal </md-radio-button>'+
                    '          </md-radio-group>'+
                    '  </md-dialog-content>' +
                    '  <md-dialog-actions>' +
                    '    <md-button ng-click="change()" class="md-primary">' +
                    '      Ok' +
                    '    </md-button>' +
                    '    <md-button ng-click="closeDialog()" class="md-primary">' +
                    '      Cancel' +
                    '    </md-button>' +
                    '  </md-dialog-actions>' +
                    '</md-dialog>';
        }

        function changeLabelDialogTemplate(){
            return '<md-dialog>' +
                    '   <md-dialog-content>'+
                    '       <md-input-container>' +
                    '           <textarea ng-model="node.text" placeholder="change label">' +
                    '           </textarea>' +
                    '       </md-input-container>'+
                    '   </md-dialog-content>' +
                    '   <md-dialog-actions>' +
                    '       <md-button ng-click="changeNodeLabel()" class="md-primary">' +
                    '           Save' +
                    '       </md-button>' +
                    '   </md-dialog-actions>' +
                    '</md-dialog>';
        }

        function addTextToNodeDialog(){
            return '<md-dialog>' +
                    '   <md-dialog-content>'+
                    '       <md-input-container>' +
                    '           <label>Add Note</label>'+ //Additional Line
                    '           <textarea ng-model="node.text" placeholder="Add Note">' +
                    '           </textarea>' +
                    '       </md-input-container>'+
                    '   </md-dialog-content>' +
                    '   <md-dialog-actions>' +
                    '       <md-button ng-click="addTextToNodes()" class="md-primary">' +
                    '           Save' +
                    '       </md-button>' +
                    '   </md-dialog-actions>' +
                    '</md-dialog>';
        }