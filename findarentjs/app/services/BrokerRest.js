var brokerRestBaseURL =  'http://192.168.0.101:8080/brokers/rest/';

angular
    .module('app')
    .factory('BrokerRest',['$http',function($http){
        return {
            getBrokerById : function (brokerId) {
                console.log(brokerRestBaseURL + brokerId);
                return $http.get( brokerRestBaseURL + brokerId);
            }
        }
    }]);