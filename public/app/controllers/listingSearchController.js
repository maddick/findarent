angular
    .module('app')
    .controller('listingSearchController',['$scope', 'ListingSearch', 'SearchURL', '$location', function($scope,ListingSearch,SearchURL,$location){
        var search = $location.search();
        var searchParams = {};
        var listingSearchParams = {};
        var isCityState = search['city-state'] !== undefined;
        var isZipCode = search['zip-code'] !== undefined;
        var cityState = '';
        var zipCode = '';
        var zipOrCityState = '';
        var isLandlordSearch = search['landlord-id'] !== undefined;
        var noSearch = false;


        //go through variable validations
        if ( isCityState ) {
            cityState = search['city-state'];
            searchParams['cityState'] = zipOrCityState = cityState;
            listingSearchParams.cityStateOrZip = cityState;
        } else if ( isZipCode ) {
            zipCode = search['zip-code'];
            searchParams['zipCode'] = zipOrCityState = zipCode;
            listingSearchParams.cityStateOrZip = zipCode;
        } else {
            noSearch = true;
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
            noSearch = false;
        }

        $scope.listings = {};
        var promise = null;

        //check if we are preforming or if we are just adding
        //the controller for possible future searches
        if ( !noSearch ) {

            //show a loading screen
            $('#search-results-loading').fadeIn();

            if ( isLandlordSearch ) {
                promise = ListingSearch.getListingsByLandlordId(searchParams['landlordId']);
            } else {
                promise = ListingSearch.getListings(searchParams);
            }

            promise.then(
                function(response){
                    $scope.listings = response.data;
                    $scope.listings.count = response.data.listings.length;
                    $scope.successMessage = { zipOrCityState : zipOrCityState };
                    $scope.listingSearchParams = listingSearchParams;

                    //convert rent to a number
                    angular.forEach($scope.listings.listings, function(listing){
                        listing.Rent = parseFloat(listing.Rent);
                    });

                    //paginate the results
                    $scope.pagination = {};
                    $scope.pagination.currentPage = 1;
                    $scope.pagination.numPages = Math.ceil( $scope.listings.count / 5 );
                    $scope.pagination.numLastPage = $scope.listings.count % 5;
                    $scope.pagination.numPerPage = ( $scope.pagination.currentPage !== $scope.pagination.numPages ) ? 5 : $scope.pagination.numLastPage;

                    $scope.pagination.pages = [];
                    for ( var i = 1; i <= $scope.pagination.numPages; i++ ) {
                        $scope.pagination.pages.push(i);
                    }

                    /*$scope.listings.pages = {};
                    var index = 0;
                    for ( var page = 1; page <= $scope.numPages; page++ ) {
                        var listingsPerPage = ( page !== $scope.numPages ) ? 5 : $scope.numLastPage;
                        $scope.listings.pages['page' + page] = {};
                        for ( var listing = 1; listing <= listingsPerPage; listing++ ) {
                            $scope.listings.pages['page' + page]['listing' + listing] = {};
                            $scope.listings.pages['page' + page]['listing' + listing] = response.data.listings[index];
                            index++;
                        }
                    }

                    console.log($scope.listings.pages);*/

                    $('#search-results-loading').fadeOut(400,function(){
                        $('#search-results-section').fadeIn();
                    });
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
        }


        $scope.performSearch = function() {
            SearchURL.goToSearchURL($scope);
        }

        $scope.goToListing = function(listingId) {
            $location.url('/listing/' +  listingId);
        }

        $scope.nextPage = function() {
            $scope.currentPage = ( $scope.currentPage !== $scope.numPages ) ? $scope.currentPage + 1 : $scope.currentPage;
        }

        $scope.previousPage = function() {
            $scope.currentPage = ( $scope.currentPage !== 1 ) ? $scope.currentPage - 1 : $scope.currentPage;
        }

        $scope.lastPage = function() {

        }

        $scope.firstPage = function() {

        }

        $scope.goToPage = function(page) {
            $scope.pagination.currentPage = page;
        }
    }]);