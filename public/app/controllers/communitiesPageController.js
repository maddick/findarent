angular
    .module('app')
    .controller('communitiesPageController',['$scope','CommunitySearch',function($scope,CommunitySearch){

        $scope.ct = {};
        var promiseCt = CommunitySearch.getCommunityCitiesByState('ct');
        var promiseRi = CommunitySearch.getCommunityCitiesByState('ri');

        promiseCt.then(
            function(response){
                $scope.ct = response.data;
                var numCols = ( $scope.ct.cities.length < 4 ) ? 3 : 4;
                var perColumn = ( Math.floor($scope.ct.cities.length / numCols) < 1 ) ? 1 : Math.floor($scope.ct.cities.length / numCols);
                var addToColumnOne = $scope.ct.cities.length % numCols;
                $scope.ct.cityColumns = {};
                $scope.ct.numColumns = 3;

                var index = 0;
                for ( var col = 1; col <= numCols; col++ ) {
                    $scope.ct.cityColumns['col' + col] = {};
                    var itemCount = (col === 1) ? perColumn + addToColumnOne : perColumn;
                    for( var item = 0; item < itemCount; item++ ) {
                        $scope.ct.cityColumns['col' + col]['row' + item] = {};
                        $scope.ct.cityColumns['col' + col]['row' + item].city = $scope.ct.cities[index]['City'];
                        index++;
                    }
                }
            },
            function(response){
                console.log(response);
            });
        promiseRi.then(
            function(response){
                $scope.ri = response.data;
                var numCols = ( $scope.ri.cities.length < 4 ) ? 3 : 4;
                var perColumn = ( Math.floor($scope.ri.cities.length / numCols) < 1 ) ? 1 : Math.floor($scope.ri.cities.length / numCols);
                var addToColumnOne = $scope.ri.cities.length % numCols;
                $scope.ri.cityColumns = {};
                $scope.ri.numColumns = 3;

                var index = 0;
                for ( var col = 1; col <= numCols; col++ ) {
                    $scope.ri.cityColumns['col' + col] = {};
                    var itemCount = (col === 1) ? perColumn + addToColumnOne : perColumn;
                    for( var item = 0; item < itemCount; item++ ) {
                        $scope.ri.cityColumns['col' + col]['row' + item] = {};
                        $scope.ri.cityColumns['col' + col]['row' + item].city = $scope.ri.cities[index]['City'];
                        index++;
                    }
                }
            },
            function(response){
                console.log(response);
            });

    }]);