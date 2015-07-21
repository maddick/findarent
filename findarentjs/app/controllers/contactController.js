angular
    .module('app')
    .controller('contactController',['$scope','$sce','$http','ContentService','$window',function($scope,$sce,$http,ContactService,$window){
        $scope.contact = {};
        var promise = ContactService.getContent('Contact');

        promise.then(
            function(response){
                $scope.contact.body = $sce.trustAsHtml(response.data.content[0].Body);
            },
            function(response){

            }
        );

        //setup an object to hold our message variables
        $scope.message = {};
        $scope.message.senderName = '';
        $scope.message.senderEmail = '';
        $scope.message.senderPhone = '';
        $scope.message.senderCompnay = '';
        $scope.message.senderMessage = '';

        //email results
        $scope.emailResult = {};

        $scope.validation = {};
        $scope.validation.senderEmailNotValid = true;
        $scope.validation.formIsValid = false;
        $scope.validation.senderNameValid = false;
        $scope.validation.senderMessageValid = false;
        $scope.validation.validateInfo = function() {
            $scope.validation.senderNameValid = $scope.message.senderName !== undefined &&
            $scope.message.senderName !== null &&
            $scope.message.senderName !== '';

            $scope.validation.senderMessageValid = $scope.message.senderMessage !== null &&
                                                   $scope.message.senderMessage !== '';

            return ($scope.validation.senderNameValid && $scope.validation.senderMessageValid && !$scope.validation.senderEmailNotValid);

        };

        $scope.submit = function(){
            if ( $scope.validation.validateInfo() ) {

                var promise = $http({
                    method: 'POST',
                    url: 'http://192.168.0.101:8080/communication/message/send-email-to-far/',
                    headers: {'Content-Type':'application/json'},
                    data: new Blob([JSON.stringify($scope.message)])
                });

                promise.then(
                    function(response){
                        $scope.emailResult.MessageTitle = 'Contact Request Sent';
                        $scope.emailResult.Message = 'Your information has been submitted to FindARent.net. Our staff will reply to you soon with the requested information.';

                        $('#contact-far-section').fadeOut(400,function(){
                            $('#contact-email-message').fadeIn();
                        });
                    },
                    function(response){
                        console.log(response);
                        $scope.emailResult.MessageTitle = 'Error Sending Request';
                        $scope.emailResult.Message = 'There was an error attempting to send your request. Please refresh the page and try again.';

                        $('#contact-far-section').fadeOut(400,function(){
                            $('#contact-email-message').fadeIn();
                        });
                    }
                );
            }
        }

        $scope.goBack = function(){
            $window.history.back();
        };

    }]);