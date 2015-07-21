angular
    .module('app')
    .controller('appController',['$scope', '$location', 'SearchURL','GoogleAnalytics','$rootScope',function($scope,$location,SearchURL,GoogleAnalytics,$rootScope){
        GoogleAnalytics.track();
        $scope.performSearch = function(){

            SearchURL.goToSearchURL($scope);

            if ( $scope.cityStateZipError ) {
                $('#city-state-or-zip-error').fadeIn().delay(3000).fadeOut();
            }
        };
    }]);