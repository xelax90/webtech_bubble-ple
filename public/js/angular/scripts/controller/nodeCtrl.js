/**
 * * Created by Waqar Ahmed on 04/02/16.
 */
'use strict';

app.controller('nodeCtrl', ['$mdSidenav', '$location', '$scope', '$timeout', 'Upload', '$mdToast', '$mdDialog', '$http', '$anchorScroll', 'networkService', '$rootScope', 'bubbleService', function ($mdSidenav, $location, $scope, $timeout, Upload, $mdToast, $mdDialog, $http, $anchorScroll, networkService, $rootScope, bubbleService) {
    $http.get('l2p/authenticate').then(function(response) {
        if (response.data['success'] === false) {
            $location.path('/login');
        }
        else {
            runApp();
        }
    });

    function runApp(){
        $scope.myFile;

        $scope.currentCourseId;

        $scope.loadingData = true;
        $scope.breadCrumbsParent = "Personal Learning Environment";
        $scope.breadCrumbsChild;

        $scope.bcSemesterId;
        $scope.bcCourseId;
        var onClickTimeout;

        $scope.toggleList = function () {
            $mdSidenav('left').toggle();
        };

        $scope.showProgressBar = false;
        var bubbleType = 'Bubble';

        $http.get('admin/bubblePLE/semesters/rest').then(function (response) {
            $scope.semesters = response.data;
            var semId = response.data[0].id;
            $scope.bcSemesterId = semId;

            $scope.loadingData = true;
            getCourses(semId);
            networkService.setmdDialog($mdDialog);
        }, function (errResponse) {
            $mdToast.show(
                $mdToast.simple()
                    .textContent('Error fetching semester')
                    .position('bottom')
                    .hideDelay(3000)
                );
        });

        $scope.announceSemester = function (sId) {
            $scope.breadCrumbsChild = "";
            $scope.bcSemesterId = sId;

            $scope.loadingData = true;
            getCourses(sId);
            networkService.setmdDialog($mdDialog);
        };

        var networkInitializer = function (network) {
            network.on('doubleClick', onDoubleClick);
            network.on("click", onClick);
        }

        //filter courses of one semester
        function getCourses(semesterId) {
            networkService.setIsSemesterView(true);
            $http.get('admin/bubblePLE/filter/parent/' + semesterId).then(function (response) {
                var bubbles = new Array();
                var items = response.data.bubbles;
                var edges = response.data.edges;

                $scope.loadingData = false;

                for (var i in items) {
                    var isSemester = bubbleService.isSemester(items[i]);
                    if (isSemester || (isChild(items[i], semesterId))) {
                        if (isSemester) {
                            $scope.bcSemesterId = items[i].id;
                            $scope.breadCrumbsParent = items[i].title;
                        }
                        bubbles.push(createNode(items[i]));
                    }
                }
                for (var i = 0; i < edges.length; i++) {
                    edges[i].arrows = 'to';
                }

                //var nodes = new vis.DataSet(bubbles);
                //var edges = new vis.DataSet(edges);

                networkService.setNetworkData(items, bubbles, edges);
                networkService.initNetwork(networkInitializer);

                //networkService.getNetwork().setData({nodes: bubbles, edges: edges});

            }, function (errResponse) {
                $mdToast.show(
                    $mdToast.simple()
                        .textContent('Error fetching courses')
                        .position('bottom')
                        .hideDelay(3000)
                    );
            });
        }


        function onDoubleClick(node) {
            clearTimeout(onClickTimeout);
            onClickTimeout = false;
            this.items = networkService.getNodes();
            var nodeId = node.nodes[0];
            var node = this.items.get(nodeId);

            if (nodeId) {
                if (isCourse(nodeId, networkService.getOrignalItems())) {

                    $scope.bcCourseId = node.id;
                    $scope.breadCrumbsChild = node.title;

                    $scope.currentCourseId = nodeId;

                    $scope.loadingData = true;
                    getAttachments(nodeId);
                    return;
                }
                else {
                    var myNode = getOrignalNode(nodeId);
                    if (bubbleService.isMediaAttachment(myNode)) {
                        var myTemplate;
                        if (myNode.filename.search("youtube") != -1)
                            myTemplate = PlayYoutubeVideoDialogTemplate(myNode.title, myNode.filename);
                        else
                            myTemplate = PlayVideoDialogTemplate(myNode.title, myNode.filename);
                        $mdDialog.show({
                            template: myTemplate,
                            controller: function ($scope, $mdDialog) {
                                $scope.closeMediaDialog = function () {
                                    $mdDialog.hide();
                                };
                            }
                        });

                        //window.open(myNode.filename, '_blank');
                    } else if(bubbleService.isLinkAttachment(myNode)) {
                        window.open(bubbleService.getLinkAttachmentUrl(myNode), '_blank');
                    } else if(bubbleService.isL2PMaterialAttachment(myNode)){
                        window.open(bubbleService.getFileAttachmentUrl(myNode), '_blank');
                    } else if (bubbleService.isFileAttachment(myNode)) {
                        window.open(bubbleService.getFileAttachmentUrl(myNode), '_blank');
                        //window.location.assign(isFile(nodeId, networkService.getOrignalItems()));
                    }
                }
            }



        }

        function getOrignalNode(nodeId) {
            var allNodes = networkService.getOrignalItems();
            for (var i = 0; i < allNodes.length; i++) {
                if (allNodes[i].id == nodeId)
                    return allNodes[i];
            }
        }

        function isChild(Node, parentId) {
            for (var i = 0; i < Node.parents.length; i++) {
                if (Node.parents[i] == parentId) {
                    return true;
                }
            }
            return false;
        }

        function isCourse(id, items) {
            for (var i = 0; i < items.length; i++) {
                if (items[i].id == id) {
                    if (bubbleService.isCourse(items[i])) {
                        return true;
                    }
                }
            }
            return false;
        }

        function createNode(bubble) {
            return networkService.createNode(bubble);
        }

        $scope.savePositions = function () {
            networkService.getNetwork().storePositions();
            var nodes = networkService.getNodes().get();
            var request = {bubbles: []};
            for (var i in nodes) {
                var node = nodes[i];
                var pos = networkService.getNetwork().getPositions(['cidCluster' + node.id]);
                if (pos['cidCluster' + node.id]) {
                    request.bubbles.push({id: node.id, x: pos['cidCluster' + node.id].x, y: pos['cidCluster' + node.id].y});
                } else {
                    request.bubbles.push({id: node.id, x: node.x, y: node.y});
                }
            }
            $http.post('admin/bubblePLE/updatePositions', request).then(function (response) {
                $mdToast.show(
                        $mdToast.simple()
                        .textContent('Layout saved!')
                        .position('bottom')
                        .hideDelay(3000)
                        );
            }, function (errResponse) {
                $mdToast.show(
                        $mdToast.simple()
                        .textContent('Error saving layout!')
                        .position('bottom')
                        .hideDelay(3000)
                        );
            });
        }

        function getAttachments(courseId) {
            networkService.setIsSemesterView(false);
            $http.get('admin/bubblePLE/filter/parent/' + courseId).then(function (response) {

                $scope.loadingData = false;
                var bubbles = new Array();
                var items = response.data.bubbles;
                var edges = response.data.edges;
                for (var i = 0; i < items.length; i++) {
                    bubbles.push(createNode(items[i]));
                }
                for (var i = 0; i < edges.length; i++) {
                    edges[i].arrows = 'to';
                }

                networkService.setNetworkData(bubbles, edges);
                networkService.initNetwork(networkInitializer);
                //function(item){

                // if(isLinkAttachment(item.nodes[0], items) != false){
                //     window.open(isLinkAttachment(item.nodes[0], items), '_blank');
                // }
                // if(isFile(item.nodes[0], items)){
                //   downloadFile(orignalNode.title, orignalNode.filename);
                // }
                // if (isL2Plink(item.nodes[0], items)!= false) {
                //     window.location = isL2Plink(item.nodes[0], items);
                // }
                //});


                for (var i in bubbles) {
                    makeCluster(bubbles[i], items);
                }


            }, function (errResponse) {
                $mdToast.show(
                        $mdToast.simple()
                        .textContent('Error fetching courses')
                        .position('bottom')
                        .hideDelay(3000)
                        );
            });
        }

        function makeCluster(bubble, items) {
            var needsCluster = !bubble.cid;
            if(bubble.bubbleType){
                needsCluster = needsCluster && !bubbleService.isCourse(bubble) && !bubbleService.isSemester(bubble);
            } else {
                needsCluster = needsCluster && !isCourse(bubble.id, items);
            }
            if (needsCluster) {
                var clusterOptionsByData = {
                    joinCondition: function (childOptions) {
                        return childOptions.cid == bubble.id || childOptions.id == bubble.id;
                    },
                    clusterNodeProperties: {id: 'cidCluster' + bubble.id, label: bubble.title}
                };
                if (bubble.x) {
                    clusterOptionsByData.clusterNodeProperties.x = bubble.x;
                    clusterOptionsByData.clusterNodeProperties.y = bubble.y;
                }
                networkService.getNetwork().cluster(clusterOptionsByData);
            }
        }

        function isL2Plink(nodeId, items) {
            for (var i = 0; i < items.length; i++) {
                if (items[i].id == nodeId) {
                    if (bubbleService.isL2PMaterialAttachment(items[i])) {
                        return applicationBasePath + items[i].filename.substring(1);
                        //return items[i].filename.substring(1);
                    }
                }
            }
            return false;
        }
        function isLinkAttachment(nodeId, items) {
            for (var i = 0; i < items.length; i++) {
                if (items[i].id == nodeId) {
                    if (bubbleService.isLinkAttachment(items[i])) {
                        return items[i].url;
                    }
                }
            }
            return false;
        }
        function isFile(nodeId, items) {
            for (var i = 0; i < items.length; i++) {
                if (items[i].id == nodeId) {
                    if (bubbleService.isFileAttachment(items[i])) {
                        return applicationBasePath + items[i].filename.substring(1);
                    }
                }
            }
            return false;
        }

        $scope.clickBreadCrumbsParent = function (bcSemesterId) {
            $scope.breadCrumbsChild = "";

            $scope.loadingData = true;
            getCourses(bcSemesterId);
        };

        $scope.clickBreadCrumbsChild = function (bcCourseId) {
            $scope.loadingData = true;
            getAttachments(bcCourseId);
        };

        $scope.addNewBubble = function () {
            bubbleType = 'Bubble';
            showToast($mdToast, 'Click anywhere to add a Bubble');
            networkService.setBubbleType(bubbleType);
            networkService.getNetwork().addNodeMode();
        };

        $scope.addNewEdge = function () {
            showToast($mdToast, 'Drag edge from parent to child Bubble');
            networkService.getNetwork().addEdgeMode();
        };

        $scope.addLinkBubble = function () {
            bubbleType = 'LinkAttachment';
            networkService.setBubbleType(bubbleType);
            networkService.getNetwork().addNodeMode();
            $mdToast.show(
                    $mdToast.simple()
                    .textContent("Click anywhere to add a LinkBubble")
                    .position('bottom')
                    .hideDelay(3000)
                    );
        };

        $scope.addDocumentBubble = function () {
            bubbleType = 'FileAttachment';
            networkService.setBubbleType(bubbleType);
            networkService.getNetwork().addNodeMode();
        };


        $scope.deleteSelectedNodeEdge = function () {
            deleteNodeorEdge(networkService, $mdToast, $http);
        };
        
        $scope.deleteSelectedNodeEdgeMode = function () {
            networkService.setClusterClickDisabled(true);
            networkService.setDeleteMode(true);
            $mdToast.show(
            $mdToast.simple()
                .textContent('Now select a bubble to delete.')
                .position('bottom')
                .hideDelay(3000)
            );
            
        };

        $scope.enableEditMode = function () {
            networkService.setClusterClickDisabled(true);
            networkService.setEditMode(true);
            $mdToast.show(
            $mdToast.simple()
                .textContent('Now select a bubble to edit.')
                .position('bottom')
                .hideDelay(3000)
            );
            
        };

        $scope.filUpload = function () {
            bubbleType = 'fileAttachment';
            showToast($mdToast, 'Click anywhere to add a Bubble for file');
            networkService.setBubbleType(bubbleType);
            networkService.getNetwork().addNodeMode();
        }

        $scope.mediaUpload = function () {
            bubbleType = 'mediaAttachment';
            showToast($mdToast, 'Click anywhere to add a Bubble for Media');
            networkService.setBubbleType(bubbleType);
            networkService.getNetwork().addNodeMode();
        }

        // for Opening the <form> to add text to node (UI hint : Edit Bubble)
        $scope.openTextBox = function () {
            if (networkService.getNetwork().getSelectedNodes().length > 0) {
                var parentEl = angular.element(document.body);
                $mdDialog.show({
                    parent: parentEl,
                    template: addTextToNodeDialog(),
                    clickOutsideToClose: true,
                    // for saving the added text to the node
                    controller: function ($scope, $mdDialog) {
                        $scope.addTextToNodes = function () {
                            var selectedNodes = networkService.getNetwork().getSelectedNodes();
                            for (var i = 0; i < selectedNodes.length; i++) {
                                networkService.getNodes().update({id: selectedNodes[i], title: $scope.node.text});
                            }
                            $mdDialog.hide();
                        }
                    }
                });
            }

        };

        // Dialog Box to share the selected bubble
        $scope.openShareBox = function ($event) {
            if (networkService.getNetwork().getSelectedNodes().length > 0) {
                $mdDialog.show({
                    targetEvent: $event,
                    template:
                            '<md-dialog aria-label="List dialog">' +
                            '  <md-dialog-content>' +
                            '     <div>Select Recipient User</div>' +
                            '     <br>' +
                            '     <md-input-container style="margin-right: 10px;">' +
                            '       <label>Target User</label>' +
                            '       <md-select ng-model="userId">' +
                            '       <md-option ng-repeat="user in users" value="{{user.id}}">{{user.name}}</md-option>' +
                            '     </md-select>' +
                            '   </md-input-container>' +
                            '  </md-dialog-content>' +
                            '  <md-dialog-actions>' +
                            '    <md-button ng-click="shareBubble()" class="md-primary">' +
                            '      Share' +
                            '    </md-button>' +
                            '    <md-button ng-click="closeDialog()" class="md-primary">' +
                            '      Close Dialog' +
                            '    </md-button>' +
                            '  </md-dialog-actions>' +
                            '</md-dialog>',
                    controller: ShareBubbleDialogController
                });
            } else {
                $mdToast.show(
                        $mdToast.simple()
                        .textContent('Select a Bubble you need to share.')
                        .position('bottom')
                        .hideDelay(3000)
                        );
            }

            function ShareBubbleDialogController($scope, $mdDialog) {
                $http.get('admin/bubblePLE/usernames').then(function (response) {
                    $scope.users = response.data;
                }, function (errResponse) {
                    $mdToast.show(
                            $mdToast.simple()
                            .textContent('Error fetching Users')
                            .position('bottom')
                            .hideDelay(3000)
                            );
                });

                $scope.shareBubble = function () {
                    if ($scope.userId) {
                        var selectedBubble = networkService.getNetwork().getSelectedNodes();
                        angular.forEach(selectedBubble, function (value, key) {
                            $http.get('admin/bubblePLE/share/' + value + '/' + $scope.userId).then(function (response) {
                                //    Bubble Sharing Done
                            }, function (errResponse) {
                                $mdToast.show(
                                        $mdToast.simple()
                                        .textContent('Error sharing Bubble')
                                        .position('bottom')
                                        .hideDelay(3000)
                                        );
                            });
                        });
                        $mdDialog.hide();
                        $mdToast.show(
                                $mdToast.simple()
                                .textContent('Bubble(s) shared!')
                                .position('bottom')
                                .hideDelay(3000)
                                );
                    }
                };

                $scope.closeDialog = function () {
                    $mdDialog.hide();
                }
            }
        };


        // for Opening the <form> to change label of the node
        $scope.openNodeChangeBox = function () {
            if (networkService.getNetwork().getSelectedNodes().length > 0) {
                var parentEl = angular.element(document.body);
                $mdDialog.show({
                    parent: parentEl,
                    template: changeLabelDialogTemplate(),
                    clickOutsideToClose: true,
                    // for saving the added text to the node
                    controller: function ($scope, $mdDialog) {
                        $scope.changeNodeLabel = function () {
                            var selectedNode = networkService.getNetwork().getSelectedNodes()[0];
                            var req = {bubble: {title: $scope.node.text}};
                            $http.post('admin/bubblePLE/bubbles/rest/' + selectedNode, req).then(function (response) {
                                $mdDialog.hide();
                                networkService.getNodes().update(createNode(response.data.item));
                                $mdToast.show(
                                    $mdToast.simple()
                                        .textContent('Bubble updated!')
                                        .position('bottom')
                                        .hideDelay(3000)
                                    );
                                
                            }, function(errResponse){
                                $mdDialog.hide();
                                $mdToast.show(
                                    $mdToast.simple()
                                        .textContent('Error updating bubble!')
                                        .position('bottom')
                                        .hideDelay(3000)
                                    );
                                
                            });
                        };
                    }
                });
            } else {
                $mdToast.show(
                $mdToast.simple()
                    .textContent('Select a bubble first.')
                    .position('bottom')
                    .hideDelay(3000)
                );
            }

        };

        var doubleClickTime = 0;
        var threshold = 200;



        /*When user click on bubble then this method will be called to check whether user click once or twice*/
        function onClick(properties) {
            var t0 = new Date();
            if (t0 - doubleClickTime > threshold && !onClickTimeout) {
                onClickTimeout = setTimeout(function () {
                    if (t0 - doubleClickTime > threshold) {
                        doOnClick(properties);
                    }
                }, threshold);
            }
        }

        /*If user single click on the bubble then this method will be called*/
        function doOnClick(params) {
            if (params.nodes.length == 1 && !networkService.getClusterClickDisabled()) {
                if (networkService.getNetwork().isCluster(params.nodes[0]) == true) {
                    networkService.getNetwork().openCluster(params.nodes[0]);
                    networkService.getNetwork().setOptions({physics: {stabilization: {fit: false}}});
                    networkService.getNetwork().stabilize();
                }
                else {
                    
                    makeCluster(networkService.getNodes().get(params.nodes[0]));
                    networkService.getNetwork().setOptions({physics: {stabilization: {fit: false}}});
                    networkService.getNetwork().stabilize();
                }
            }
            console.log(params);
            if(params.nodes.length > 0 && networkService.getClusterClickDisabled()){
                if(networkService.getEditMode()){
                    if(networkService.getNetwork().isCluster(params.nodes[0]) == true){
                        $mdToast.show(
                        $mdToast.simple()
                            .textContent('Cannot edit cluster.')
                            .position('bottom')
                            .hideDelay(3000)
                        );
                    } else {
                        $scope.openNodeChangeBox();
                    }
                    networkService.setEditMode(false);
                    networkService.setClusterClickDisabled(false);
                } else if(networkService.getDeleteMode()) {
                    if(networkService.getNetwork().isCluster(params.nodes[0]) == true){
                        $mdToast.show(
                        $mdToast.simple()
                            .textContent('Cannot delete cluster.')
                            .position('bottom')
                            .hideDelay(3000)
                        );
                    } else {
                        $scope.deleteSelectedNodeEdge();
                    }
                    networkService.setDeleteMode(false);
                    networkService.setClusterClickDisabled(false);
                }
            }
        }

        // Upload actual file to the server
        $scope.onFileSelect = function (file) {
            //uploadFile($scope, $mdToast, $timeout, file, Upload, networkService);
            $scope.$emit('uploadFileEvent', [file]);
        };

        /* Search node in network */
        $scope.searchNode = function () {
            $mdDialog.show({
                template: getSearchDialogTemplate(), //template is in dialogtemplate file
                locals: {
                    items: (networkService.getNodes())
                },
                controller: searchController
            });
        };


        /* Change Color of network */
        $scope.changeColor = function () {

            if (networkService.getNetwork().getSelectedNodes().length == 0) {
                showToast($mdToast, 'Please select a node first');
                return;
            }

            $mdDialog.show({
                template: getColorChangeDialogTemplate(), //template is in dialogtemplate file
                locals: {
                    items: (networkService.getNodes())
                },
                controller: colorChangeCtrl
            });

        };

        /* Collapsing sidebar */
        $scope.collapse_sidebar = function () {
            var icon_labels = document.getElementsByClassName('icon-label');
            var icons = document.getElementsByClassName('sidebar-menu')[0].getElementsByTagName('md-icon');
            var sidebar = document.getElementsByTagName('md-sidenav')[0]
            var logo_label = document.getElementsByClassName('logo-label')[0]
            for (var i = 0; i < icon_labels.length; i++) {
                if (icon_labels[i].style.opacity === '') {
                    icon_labels[i].style.opacity = 0;
                    icons[i].style.background = 'none';
                    icons[i].style.color = 'rgba(0,0,0,0.54)';

                } else {
                    icon_labels[i].style.opacity = '';
                    icons[i].style.background = '#5c6bc0';
                    icons[i].style.color = '#ffffff';
                }
            }

            if (sidebar.style.width === '') {
                sidebar.style.width = '90px';
                logo_label.style.display = 'none';
            } else {
                sidebar.style.width = '';
                logo_label.style.display = 'inline-block';
            }

        };
    }

    }]);