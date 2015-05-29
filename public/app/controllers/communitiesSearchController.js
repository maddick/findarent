angular
    .module('app')
    .controller('communitiesSearchController',['$scope','$routeParams','CommunitySearch',function($scope,$routeParams,CommunitySearch){

        if ($routeParams.cityState !== undefined) {
            var searchParams = {};
            searchParams['cityState'] = $routeParams.cityState;
            console.log(searchParams);
            var promise = CommunitySearch.getCommunities(searchParams);

            promise.then(
                function(response){
                    $scope.communities = response.data;
                },
                function(response){
                    $scope.communities = response.data;
                });
        } else {
            //do error
        }

    }]);
