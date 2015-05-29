var communitySearchBaseURL =  'http://192.168.0.101:8080/communities/featured-communities/search';

angular
    .module('app')
    .factory('CommunitySearch', [ '$http', function($http){
        return {
            getCommunities : function (params) {
                var cityState = '';

                if ( 'cityState' in params) {
                    cityState = '/city-state/' + params['cityState'];
                }

                return $http.get( communitySearchBaseURL + cityState );
            }
        }
    }]);