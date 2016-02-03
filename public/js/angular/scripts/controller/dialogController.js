 /**
   * Created by Waqar Ahmed on 04/02/16.
   */
function dialogController($scope, $mdDialog) {
                    $scope.addingNewNode = function() {
                        data.label = $scope.bubbleName;
                        $mdToast.show(
                            $mdToast.simple()
                            .textContent('Bubble Added: ' +  $scope.bubbleName)
                            .position('bottom')
                            .hideDelay(3000)
                            );

                        $scope.bubbleName = "";
                        $mdDialog.hide();
                        callback(data);
                    };
                    $scope.closeDialog = function() {
                        network.disableEditMode();
                        $mdDialog.hide();
                    };
                }