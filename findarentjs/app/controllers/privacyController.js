angular
    .module('app')
    .controller('privacyController',['$scope','$sce','ContentService',function($scope,$sce,ContentService){
        $scope.privacy = {};
        var promise = ContentService.getContent('Privacy');

        promise.then(
            function(response){
                console.log(response);
                $scope.privacy.body = $sce.trustAsHtml(response.data.content[0].Body);
            },
            function(response){
                //console.log(response);
            }
        );
    }]);