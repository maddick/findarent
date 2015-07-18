angular
    .module('app')
    .controller('equalController',['$scope','$sce','ContentService',function($scope,$sce,ContentService){
        $scope.equal ={};
        var promise = ContentService.getContent('EqualHousing');
        promise.then(
            function(response){
                $scope.equal.body = $sce.trustAsHtml(response.data.content[0].Body);
            },
            function(response){
                //console.log(response);
            }
        );
    }]);