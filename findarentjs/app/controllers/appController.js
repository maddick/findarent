angular
    .module('app')
    .controller('appController',['$scope', '$location', 'SearchURL',function($scope,$location,SearchURL){
        $scope.performSearch = function(){

            SearchURL.goToSearchURL($scope);

            if ( $scope.cityStateZipError ) {
                $('#city-state-or-zip-error').fadeIn().delay(3000).fadeOut();
            }
        }
        $scope.status = 'ready';
    }]);