var listingRestBaseURL =  'http://192.168.0.101:8080/listing/rest/';

angular
    .module('app')
    .factory('ListingRest',['$http',function($http){
        return {
            getListingById : function (listingId) {
                return $http.get( listingRestBaseURL + listingId);
            }
        }
    }]);