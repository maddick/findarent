angular
    .module('app')
    .controller('listingSearchController',['$scope', '$routeParams', 'ListingSearch', function($scope,$routeParams,ListingSearch){
        var searchParams = {};
        var isCityState = $routeParams.cityState !== undefined;
        var cityState = '';
        var zipCode = '';

        if ( isCityState ) {
            cityState = $routeParams.cityState;
            searchParams['cityState'] = cityState;
        } else {
            zipCode = $routeParams.zipCode;
            searchParams['zipCode'] = zipCode;
        }

        if ( $routeParams.numberOfBedrooms !== undefined) {
            searchParams['numberOfBedrooms'] = $routeParams.numberOfBedrooms;
        }

        console.log(searchParams);

        var promise = ListingSearch.get(searchParams);
        promise.then(
        function(response){
            $scope.listings = response.data;
        },
        function(response){
            $scope.listings = response.data;
        });
    }]);