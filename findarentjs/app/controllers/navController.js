angular
    .module('app')
    .controller('navController',['$scope','$location',function($scope,$location){
        $scope.blogURL = 'http://findarent.net/blog/index.php';
        $scope.postPropertyURL = 'http://findarent.net/list-property';

        $scope.goToExternalURL = function(url){
            $location.url(url);
        };
    }]);