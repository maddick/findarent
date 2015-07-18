angular
    .module('app')
    .controller('aboutController',['$scope','$sce','ContentService',function($scope,$sce,ContentService){
        $scope.about = {};
        var promise = ContentService.getContent('About');
        promise.then(
            function(response){
                $scope.about.body = $sce.trustAsHtml(response.data.content[0].Body);
            },
            function(response){
                //console.log(response);
            }
        );
    }]);