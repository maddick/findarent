angular
    .module('app')
    .factory('ListingRest',['$http',function($http){
        return {
            getListingById : function (listingId) {
                return $http.get('http://localhost:8080/listing/rest/' + listingId);
            }
        }
    }]);