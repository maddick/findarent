angular
    .module('app')
    .controller('displayListingController',['$scope','$routeParams','ListingRest','$location',function($scope,$routeParams,ListingRest,$location){
        var listingId = ($routeParams.listingId === undefined ) ? '' : $routeParams.listingId;
        var promise = ListingRest.getListingById( listingId );
        promise.then(
            function(response){
                $scope.listing = response.data.listing[0];
                $scope.goToAddress = $scope.listing.Address + ',' + $scope.listing.City + ',' + $scope.listing.State;
                console.log($scope.listing);
            },
            function(response){
                console.log(response);
            }
        );

        $scope.seeOnMap = function(){
            if ( $scope.goToAddress !== undefined ) {
                var url = 'http://maps.google.com/maps?q=' + $scope.goToAddress;
                var win = window.open(url, '_blank');
                win.focus();
            }
        }

        $scope.gotToListingSearch = function () {
            var landlordId = $scope.listing.LandlordID;
            if ( landlordId !== undefined ) {
                $location.url('/search/landlord-id/' + landlordId);
            }
        }

        $scope.emailToFriend = function () {

        }
    }]);