var communityRestBaseURL =  'http://192.168.0.101:8080/communities/rest/';

angular
    .module('app')
    .factory('CommunityRest',['$http',function($http){
        return {
            getCommunityById : function (communityId) {
                console.log(communityRestBaseURL + communityId);
                return $http.get( communityRestBaseURL + communityId);
            }
        }
    }]);