var autocompleteURL = 'http://192.168.0.101:8080/listing/search/get-autocomplete-suggestions/autocomplete-data/';

app
    .factory('autocompleteDataService',['$http',function($http){
        return{
            getSuggestions : function(autocompleteData) {
                return http.get(autocompleteURL + autocompleteData);
            }
        }
    }]);