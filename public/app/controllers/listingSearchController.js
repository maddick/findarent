angular
    .module('app')
    .controller('listingSearchController',['$scope', '$routeParams', 'ListingSearch', 'SearchURL', function($scope,$routeParams,ListingSearch,SearchURL){
        var searchParams = {};
        var listingSearchParams = {};
        var isCityState = $routeParams.cityState !== undefined;
        var cityState = '';
        var zipCode = '';
        var zipOrCityState = '';

        if ( isCityState ) {
            cityState = $routeParams.cityState;
            searchParams['cityState'] = zipOrCityState = cityState;
            listingSearchParams.cityStateOrZip = cityState;
        } else {
            zipCode = $routeParams.zipCode;
            searchParams['zipCode'] = zipOrCityState = zipCode;
            listingSearchParams.cityStateOrZip = zipCode;
        }

        if ( $routeParams.numberOfBedrooms !== undefined) {
            searchParams['numberOfBedrooms'] = $routeParams.numberOfBedrooms;
            listingSearchParams.numberOfBedrooms = $routeParams.numberOfBedrooms;
        }

        if ( $routeParams.numberOfBathrooms !== undefined) {
            searchParams['numberOfBathrooms'] = $routeParams.numberOfBathrooms;
            listingSearchParams.numberOfBathrooms = $routeParams.numberOfBathrooms;
        }

        if ( $routeParams.minRent !== undefined) {
            searchParams['minRent'] = $routeParams.minRent;
            listingSearchParams.minRent = $routeParams.minRent;
        }

        if ( $routeParams.maxRent !== undefined) {
            searchParams['maxRent'] = $routeParams.maxRent;
            listingSearchParams.maxRent = $routeParams.maxRent;
        }

        if ( $routeParams.radius !== undefined ) {
            searchParams['radius'] = $routeParams.radius;
            listingSearchParams.radius = $routeParams.radius;
        }

        $scope.listings = {};
        var promise = ListingSearch.getListings(searchParams);
        promise.then(
        function(response){
            $scope.listings = response.data;
            $scope.listings.count = response.data.listings.length;
            $scope.successMessage = { zipOrCityState : zipOrCityState };
            $scope.listingSearchParams = listingSearchParams;
        },
        function(response){
            $scope.listings = response.data;//TODO: add error handler
        });

        var totalPromise = ListingSearch.getTotalActiveListings();
        totalPromise.then(
        function(response){
            $scope.totalActiveListings = response.data.TotalActiveListings;
        },
        function(response){
            console.log('error: ' + response.data.reasons);//TODO: add error handler
        });

        $scope.performSearch = function() {
            SearchURL.goToSearchURL($scope);
        }
    }]);