var listingSearchBaseURL =  'http://192.168.0.101:8080/listing/search';

angular
    .module('app')
    .factory('ListingSearch', [ '$http', function($http){
        return {
            getListings: function(params){
                var zipCode = '';
                var numberOfBedrooms = '';
                var numberOfBathrooms = '';
                var minRent = '';
                var maxRent = '';
                var cityState = '';
                var radius = '';
                var type = '';
                var payload = {};

                if ( 'cityState' in params ) {
                    //cityState = '/city-state/' + params['cityState'];
                    payload['city-state'] = params['cityState'];
                }

                if ( 'zipCode' in params ) {
                    //zipCode = '/zip-code/' + params['zipCode'];
                    payload['zip-code'] = params['zipCode'];
                }

                if ( 'numberOfBedrooms' in params ) {
                    //numberOfBedrooms = '/number-of-bedrooms/' + params['numberOfBedrooms'];
                    payload['number-of-bedrooms'] = params['numberOfBedrooms'];
                }

                if ( 'numberOfBathrooms' in params ) {
                    //numberOfBathrooms = '/number-of-bathrooms/' + params['numberOfBathrooms'];
                    payload['number-of-bathrooms'] = params['numberOfBathrooms'];
                }

                if ( 'minRent' in params ) {
                    //minRent = '/min-rent/' + params['minRent'];
                    payload['min-rent'] = params['minRent'];
                }

                if ( 'maxRent' in params ) {
                    //maxRent = '/max-rent/' + params['maxRent'];
                    payload['max-rent'] = params['maxRent'];
                }

                if ( 'radius' in params ) {
                    //radius = '/radius/' + params['radius'];
                    payload['radius'] = params['radius'];
                }

                if ( 'type' in params ) {
                    //type = '/type/' + params['type'];
                    payload['type'] = params['type'];
                }
                //return $http.get( listingSearchBaseURL + '/get-listings' + cityState + zipCode + numberOfBedrooms + numberOfBathrooms + minRent + maxRent + radius + type );
                return $http(
                    {
                        method: 'POST',
                        url: listingSearchBaseURL + '/get-listings',
                        headers : { 'Content-Type' : 'application/json' },
                        data: new Blob([JSON.stringify(payload)])
                    }
                );
            },

            getListingsByLandlordId: function(landlordId){
                return $http.get( listingSearchBaseURL + '/get-listings-by-landlord/landlord-id/' + landlordId );
            },

            getListingsByCommunityId: function(communityId) {
                return $http.get( listingSearchBaseURL + '/get-listings-by-community-id/community-id/' + communityId);
            },

            getListingsByBrokerId: function(brokerId){
                return $http.get( listingSearchBaseURL + '/get-listings-by-broker-id/broker-id/' + brokerId);
            },

            getTotalActiveListings: function() {
                return $http.get( listingSearchBaseURL + '/total-active-listings/');
            },
            getListingPhotos: function(listingId) {
                //console.log(listingSearchBaseURL + '/get-photos-by-listing-id/listing-id/' + listingId);
                return $http.get(listingSearchBaseURL + '/get-photos-by-listing-id/listing-id/' + listingId );
            },
            getPopularSearches: function() {
                return $http.get(listingSearchBaseURL + '/get-popular-searches/');
            }
        }
    }]);