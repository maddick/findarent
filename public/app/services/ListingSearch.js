angular
    .module('app')
    .factory('ListingSearch', [ '$http', '$q', function($http,$q){
        return {
            get: function(params){
                var zipCode = '';
                var numberOfBedrooms = '';
                var cityState = '';

                if ( 'cityState' in params ) {
                    cityState = '/city-state/' + params['cityState'];
                }

                if ( 'zipCode' in params ) {
                    zipCode = '/zip-code/' + params['zipCode'];
                } else {
                    zipCode = '';
                }

                if ( 'numberOfBedrooms' in params ) {
                    numberOfBedrooms = '/number-of-bedrooms/' + params['numberOfBedrooms'];
                }
                return $http.get('http://localhost:8080/listing/search' + cityState + zipCode + numberOfBedrooms);
            }
        }
    }]);