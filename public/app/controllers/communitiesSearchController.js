angular
    .module('app')
    .controller('communitiesSearchController',['$scope','$routeParams','CommunitySearch','$location',function($scope,$routeParams,CommunitySearch,$location){

        if ($routeParams.cityState !== undefined) {
            var searchParams = {};
            searchParams['cityState'] = $routeParams.cityState;
            var promise = CommunitySearch.getCommunities(searchParams);

            $('#search-results-loading').fadeIn();

            promise.then(
                function(response){
                    $('#search-results-loading').fadeOut(400,function(){
                        $('#search-results-section').fadeIn();
                    });
                    $scope.communities = response.data;

                    //strip the stupid html crap from the messages
                    for ( var i = 0; i < $scope.communities.communities.length; i++ ) {
                        var strInputCode = $scope.communities.communities[i]['MarketingMessage'];
                        /*strInputCode = strInputCode.replace(/&(lt|gt);/g, function (strMatch, p1){
                            return (p1 == "lt")? "<" : ">";
                        });*/
                        var strTagStrippedText = strInputCode.replace(/<\/?[a-zA-Z0-9=:;,."'#!\/\-\s]+(?:\s\/>|>|$)/g, "");
                        strTagStrippedText = strTagStrippedText.replace(/&[#]?(?:[a-zA-Z]+|[0-9]+);/g,"");
                        $scope.communities.communities[i]['MarketingMessage'] = strTagStrippedText;
                    }
                },
                function(response){
                    $('#search-results-loading').fadeOut();
                    $scope.communities = response.data;
                });
        } else {
            //do error
        }

        $scope.goToCommunity = function(communityId) {
            $location.url('/featured-communities/' +  communityId);
        }
    }]);
