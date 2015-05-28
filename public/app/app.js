var newApp = angular.module('app',[])
    .config(['$routeProvider', function($routeProvider){
        $routeProvider

            //routing for city-state based urls

            //single variables
            .when('/search',{
                templateUrl: 'app/views/listingSearch.html',
                controller: 'appController'
            })
            .when('/search/city-state/:cityState',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bathrooms/:numberOfBathrooms',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/min-rent/:minRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bedrooms plus other possibilities
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/min-rent/:minRent/',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bathroom plus other possibilities
            .when('/search/city-state/:cityState/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bathrooms/:numberOfBathrooms/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bathrooms/:numberOfBathrooms/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //minRent and other possibilities
            .when('/search/city-state/:cityState/min-rent/:minRent/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/min-rent/:minRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //maxRent and other possibilities
            .when('/search/city-state/:cityState/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed and bath plus other possibilities
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed and minRent and possibilities
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/min-rent/:minRent/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/min-rent/:minRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed and maxRent and possibilities
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bath and minRent and possibilities
            .when('/search/city-state/:cityState/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bath and maxRent and possibilities
            .when('/search/city-state/:cityState/number-of-bathrooms/:numberOfBathrooms/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //minRent and maxRent and possibilities
            .when('/search/city-state/:cityState/min-rent/:minRent/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed, bath, and minRent and possibilities
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed, bath, and maxRent and possibilities
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed, bath, minRent, maxRent, and radius
            .when('/search/city-state/:cityState/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //routing for zip code based urls
            //single variables
            .when('/search/zip-code/:zipCode',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bathrooms/:numberOfBathrooms',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/min-rent/:minRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bedrooms plus other possibilities
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/min-rent/:minRent/',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bathroom plus other possibilities
            .when('/search/zip-code/:zipCode/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bathrooms/:numberOfBathrooms/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bathrooms/:numberOfBathrooms/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //minRent and other possibilities
            .when('/search/zip-code/:zipCode/min-rent/:minRent/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/min-rent/:minRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //maxRent and other possibilities
            .when('/search/zip-code/:zipCode/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed and bath plus other possibilities
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed and minRent and possibilities
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/min-rent/:minRent/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/min-rent/:minRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed and maxRent and possibilities
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bath and minRent and possibilities
            .when('/search/zip-code/:zipCode/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bath and maxRent and possibilities
            .when('/search/zip-code/:zipCode/number-of-bathrooms/:numberOfBathrooms/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //minRent and maxRent and possibilities
            .when('/search/zip-code/:zipCode/min-rent/:minRent/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed, bath, and minRent and possibilities
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/max-rent/:maxRent',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed, bath, and maxRent and possibilities
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })

            //bed, bath, minRent, maxRent, and radius
            .when('/search/zip-code/:zipCode/number-of-bedrooms/:numberOfBedrooms/number-of-bathrooms/:numberOfBathrooms/min-rent/:minRent/max-rent/:maxRent/radius/:radius',{
                templateUrl: 'app/views/listingSearchResults.html',
                controller: 'listingSearchController'
            })


            //go to specific listing
            .when('/listing/:listingId',{
                templateUrl: 'app/views/displayListing.html'
            })

            .when('/search/landlord-id/:landlordId',{
                templateUrl: 'app/views/listingSearchResults.html'
            })

            //catch all rules
            .when('/',{
                templateUrl: 'app/views/listingSearch.html',
                controller: 'listingSearchController'
            })
            .otherwise({redirectTo: '/'});
    }]);