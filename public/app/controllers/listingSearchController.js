angular
    .module('app')
    .controller('listingSearchController',['$scope', '$routeParams', 'ListingSearch', function($scope,$routeParams,ListingSearch){
        var searchParams = {};
        var isCityState = $routeParams.cityState !== undefined;
        var cityState = '';
        var zipCode = '';
        var zipOrCityState = '';

        if ( isCityState ) {
            cityState = $routeParams.cityState;
            searchParams['cityState'] = zipOrCityState = cityState;
        } else {
            zipCode = $routeParams.zipCode;
            searchParams['zipCode'] = zipOrCityState = zipCode;
        }

        if ( $routeParams.numberOfBedrooms !== undefined) {
            searchParams['numberOfBedrooms'] = $routeParams.numberOfBedrooms;
        }

        if ( $routeParams.radius !== undefined ) {
            searchParams['radius'] = $routeParams.radius;
        }

        console.log(searchParams);

        var promise = ListingSearch.get(searchParams);
        promise.then(
        function(response){
            $scope.listings = response.data;
            $scope.listings.count = response.data.listings.length;
            $scope.successMessage = { zipOrCityState : zipOrCityState };
        },
        function(response){
            $scope.listings = response.data;
        });
    }]);