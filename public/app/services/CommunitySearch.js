var communitySearchBaseURL =  'http://192.168.0.101:8080/communities/featured-communities';

angular
    .module('app')
    .factory('CommunitySearch', [ '$http', function($http){
        return {
            getCommunities : function (params) {
                var cityState = '';

                if ( 'cityState' in params) {
                    cityState = '/city-state/' + params['cityState'];
                }

                return $http.get( communitySearchBaseURL + '/search' + cityState );
            },
            getCommunityCitiesByState : function (state) {
                return $http.get( communitySearchBaseURL + '/get-community-cities-by-state/state/' + state);
            }
        }
    }]);