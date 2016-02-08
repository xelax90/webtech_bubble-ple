  /**
   * Created by Waqar Ahmed on 04/02/16.
   */
 function getTemplate(type){
    var template = "";
    if(type === 'Bubble'){
        template =  '<md-dialog aria-label="List dialog">' +
        '  <md-toolbar>' +
        '     <div class="md-toolbar-tools">' +
        '      <h2>Add Bubble</h2>' +
        '      <span flex></span>' +
        '    </div>' +
        '  </md-toolbar>' +
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
        '  <md-toolbar>' +
        '     <div class="md-toolbar-tools">' +
        '      <h2>Add Link to Bubble</h2>' +
        '      <span flex></span>' +
        '    </div>' +
        '  </md-toolbar>' +
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
    else if(type === "fileAttachment") {
        template = uploadFileDialogTemplate(); 
    }
    else if(type === "mediaAttachment") {
        template = uploadMediaDialogTemplate(); 
    }

    return template;
}


function getSearchDialogTemplate(){
    return '<md-dialog aria-label="List dialog">' +
    '  <md-toolbar>' +
    '     <div class="md-toolbar-tools">' +
    '      <h2>Search Bubble</h2>' +
    '      <span flex></span>' +
    '    </div>' +
    '  </md-toolbar>' +
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
    '  <md-toolbar>' +
    '     <div class="md-toolbar-tools">' +
    '      <h2>Set Bubble Priority</h2>' +
    '      <span flex></span>' +
    '    </div>' +
    '  </md-toolbar>' +
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
    return '<md-dialog aria-label="List dialog">' +
    '  <md-toolbar>' +
    '     <div class="md-toolbar-tools">' +
    '      <h2>Change Bubble Label</h2>' +
    '      <span flex></span>' +
    '    </div>' +
    '  </md-toolbar>' +
    '   <md-dialog-content>'+
    '    <br>'+
    '    <md-input-container>' +
    '        <label>Change Label</label>'+
    '        <input type="text" ng-model="node.text">'+
    '    </md-input-container>'+
    '   </md-dialog-content>' +
    '   <md-dialog-actions>' +
    '       <md-button ng-click="changeNodeLabel()" class="md-primary">' +
    '           Save' +
    '       </md-button>' +
    '   </md-dialog-actions>' +
    '</md-dialog>';
}

function uploadFileDialogTemplate(){
    return '<md-dialog aria-label="List dialog">' +
        '  <md-toolbar>' +
        '     <div class="md-toolbar-tools">' +
        '      <h2>Upload Bubble File</h2>' +
        '      <span flex></span>' +
        '    </div>' +
        '  </md-toolbar>' +
        '  <md-dialog-content>'+
        '    <br>'+
        '    <md-input-container>'+
        '        <label>Title</label>'+
        '        <input type="text" ng-model="bubbleName">'+
        '    </md-input-container>'+
        '    <md-button ng-click="clickUpload()" class="md-primary">' +
        '      Browse' +
        '    </md-button>' +
        '  <md-dialog-actions>' +
        '    <md-button ng-click="uploadFile()" class="md-primary">' +
        '      Upload' +
        '    </md-button>' +
        '    <md-button ng-click="closeDialog()" class="md-primary">' +
        '      Cancel' +
        '    </md-button>' +
        '  </md-dialog-actions>' +
        '</md-dialog>';
}

function uploadMediaDialogTemplate(){
    return '<md-dialog aria-label="List dialog">' +
        '  <md-toolbar>' +
        '     <div class="md-toolbar-tools">' +
        '      <h2>Upload Bubble Media</h2>' +
        '      <span flex></span>' +
        '    </div>' +
        '  </md-toolbar>' +
        '  <md-dialog-content>'+
        '    <br>'+
        '    <md-input-container>'+
        '        <label>Title</label>'+
        '        <input type="text" ng-model="bubbleName">'+
        '    </md-input-container>'+
        '    <md-button ng-click="clickUpload()" class="md-primary">' +
        '      Browse' +
        '    </md-button>' +
         '<h2>OR</h2>' +
        '    <md-input-container>'+
        '        <label>Url</label>'+
        '        <input type="text" ng-model="mediaUrl" class="mediaurl">'+
        '    </md-input-container>'+
        '  <md-dialog-actions>' +
        '    <md-button ng-click="uploadFile()" class="md-primary">' +
        '      Upload' +
        '    </md-button>' +
        '    <md-button ng-click="closeDialog()" class="md-primary">' +
        '      Cancel' +
        '    </md-button>' +
        '  </md-dialog-actions>' +
        '</md-dialog>';
}


function PlayVideoDialogTemplate(title, url){
    return '<md-dialog aria-label="List dialog">' +
        '  <md-dialog-content>'+
        '    <br>'+
        '<video width="320" height="240" controls> '+
        '<source src="'+
        url+
        '" type="video/mp4"> '+
        'Your browser does not support the video tag. '+
        '        </video>'+
        '  </md-dialog-content>'+
        '  <md-dialog-actions>' +
        '    <md-button ng-click="closeMediaDialog()" class="md-primary">' +
        '      Cancel' +
        '    </md-button>' +
        '  </md-dialog-actions>' +
        '</md-dialog>';
}

function PlayYoutubeVideoDialogTemplate(title, url){
    return ' <md-dialog aria-label="Mango (Fruit)"  ng-cloak> <md-dialog-content><iframe width="420" height="345" src="'+
    url+
    '"></iframe></md-dialog-content><md-dialog-actions><md-button ng-click="closeMediaDialog()" class="md-primary">Cancel</md-button></md-dialog-actions></md-dialog>';
}


function addTextToNodeDialog(){
    return '<md-dialog>' +
    '  <md-toolbar>' +
    '     <div class="md-toolbar-tools">' +
    '      <h2>Add Bubble Text</h2>' +
    '      <span flex></span>' +
    '    </div>' +
    '  </md-toolbar>' +
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