/* 
 * Created by touhid
 */

'use strict';

app.controller('loginCtrl',['$location', '$scope', '$http','$mdToast', function($location, $scope, $http, $mdToast) {
        
        $scope.authenticate = true;
        $scope.loading = false;
        $scope.loadingMessage = '';
        $scope.noticeMessageShow = false;
        $scope.noticeMessage = 'Please authorize the app from L2P';
        $scope.sync = false;       
        
        
        $http.get('l2p/authenticate').then(function(response){
            if(response.data['success'] === true){
                $scope.sync = true;
                $scope.authenticate = false;
                $scope.noticeMessageShow = true;
                $scope.noticeMessage = 'You are already authenticated. Sync your L2P.';
            }
        });
        
        $scope.l2pAuthenticate = function() {
            console.log('authenticating');
            $scope.loading = true;
            $scope.loadingMessage = 'Authenticating...';
            $scope.authenticate = false;
            $scope.noticeMessageShow = true;
            
            $http.get('l2p/authenticate').then(function(response){
                if(response.data['success'] === true){
                    $scope.sync = true;
                    $scope.loading = false;
                    $scope.noticeMessage = 'You are already authenticated. Sync your L2P.';

                } else if(response.data['success'] === false){
                    window.open(response.data['verifyUrl'], '_blank');
                    var refreshPage = setInterval(function(){
                        $http.get('l2p/authenticate').then(function(next_response){
                            if(next_response.data['success'] === true){
                                $scope.sync = true;
                                $scope.noticeMessage = 'You are authenticated. Sync your L2P.';
                                $scope.loading = false;
                                clearTimeout(refreshPage);
                            }
                        });
                    }, 5000);
                    
                }
            }, function(errResponse) {
                $scope.loading = false;
                $scope.loadingMessage = '';
                $scope.authenticate = true;
                $scope.noticeMessage = 'Error Authenticating...Please try again';
                console.log('Error Authenticating');
            });
          
        };
        
        $scope.l2pSync = function(){
            console.log('syncing');
            $scope.sync = false;
            $scope.loading = true;
            $scope.loadingMessage = 'Syncing...';
            $scope.noticeMessage = 'L2P is syncing...it may take 1 to 2 minutes';
            $http.get('admin/bubblePLE/sync').then(function(response){
                if(response.data['success'] === true){
                    setTimeout(function(){
                        $location.path('/#/');
                        $mdToast.show(
                        $mdToast.simple()
                          .textContent('hooray! L2P is synced.')
                          .position('bottom')
                          .hideDelay(3000)
                        );
                    }, 1500);
                } else if(response.data['success'] === false){
                    $scope.sync = true;
                    $scope.loading = false;
                    $scope.noticeMessage = 'Error Syncing...Please try again';
                    
                }
            }, function(errResponse){
                    $scope.sync = true;
                    $scope.loading = false;
                    $scope.noticeMessage = 'Error Syncing...Please try again';
            });
        };
}]);
