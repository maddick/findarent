angular
    .module('app')
    .controller('displayListingController',['$scope','$routeParams','ListingRest','$location','ListingSearch',function($scope,$routeParams,ListingRest,$location,ListingSearch){
        var listingId = ($routeParams.listingId === undefined ) ? '' : $routeParams.listingId;
        $scope.listing = {};
        //show a loading screen
        $('#display-listing-loading').fadeIn();

        var promise = ListingRest.getListingById( listingId );
        promise.then(
            function(response){
                $scope.listing = response.data.listing[0];
                $scope.listing.result = response.data.result;
                $scope.listing.reasons = response.data.reasons;
                $scope.goToAddress = $scope.listing.Address + ',' + $scope.listing.City + ',' + $scope.listing.State;
                $('#display-listing-loading').fadeOut(400, function(){
                    $('#display-listing-success').fadeIn();
                });
                console.log($scope.listing);

                var photoPromise = ListingSearch.getListingPhotos(listingId);
                photoPromise.then(
                    function(response){
                        $scope.listing.photos = response.data.photos;
                        console.log($scope.listing.photos);
                    },
                    function(response){
                        console.log(response);
                    }
                );
            },
            function(response){
                $('#display-listing-loading').fadeOut();
                $scope.listing = response.data.listing[0];
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
                $location.url('/search?landlord-id=' + landlordId);
            }
        }

        $scope.emailToFriend = function () {

        }
    }]);