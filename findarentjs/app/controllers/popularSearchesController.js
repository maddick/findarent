angular
    .module('app')
    .controller('popularSearchesController',['$scope','ListingSearch',function($scope, ListingSearch){
        var promise = ListingSearch.getPopularSearches();

        promise.then(
            function(response){
                $scope.popularSearches = response.data.searches;

                var numColumns = 4;
                var perColumn = Math.floor( $scope.popularSearches.length / numColumns );
                var addToColumnOne = $scope.popularSearches.length % numColumns;

                $scope.popularSearches.searchColumns = {};

                var index = 0;
                for ( var col = 1; col <= numColumns; col++ ) {
                    $scope.popularSearches.searchColumns['col' + col] = {};
                    var itemCount = ( col === 1 ) ? perColumn + addToColumnOne : perColumn;
                    for ( var item = 0; item < itemCount; item++ ) {
                        $scope.popularSearches.searchColumns['col' + col]['search' + item] = {};
                        $scope.popularSearches.searchColumns['col' + col]['search' + item].city = $scope.popularSearches[index].City;
                        $scope.popularSearches.searchColumns['col' + col]['search' + item].state = $scope.popularSearches[index].State;
                        index++;
                    }
                }
            },
            function(response){
                //console.log(response.data);
            }
        );
    }]);