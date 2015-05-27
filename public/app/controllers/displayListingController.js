angular
    .module('app')
    .controller('displayListingController',['$scope','$routeParams','ListingRest',function($scope,$routeParams,ListingRest){
        var listingId = ($routeParams.listingId === undefined ) ? '' : $routeParams.listingId;
        var promise = ListingRest.getListingById( listingId );
        promise.then(
            function(response){
                $scope.listing = response.data.listing[0];
                console.log($scope.listing);
            },
            function(response){
                console.log(response);
            }
        );
    }]);