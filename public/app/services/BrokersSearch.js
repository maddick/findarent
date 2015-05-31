var brokerSearchBaseURL =  'http://192.168.0.101:8080/brokers/featured-brokers/search';

angular
    .module('app')
    .factory('BrokersSearch', [ '$http', function($http){
        return {
            getBrokers : function (params) {
                var cityState = '';

                if ( 'cityState' in params) {
                    cityState = '/city-state/' + params['cityState'];
                }

                return $http.get( brokerSearchBaseURL + cityState );
            }
        }
    }]);