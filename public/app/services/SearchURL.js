angular
    .module('app')
    .factory('SearchURL', ['$location', function($location){
        return {
            goToSearchURL : function($scope){
                var numberOfBedrooms    = ($scope.listingSearchParams !== undefined) ? $scope.listingSearchParams.numberOfBedrooms : undefined;
                var cityStateOrZip      = ($scope.listingSearchParams !== undefined) ? $scope.listingSearchParams.cityStateOrZip : undefined;
                var radius              = ($scope.listingSearchParams !== undefined) ? $scope.listingSearchParams.radius : undefined;
                var numberOfBathrooms   = ($scope.listingSearchParams !== undefined) ? $scope.listingSearchParams.numberOfBathrooms : undefined;
                var minRent             = ($scope.listingSearchParams !== undefined) ? $scope.listingSearchParams.minRent : undefined;
                var maxRent             = ($scope.listingSearchParams !== undefined) ? $scope.listingSearchParams.maxRent : undefined;
                var cityStateZipRegEx   = /^(\d{5})$|^((?:\b[a-zA-Z]+\b\s?)+,?\s?[a-zA-Z]{2})$/;
                var regEx               = new RegExp(cityStateZipRegEx);
                var regExArray          = regEx.exec(cityStateOrZip);
                var url                 = '/search?';

                if (regExArray !== null) {

                    $scope.cityStateZipError = false;

                    if ( regExArray[1] !== undefined ) {
                        url = url + 'zip-code=' + regExArray[1] + '&';
                    } else {
                        url = url + 'city-state=' + regExArray[2] + '&';
                    }

                    if ( numberOfBedrooms !== undefined && numberOfBedrooms !== '' ) {
                        url = url + 'number-of-bedrooms=' + numberOfBedrooms + '&';
                    }

                    if ( numberOfBathrooms !== undefined && numberOfBathrooms !== '' ) {
                        url = url + 'number-of-bathrooms=' + numberOfBathrooms + '&';
                    }

                    if ( minRent !== undefined && minRent !== '' ) {
                        url = url + 'min-rent=' + minRent + '&';
                    }

                    if ( maxRent !== undefined && maxRent !== '' ) {
                        url = url + 'max-rent=' + maxRent + '&';
                    }

                    if ( radius !== undefined && radius !== '' ) {
                        url = url + 'radius=' + radius + '&';
                    }

                    console.log(url);

                    //go to search url
                    $location.url(url);
                } else {
                    $scope.cityStateZipError = true;
                }
            }
        }
    }]);