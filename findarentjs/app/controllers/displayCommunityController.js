angular
    .module('app')
    .controller('displayCommunityController',['$scope','$routeParams','CommunityRest','$location','$sce',function($scope,$routeParams,CommunityRest,$location,$sce){
        var communityId = ($routeParams.communityId === undefined ) ? '' : $routeParams.communityId;

        //show a loading screen
        $('#display-community-loading').fadeIn();

        var promise = CommunityRest.getCommunityById( communityId );
        promise.then(
            function(response){
                $scope.community = response.data.communities[0];
                $scope.community.MarketingMessage = $sce.trustAsHtml($scope.community.MarketingMessage);
                $scope.community.result = response.data.result;
                $scope.goToAddress = $scope.community.Address + ',' + $scope.community.City + ',' + $scope.community.State;
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

        $scope.seeOnMap = function(){
            if ( $scope.goToAddress !== undefined ) {
                var url = 'http://maps.google.com/maps?q=' + $scope.goToAddress;
                var win = window.open(url, '_blank');
                win.focus();
            }
        };

        $scope.gotToListingSearch = function () {
            var communityId = $scope.community.CommunityID;
            if ( communityId !== undefined ) {
                $location.url('/search?community-id=' + communityId);
            }
        };
    }]);