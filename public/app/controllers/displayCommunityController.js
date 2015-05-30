angular
    .module('app')
    .controller('displayCommunityController',['$scope','$routeParams','CommunityRest','$location',function($scope,$routeParams,CommunityRest,$location){
        var communityId = ($routeParams.communityId === undefined ) ? '' : $routeParams.communityId;

        //show a loading screen
        $('#display-community-loading').fadeIn();

        var promise = CommunityRest.getCommunityById( communityId );
        promise.then(
            function(response){
                $scope.community = response.data.communities[0];
                $scope.community.result = response.data.result;
                $('#display-community-loading').fadeOut(400, function(){
                    $('#display-community-success').fadeIn();
                });
                console.log($scope.community);
            },
            function(response){
                $('#display-community-loading').fadeOut();
                $scope.community = response.data.communities[0];
                console.log(response);
            }
        );
    }]);