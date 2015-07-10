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

                if ( 'cityState' in params ) {
                    cityState = '/city-state/' + params['cityState'];
                }

                if ( 'zipCode' in params ) {
                    zipCode = '/zip-code/' + params['zipCode'];
                }

                if ( 'numberOfBedrooms' in params ) {
                    numberOfBedrooms = '/number-of-bedrooms/' + params['numberOfBedrooms'];
                }

                if ( 'numberOfBathrooms' in params ) {
                    numberOfBathrooms = '/number-of-bathrooms/' + params['numberOfBathrooms'];
                }

                if ( 'minRent' in params ) {
                    minRent = '/min-rent/' + params['minRent'];
                }

                if ( 'maxRent' in params ) {
                    maxRent = '/max-rent/' + params['maxRent'];
                }

                if ( 'radius' in params ) {
                    radius = '/radius/' + params['radius'];
                }
                return $http.get( listingSearchBaseURL + '/get-listings' + cityState + zipCode + numberOfBedrooms + numberOfBathrooms + minRent + maxRent + radius );
            },

            getListingsByLandlordId: function(landlordId){
                return $http.get( listingSearchBaseURL + '/get-listings-by-landlord/landlord-id/' + landlordId );
            },

            getListingsByCommunityId: function(communityId) {
                return $http.get( listingSearchBaseURL + '/get-listings-by-community-id/community-id/' + communityId);
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