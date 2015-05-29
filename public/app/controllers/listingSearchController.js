angular
    .module('app')
    .controller('listingSearchController',['$scope', 'ListingSearch', 'SearchURL', '$location', function($scope,ListingSearch,SearchURL,$location){
        var search = $location.search();
        console.log(search);
        var searchParams = {};
        var listingSearchParams = {};
        var isCityState = search['city-state'] !== undefined;
        var isZipCode = search['zip-code'] !== undefined;
        var cityState = '';
        var zipCode = '';
        var zipOrCityState = '';
        var isLandlordSearch = search['landlord-id'] !== undefined;

        if ( isCityState ) {
            cityState = search['city-state'];
            searchParams['cityState'] = zipOrCityState = cityState;
            listingSearchParams.cityStateOrZip = cityState;
        } else if ( isZipCode ) {
            zipCode = search['zip-code'];
            searchParams['zipCode'] = zipOrCityState = zipCode;
            listingSearchParams.cityStateOrZip = zipCode;
        }

        if ( search['number-of-bedrooms'] !== undefined) {
            searchParams['numberOfBedrooms'] = search['number-of-bedrooms'];
            listingSearchParams.numberOfBedrooms = search['number-of-bedrooms'];
        }

        if ( search['number-of-bathrooms'] !== undefined) {
            searchParams['numberOfBathrooms'] = search['number-of-bathrooms'];
            listingSearchParams.numberOfBathrooms = search['number-of-bathrooms'];
        }

        if ( search['min-rent'] !== undefined) {
            searchParams['minRent'] = search['min-rent'];
            listingSearchParams.minRent = search['min-rent'];
        }

        if ( search['max-rent'] !== undefined) {
            searchParams['maxRent'] = search['max-rent'];
            listingSearchParams.maxRent = search['max-rent'];
        }

        if ( search['radius'] !== undefined ) {
            searchParams['radius'] = search['radius'];
            listingSearchParams.radius = search['radius'];
        }

        if ( search['landlord-id'] !== undefined ) {
            searchParams['landlordId'] = search['landlord-id'];
        }


        //show a loading screen
        $('#search-results-loading').fadeIn();

        $scope.listings = {};
        var promise = null;
        if ( isLandlordSearch ) {
            promise = ListingSearch.getListingsByLandlordId(searchParams['landlordId']);
        } else {
            promise = ListingSearch.getListings(searchParams);
        }

        promise.then(
        function(response){
            $('#search-results-loading').fadeOut();
            $scope.listings = response.data;
            $scope.listings.count = response.data.listings.length;
            $scope.successMessage = { zipOrCityState : zipOrCityState };
            $scope.listingSearchParams = listingSearchParams;
        },
        function(response){
            $('#search-results-loading').fadeOut();
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

        $scope.goToListing = function(listingId) {
            $location.url('/listing/' +  listingId);
        }
    }]);