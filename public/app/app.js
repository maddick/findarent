var newApp = angular.module('app',[])
    .config(['$routeProvider', function($routeProvider){
        $routeProvider
            .when('/search',{
                templateUrl: 'app/views/listingSearch.html',
                controller: 'appController'
            })
            .when('/search/city-state/:cityState',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/',{
                templateUrl: 'app/views/listingSearch.html',
                controller: 'listingSearchController'
            })
            .otherwise({redirectTo: '/'});
    }]);