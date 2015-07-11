angular
    .module('app')
    .controller('displayBrokerController',['$scope','$routeParams','BrokerRest','$location','$sce',function($scope,$routeParams,BrokerRest,$location,$sce){
        var brokerId = ($routeParams.brokerId === undefined ) ? '' : $routeParams.brokerId;

        //show a loading screen
        $('#display-broker-loading').fadeIn();

        var promise = BrokerRest.getBrokerById( brokerId );
        promise.then(
            function(response){
                $scope.broker = response.data.brokers[0];
                $scope.broker.MarketingMessage = $sce.trustAsHtml($scope.broker.MarketingMessage);
                $scope.broker.result = response.data.result;
                $scope.goToAddress = $scope.broker.Address + ',' + $scope.broker.City + ',' + $scope.broker.State;
                $('#display-broker-loading').fadeOut(400, function(){
                    $('#display-broker-success').fadeIn();
                });
                console.log($scope.broker);
            },
            function(response){
                $('#display-broker-loading').fadeOut();
                $scope.broker = response.data.brokers[0];
                console.log(response);
            }
        );

        $scope.seeOnMap = function(){
            if ( $scope.goToAddress !== undefined ) {
                var url = 'http://maps.google.com/maps?q=' + $scope.goToAddress;
                var win = window.open(url, '_blank');
                win.focus();
            }
        };

        $scope.gotToListingSearch = function () {
            var brokerId = $scope.broker.BrokerID;
            if ( brokerId !== undefined ) {
                $location.url('/search?broker-id=' + brokerId);
            }
        };
    }]);
