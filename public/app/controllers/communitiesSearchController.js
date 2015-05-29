angular
    .module('app')
    .controller('communitiesSearchController',['$scope','$routeParams','CommunitySearch',function($scope,$routeParams,CommunitySearch){

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
                },
                function(response){
                    $('#search-results-loading').fadeOut();
                    $scope.communities = response.data;
                });
        } else {
            //do error
        }

    }]);
