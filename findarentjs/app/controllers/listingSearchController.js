angular
    .module('app')
    .controller('listingSearchController',
        ['$scope', 'ListingSearch', 'CommunitySearch', 'BrokersSearch', 'SearchURL', '$location', '$q',
            function($scope,ListingSearch,CommunitySearch,BrokersSearch,SearchURL,$location,$q){

        var search = $location.search();
        var searchParams = {};
        var comSearchParams = {};
        var brokerSearchParams = {};
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
            comSearchParams['cityState'] = cityState;
            brokerSearchParams['cityState'] = cityState;
            listingSearchParams.cityStateOrZip = cityState;
        } else if ( isZipCode ) {
            zipCode = search['zip-code'];
            searchParams['zipCode'] = zipOrCityState = zipCode;
            comSearchParams['zipCode'] = zipCode;
            brokerSearchParams['zipCode'] = zipCode;
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
        var promise         = null;

        //check if we are preforming or if we are just adding
        //the controller for possible future searches
        if ( !noSearch ) {

            //show a loading screen
            $('#search-results-loading').fadeIn();

            if ( isLandlordSearch ) {
                promise = ListingSearch.getListingsByLandlordId(searchParams['landlordId']);

                promise.then(
                     function(response){
                         $scope.results = [];
                         $scope.searchResult = 'success';

                         var listings = response.data;
                         $scope.listingResultCount = response.data.listings.length;
                         $scope.successMessage = { zipOrCityState : zipOrCityState };
                         $scope.listingSearchParams = listingSearchParams;

                         //convert rent to a number and give a type value for sorting and add to results
                         angular.forEach(listings.listings, function(listing){
                             listing.Rent = parseFloat(listing.Rent);
                             listing.Type = 1;
                             $scope.results.push(listing);
                         });

                         //paginate the results
                         $scope.pagination = {};
                         $scope.pagination.currentPage = ( $scope.results.length === 0 ) ? 0 : 1;
                         $scope.pagination.numPages = Math.ceil( $scope.results.length / 5 );
                         $scope.pagination.numPerPage = 5;

                         $scope.pagination.pages = [];
                         for ( var i = 1; i <= $scope.pagination.numPages; i++ ) {
                            $scope.pagination.pages.push(i);
                         }

                         $('#search-results-loading').fadeOut(400,function(){
                            $('#search-results-section').fadeIn();
                         });
                     },
                     function(response){
                         $('#search-results-loading').fadeOut();
                         $scope.listings = response.data;//TODO: add error handler
                     }
                );

            } else {
                $q.all([
                    ListingSearch.getListings(searchParams),
                    CommunitySearch.getCommunities(comSearchParams),
                    BrokersSearch.getBrokers(brokerSearchParams)
                ]).then(
                    function(response){
                        $scope.results = [];
                        $scope.searchResult = 'success';

                        var listings = response[0].data;
                        $scope.listingResultCount = response[0].data.listings.length;
                        $scope.successMessage = { zipOrCityState : zipOrCityState };
                        $scope.listingSearchParams = listingSearchParams;

                        //convert rent to a number and give a type value for sorting and add to results
                        angular.forEach(listings.listings, function(listing){
                            listing.Rent = parseFloat(listing.Rent);
                            listing.Type = 1;
                            $scope.results.push(listing);
                        });

                        var communities = response[1].data;

                        //strip the stupid html crap from the messages
                        var strTagStrippedText;
                        var strInputCode;
                        for ( var i = 0; i < communities.communities.length; i++ ) {
                            strInputCode = communities.communities[i]['MarketingMessage'];
                            /*strInputCode = strInputCode.replace(/&(lt|gt);/g, function (strMatch, p1){
                             return (p1 == "lt")? "<" : ">";
                             });*/
                            strTagStrippedText = strInputCode.replace(/<\/?[a-zA-Z0-9=:;,."'#!\/\-\s]+(?:\s\/>|>|$)/g, "");
                            strTagStrippedText = strTagStrippedText.replace(/&[#]?(?:[a-zA-Z]+|[0-9]+);/g,"");
                            communities.communities[i]['MarketingMessage'] = strTagStrippedText;
                        }

                        //give a rent and type value for sorting and add to results
                        angular.forEach(communities.communities, function(community){
                            community.Rent = 0;
                            community.Type = 2;
                            $scope.results.push(community);
                        });

                        var brokers = response[2].data;

                        //strip the stupid html crap from the messages
                        for ( var i = 0; i < brokers.brokers.length; i++ ) {
                            strInputCode = brokers.brokers[i]['MarketingMessage'];
                            /*strInputCode = strInputCode.replace(/&(lt|gt);/g, function (strMatch, p1){
                             return (p1 == "lt")? "<" : ">";
                             });*/
                            strTagStrippedText = strInputCode.replace(/<\/?[a-zA-Z0-9=:;,."'#!\/\-\s]+(?:\s\/>|>|$)/g, "");
                            strTagStrippedText = strTagStrippedText.replace(/&[#]?(?:[a-zA-Z]+|[0-9]+);/g,"");
                            brokers.brokers[i]['MarketingMessage'] = strTagStrippedText;
                        }

                        //give a rent and type value for sorting and add to results
                        angular.forEach(brokers.brokers, function(broker){
                            broker.Rent = 0;
                            broker.Type = 3;
                            $scope.results.push(broker);
                        });

                        //paginate the results
                        $scope.pagination = {};
                        $scope.pagination.currentPage = ( $scope.results.length === 0 ) ? 0 : 1;
                        $scope.pagination.numPages = Math.ceil( $scope.results.length / 5 );
                        $scope.pagination.numPerPage = 5;

                        $scope.pagination.pages = [];
                        for ( var i = 1; i <= $scope.pagination.numPages; i++ ) {
                            $scope.pagination.pages.push(i);
                        }

                        $('#search-results-loading').fadeOut(400,function(){
                            $('#search-results-section').fadeIn();
                        });
                    },
                    function(response){
                        $('#search-results-loading').fadeOut();
                        $scope.listings = response.data;//TODO: add error handler
                    }
                );
            }

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
        };

        $scope.goToListing = function(listingId) {
            $location.url('/listing/' +  listingId);
        };

        $scope.goToPage = function(page) {
            $scope.pagination.currentPage = page;
        };

        $scope.nextPage = function() {
            if ( $scope.pagination.currentPage !== $scope.pagination.numPages ) {
                $scope.pagination.currentPage = $scope.pagination.currentPage + 1;
            }
        };

        $scope.previousPage = function() {
            if ( $scope.pagination.currentPage !== 1 ) {
                $scope.pagination.currentPage = $scope.pagination.currentPage - 1;
            }
        };

        $scope.firstPage = function() {
            $scope.pagination.currentPage = 1;
        };

        $scope.lastPage = function() {
            $scope.pagination.currentPage = $scope.pagination.numPages;
        };

        $scope.goToCommunity = function(communityId) {
            $location.url('/featured-communities/' +  communityId);
        };

        $scope.status = 'ready';
    }]);