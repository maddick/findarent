var communitySearchBaseURL =  'http://192.168.0.101:8080/communities/featured-communities';

angular
    .module('app')
    .factory('CommunitySearch', [ '$http', function($http){
        return {
            getCommunities : function (params) {
                var cityState = '';
                var zipCode   = '';

                if ( 'cityState' in params) {
                    cityState = '/city-state/' + params['cityState'];
                }

                if ( 'zipCode' in params ) {
                    zipCode = '/zip-code/' + params['zipCode'];
                }

                return $http.get( communitySearchBaseURL + '/search' + cityState + zipCode );
            },
            getCommunityCitiesByState : function (state) {
                return $http.get( communitySearchBaseURL + '/get-community-cities-by-state/state/' + state);
            }
        }
    }]);