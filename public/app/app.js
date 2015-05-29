var newApp = angular.module('app',[])
    .config(['$routeProvider', function($routeProvider){
        $routeProvider

            //routing for city-state based urls

            //single variables
            .when('/search',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //go to specific listing
            .when('/listing/:listingId',{
                templateUrl: 'app/views/displayListing.html'
            })
            /*.when('/search/landlord-id/:landlordId',{
                templateUrl: 'app/views/listingSearchResults.html'
            })*/
            //catch all rules
            .when('/',{
                templateUrl: 'app/views/listingSearch.html',
                controller: 'appController'
            })
            .otherwise({redirectTo: '/'});
    }]);