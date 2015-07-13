angular
    .module('app')
    .controller('emailFriendController',['$scope','$http',function($scope,$http){
        $scope.emailForm = {};
        $scope.emailForm.recipientAddress = '';
        $scope.emailForm.senderAddress = '';
        $scope.validation = {};
        $scope.validation.failedRecipientTest = true;
        $scope.validation.failedSenderTest = true;

        $scope.disableButton = function(){
            //console.log('disabledButton: ' + $scope.validation.disableButton);
            $scope.validation.disableButton = $scope.validation.failedRecipientTest || $scope.validation.failedSenderTest;
            return $scope.validation.disableButton;
        };

        $scope.disableButton();

        $scope.sendEmail = function(){
            //lock the button for sending
            $('#email-friend-button').prop('disabled',true);


            if (!$scope.validation.disableButton) {
                var emailData = {
                    listingTitle :  $scope.listing.Headline,
                    senderName : $scope.emailForm.senderName,
                    recipientName :$scope.emailForm.recipientName,
                    recipientAddress : $scope.emailForm.recipientAddress,
                    listingNumber : $scope.listing.ListingID,
                    listingURL : window.location.href
                };

                var promise  = $http(
                    {
                        method: 'POST',
                        url: 'http://192.168.0.101:8080/communication/message/send-email-to-friend/',
                        headers : { 'Content-Type' : 'application/json' },
                        data: emailData
                    }
                );
                promise.then(
                    function(response){
                        if (response.data.result == 'success') {
                            //dismiss the send email modal
                            $('#email-friend-button').prop('disabled',false);
                            $('#email-friend-modal').modal('hide');

                            //set the values for the result modal
                            $scope.resultModalMessage = 'Email Sent Successfully!';
                            $scope.resultModalTitle = 'Email Success!';
                            $('#email-friend-result-modal').modal('show');
                        }
                    },
                    function(response){
                        $('#email-friend-button').prop('disabled',false);
                        $('#email-friend-modal').modal('hide');

                        $scope.resultModalMessage = 'An error occurred when sending your email.';
                        $scope.resultModalTitle = 'Email Error';
                        $('#email-friend-result-modal').modal('show');
                    }
                );
            } else {
                alert('Invalid email addresses! Please correct the fields highlighted in red!');
            }
        };
    }]);