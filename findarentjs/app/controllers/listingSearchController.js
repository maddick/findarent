angular
    .module('app')
    .controller('listingSearchController',
        ['$scope', 'ListingSearch', 'CommunitySearch', 'BrokersSearch', 'SearchURL', '$location', '$q','$route','$http','GoogleAnalytics',
            function($scope,ListingSearch,CommunitySearch,BrokersSearch,SearchURL,$location,$q,$route,$http,GoogleAnalytics){

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
        var isCommunitySearch = search['community-id'] !== undefined;
        var isBrokerSearch = search['broker-id'] !== undefined;
        var noSearch = false;
        var hasTenantId = false;
        var hasJobId = false;
        var tenantId = null;
        var jobId = null;

        GoogleAnalytics.track();


        //go through variable validations
        if ( isCityState && isZipCode ) {
            zipCode = search['zip-code'];
            searchParams['zipCode'] = zipOrCityState = zipCode;
            comSearchParams['zipCode'] = zipCode;
            brokerSearchParams['zipCode'] = zipCode;
            listingSearchParams.cityStateOrZip = search['city-state'] + ' ' + zipCode;
        } else if ( isCityState ) {
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

        if ( search['rental-type'] !== undefined ) {
            searchParams['type'] = search['rental-type'];
            listingSearchParams.rentalType = search['rental-type'];
        }

        if ( search['tenant-id'] !== undefined) {
            hasTenantId = true;
            tenantId = search['tenant-id'];
            $location.search('tenant-id', null);
        }

        if ( search['job-id'] !== undefined ) {
            hasJobId = true;
            jobId = search['job-id'];
            $location.search('job-id', null);
        }

        if ( isLandlordSearch ) {
            searchParams['landlordId'] = search['landlord-id'];
            noSearch = false;
        }

        if ( isCommunitySearch ) {
            searchParams['communityId'] = search['community-id'];
            noSearch = false;
        }

        if ( isBrokerSearch ) {
            searchParams['brokerId'] = search['broker-id'];
            noSearch = false;
        }

        $scope.listings = {};
        var promise = null;
        $scope.allResults = [];
        var pushResults = function(results, name){
            $scope.allResults[name] = results;
        };

        //check if we are preforming or if we are just adding
        //the controller for possible future searches
        if ( !noSearch ) {

            //show a loading screen
            $('#search-results-loading').fadeIn();
            $('#search-results-section').fadeOut();

            //if this isn't a community or landlord or a broker search then we are
            //performing a full search which requires listings, communities, and
            //brokers to be searched together
            if ( !isLandlordSearch && !isCommunitySearch && !isBrokerSearch ) {

                $q.all([
                    ListingSearch.getListings(searchParams).then(
                        function(response){
                            pushResults(response,'Listings');
                        },
                        function(response){
                            pushResults(response, 'Listings');
                        }),
                    CommunitySearch.getCommunities(comSearchParams).then(
                        function(response){
                            pushResults(response,'Community');
                        },
                        function(response){
                            pushResults(response,'Community');
                        }),
                    BrokersSearch.getBrokers(brokerSearchParams).then(
                        function(response){
                            pushResults(response,'Broker');
                        },
                        function(response){
                            pushResults(response,'Broker');
                        })
                ]).then(
                    function(){
                        //var all = $scope.allResults;
                        //console.log(all);
                        $scope.results = [];

                        $scope.listingSearchParams = listingSearchParams;
                        if ($scope.allResults['Listings'].status == 200) {
                            var listings = $scope.allResults['Listings'].data;
                            $scope.listingResultCount = $scope.allResults['Listings'].data.listings.length;
                            $scope.successMessage = { message : zipOrCityState };

                            //convert rent to a number and give a type value for sorting and add to results
                            angular.forEach(listings.listings, function(listing){
                                listing.Rent = parseFloat(listing.Rent);
                                listing.Type = 1;
                                $scope.results.push(listing);
                            });
                        }

                        if ($scope.allResults['Community'].status == 200 ) {
                            var communities = $scope.allResults['Community'].data;

                            //strip the stupid html crap from the messages
                            var strTagStrippedText;
                            var strInputCode;
                            for ( var i = 0; i < communities.communities.length; i++ ) {
                                strInputCode = communities.communities[i]['MarketingMessage'];
                                if (strInputCode != null) {
                                    strTagStrippedText = strInputCode.replace(/<\/?[a-zA-Z0-9=:;,."'#!\/\-\s]+(?:\s\/>|>|$)/g, "");
                                    strTagStrippedText = strTagStrippedText.replace(/&[#]?(?:[a-zA-Z]+|[0-9]+);/g,"");
                                }
                                communities.communities[i]['MarketingMessage'] = strTagStrippedText;
                            }

                            //give a rent and type value for sorting and add to results
                            angular.forEach(communities.communities, function(community){
                                community.Rent = 0;
                                community.Type = 2;
                                $scope.results.push(community);
                            });
                        }

                        if ($scope.allResults['Broker'].status == 200) {
                            var brokers = $scope.allResults['Broker'].data;

                            //strip the stupid html crap from the messages
                            for ( var i = 0; i < brokers.brokers.length; i++ ) {
                                strInputCode = brokers.brokers[i]['MarketingMessage'];
                                if (strInputCode !== null) {
                                    strTagStrippedText = strInputCode.replace(/<\/?[a-zA-Z0-9=:;,."'#!\/\-\s]+(?:\s\/>|>|$)/g, "");
                                    strTagStrippedText = strTagStrippedText.replace(/&[#]?(?:[a-zA-Z]+|[0-9]+);/g,"");
                                    brokers.brokers[i]['MarketingMessage'] = strTagStrippedText;
                                }
                            }

                            //give a rent and type value for sorting and add to results
                            angular.forEach(brokers.brokers, function(broker){
                                broker.Rent = 0;
                                broker.Type = 3;
                                $scope.results.push(broker);
                            });
                        }

                        if ( $scope.results.length == 0 ) {
                            $scope.searchResult = 'none';
                        } else {
                            $scope.searchResult = 'success';
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
                        }

                    },
                    function(response){
                        //console.log(response);
                        $('#search-results-loading').fadeOut();
                        $scope.listings = response.data;//TODO: add error handler
                    }
                );

            } else {

                var successMessage = '';

                //determine if we are searching based on landlord, community, or broker and
                //preform the appropriate search.
                if ( isLandlordSearch) {
                    promise = ListingSearch.getListingsByLandlordId(searchParams['landlordId']);
                    successMessage = 'landlord-id ' + searchParams['landlordId'];
                } else if ( isCommunitySearch ) {
                    promise = ListingSearch.getListingsByCommunityId(searchParams['communityId']);
                    successMessage = 'community-id ' + searchParams['communityId'];
                } else if ( isBrokerSearch ) {
                    promise = ListingSearch.getListingsByBrokerId(searchParams['brokerId']);
                    successMessage = 'broker-id ' + searchParams['brokerId'];
                }

                promise.then(
                    function(response){
                        $scope.results = [];
                        $scope.searchResult = 'success';

                        var listings = response.data;
                        $scope.listingResultCount = response.data.listings.length;
                        $scope.successMessage = { message : successMessage };
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
                        $scope.searchResult = 'error';
                        $scope.listings = response.data;
                    }
                );
            }

            //update database if tenant-id and job-id are provided. Having both of
            //these id's on the search signifies that the search came from a URL
            //generated from the email service.
            if ( hasJobId && hasTenantId ) {
                var payload = {};
                payload['tenant-id'] = tenantId;
                payload['job-id'] = jobId;
                var historyPromise = $http(
                    {
                        method: 'POST',
                        url: 'http://192.168.0.101:8080/communication/message/update-email-history/',
                        headers : { 'Content-Type' : 'application/json' },
                        data: new Blob([JSON.stringify(payload)])
                    }
                );

                historyPromise.then(
                    function(response){
                        //console.log(response);
                    },
                    function(response){
                        //console.log(response);
                    }
                );
            }

            var totalPromise = ListingSearch.getTotalActiveListings();
            totalPromise.then(
                function(response){
                    $scope.totalActiveListings = response.data.TotalActiveListings;
                },
                function(response){
                    //console.log('error: ' + response.data.reasons);//TODO: add error handler
            });
        }


        //defined down here are the functions necessary for the page to
        //operate correctly such as navigating to a listing or switching
        //between different pages of results
        $scope.performSearch = function() {
            SearchURL.goToSearchURL($scope);
            //$route.reload();
        };

        $scope.goToListing = function(listingId) {
            $location.url('/Listings/' +  listingId);
        };

        $scope.goToPage = function(page) {
            $scope.pagination.currentPage = page;
            document.body.scrollTop = 0;
        };

        $scope.nextPage = function() {
            if ( $scope.pagination.currentPage !== $scope.pagination.numPages ) {
                $scope.pagination.currentPage = $scope.pagination.currentPage + 1;
                document.body.scrollTop = 0;
            }
        };

        $scope.previousPage = function() {
            if ( $scope.pagination.currentPage !== 1 ) {
                $scope.pagination.currentPage = $scope.pagination.currentPage - 1;
                document.body.scrollTop = 0;
            }
        };

        $scope.firstPage = function() {
            $scope.pagination.currentPage = 1;
            document.body.scrollTop = 0;
        };

        $scope.lastPage = function() {
            $scope.pagination.currentPage = $scope.pagination.numPages;
            document.body.scrollTop = 0;
        };

        $scope.goToCommunity = function(communityId) {
            $location.url('/Communities/' +  communityId);
        };

        $scope.goToBroker = function(brokerId) {
            $location.url('/Brokers/' +  brokerId);
        };

        $scope.status = 'ready';
    }]);