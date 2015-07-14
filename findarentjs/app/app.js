var newApp = angular.module('app',['ngRoute'])
    .config(['$routeProvider', '$locationProvider', function($routeProvider,$locationProvider){
        $locationProvider.html5Mode(true);
        //$locationProvider.hashPrefix('!');
        $routeProvider

            //routing for city-state based urls

            .when('/search',{
                templateUrl: '/app/views/listingSearchResults.html'
            })

            //go to specific listing
            .when('/Listings/:listingId',{
                templateUrl: '/app/views/displayListing.html'
            })

            //communities
            .when('/featured-communities/',{
                templateUrl: '/app/views/featuredCommunitiesList.html'
            })
            .when('/featured-communities/search/city-state/:cityState',{
                templateUrl: '/app/views/communitiesSearchResults.html'
            })
            .when('/featured-communities/:communityId',{
                templateUrl: '/app/views/displayCommunity.html'
            })

            //contact owner form
            .when('/Contact-Owner',{
                templateUrl: '/app/views/contactForm.html'
            })
            .when('/Contact-Owner/:listingId',{
                templateUrl: '/app/views/contactForm.html'
            })

            //brokers
            .when('/featured-brokers/',{
                templateUrl: '/app/views/featuredBrokers.html'
            })
            .when('/featured-brokers/search/city-state/:cityState',{
                templateUrl: '/app/views/brokersSearchResults.html'
            })
            .when('/featured-brokers/:brokerId',{
                templateUrl: '/app/views/displayBroker.html'
            })

            //catch all rules
            .when('/',{
                templateUrl: '/app/views/listingSearch.html',
                controller: 'appController'
            })
            .otherwise({redirectTo: '/'});
    }]);