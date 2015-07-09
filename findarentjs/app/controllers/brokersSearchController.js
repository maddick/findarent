angular
    .module('app')
    .controller('brokersSearchController',['$scope','$routeParams','BrokersSearch','$location',function($scope,$routeParams,BrokersSearch,$location){

        if ($routeParams.cityState !== undefined) {
            var searchParams = {};
            searchParams['cityState'] = $routeParams.cityState;
            var promise = BrokersSearch.getBrokers(searchParams);

            $('#search-results-loading').fadeIn();

            promise.then(
                function(response){
                    $('#search-results-loading').fadeOut(400,function(){
                        $('#search-results-section').fadeIn();
                    });
                    $scope.brokers = response.data;

                    //strip the stupid html crap from the messages
                    for ( var i = 0; i < $scope.brokers.brokers.length; i++ ) {
                        var strInputCode = $scope.brokers.brokers[i]['MarketingMessage'];
                        var strTagStrippedText = strInputCode.replace(/<\/?[a-zA-Z0-9=:;,."'#!\/\-\s]+(?:\s\/>|>|$)/g, "");
                        strTagStrippedText = strTagStrippedText.replace(/&[#]?(?:[a-zA-Z]+|[0-9]+);/g,"");
                        $scope.brokers.brokers[i]['MarketingMessage'] = strTagStrippedText;
                    }
                },
                function(response){
                    $('#search-results-loading').fadeOut();
                    $scope.brokers = response.data;
                });
        } else {
            //do error
        }

        $scope.goToBroker = function(brokerId) {
            $location.url('/featured-brokers/' +  brokerId);
        }
    }]);