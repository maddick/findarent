var newApp = angular.module('app',[])
    .config(['$routeProvider', function($routeProvider){
        $routeProvider

            //routing for city-state based urls

            .when('/search',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //go to specific listing
            .when('/listing/:listingId',{
                templateUrl: 'app/views/displayListing.html'
            })

            //communities
            .when('/featured-communities/',{
                templateUrl: 'app/views/featuredCommunitiesList.html'
            })
            .when('/featured-communities/search/city-state/:cityState',{
                templateUrl: 'app/views/communitiesSearchResults.html'
            })

            //catch all rules
            .when('/',{
                templateUrl: 'app/views/listingSearch.html',
                controller: 'appController'
            })
            .otherwise({redirectTo: '/'});
    }]);