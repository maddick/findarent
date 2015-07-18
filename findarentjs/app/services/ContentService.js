angular
    .module('app')
    .factory('ContentService',['$http',function($http){
        return {
            getContent : function(tag){

                var payload = {};
                payload.content = tag;

                return $http({
                    method: 'POST',
                    url: 'http://192.168.0.101:8080/content/content/get-content',
                    headers: {'Content-Type' : 'application/json' },
                    data: payload
                });
            }
        }
    }]);