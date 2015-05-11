angular
    .module('app')
    .controller('appController',['$scope','$location',function($scope,$location){
        $scope.performSearch = function(){
            var numberOfBedrooms    = ($scope.listingSearchParams !== undefined) ? $scope.listingSearchParams.numberOfBedrooms : undefined;
            var cityStateOrZip      = ($scope.listingSearchParams !== undefined) ? $scope.listingSearchParams.cityStateOrZip : undefined;
            var cityStateZipRegEx   = /^(\d{5})$|^((?:\b[a-zA-Z]+\b\s?)+,?\s?[a-zA-Z]{2})$/;
            var regEx               = new RegExp(cityStateZipRegEx);
            var regExArray          = regEx.exec(cityStateOrZip);
            var url                 = '/search';

            if (regExArray !== null) {
                if ( regExArray[1] !== undefined ) {
                    url = url + '/zip-code/' + regExArray[1];
                } else {
                    url = url + '/city-state/' + regExArray[2];
                }

                if ( numberOfBedrooms !== undefined ) {
                    url = url + '/number-of-bedrooms/' + numberOfBedrooms;
                }
                console.log(url);
                $location.path(url);
            } else {
                $('#city-state-or-zip-error').fadeIn().delay(3000).fadeOut();
            }
        }
    }]);