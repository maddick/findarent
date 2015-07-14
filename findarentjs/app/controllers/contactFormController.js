angular
    .module('app')
    .controller('contactFormController',['$scope','$routeParams','$location','$http','ListingRest',function($scope,$routeParams,$location,$http,ListingRest){
        var path = $location.path();
        var listingId = ($routeParams.listingId !== undefined) ? $routeParams.listingId : '';
        var forListing = path.indexOf('/Contact-Owner') != -1;
        var forBroker = path.indexOf('/Contact-Broker') != -1;
        var forCommunity = path.indexOf('/Contact-Community') != -1;

        if (forListing || forCommunity) {
            $scope.questions = [
                {
                    value: 'Visit',
                    label: 'Please call/email me to set up a visit to your apartment(s)'
                },
                {
                    value: 'Pets',
                    label: 'Do you allow pets?'
                },
                {
                    value: 'Availability',
                    label: 'When is the apartment available?'
                },
                {
                    value: 'Questions',
                    label: 'Other - Please call me so that I can ask questions'
                }
            ];
        }

        if (forBroker) {
            $scope.questions = [
                {
                    value: 'Pets',
                    label: 'Do any or the apartments you work with allow pets?'
                },
                {
                    value: 'Schedule',
                    label: 'Can your properties accommodate my moving schedule?'
                },
                {
                    value: 'Parking',
                    label: 'Do any of your properties have parking?'
                },
                {
                    value: 'Questions',
                    label: 'Please call so we can discuss my needs'
                }
            ];
        }

        $scope.message = {};
        $scope.message.subject = 'RE: ';
        $scope.message.senderFirstName = '';
        $scope.message.senderLastName = '';
        $scope.message.senderEmail = '';
        $scope.message.senderPhone = '';
        $scope.message.ownerName = '';
        $scope.message.listing = {};
        $scope.message.senderAdditionalMessage = '';

        $scope.additionalField = {};
        $scope.additionalField.title = '';
        $scope.additionalField.isRequired = false;

        $scope.validation = {};
        $scope.validation.senderEmailNotValid = true;
        $scope.validation.formIsValid = false;
        $scope.validation.senderFirstNameValid = false;
        $scope.validation.senderAdditionalMessageValid = false;
        $scope.validation.validateInfo = function() {
            console.log('called');
            $scope.validation.senderFirstNameValid = $scope.message.senderFirstName !== undefined &&
                $scope.message.senderFirstName !== null &&
                $scope.message.senderFirstName !== '';

            $scope.validation.senderAdditionalMessageValid = ($scope.additionalField.isRequired) ? $scope.message.senderAdditionalMessage !== undefined &&
                $scope.message.senderAdditionalMessage !== null &&
                $scope.message.senderAdditionalMessage !== '' : true;

            return ($scope.validation.senderFirstNameValid && $scope.validation.senderAdditionalMessageValid && !$scope.validation.senderEmailNotValid);

        };

        if (forListing) {
            //need to get the listing and send it along
            var promise = ListingRest.getListingById(listingId);

            promise.then(
                function(response){
                    $scope.message.listing = response.data.listing[0];
                    $scope.message.subject += $scope.message.listing.Headline;
                },
                function(response){
                    //TODO: set an error message
                }
            );
        } else if (forBroker) {

        } else if (forCommunity) {

        }

        $scope.showAdditionalField = function(){
            if ( $scope.message.MessageObj === undefined ) {
                $scope.additionalField.title = '';
                $scope.additionalField.isRequired = true;
                return false;
            }

            if ( $scope.message.MessageObj === null) {
                $scope.additionalField.title = '';
                $scope.additionalField.isRequired = true;
                return false;
            }

            if ( $scope.message.MessageObj.value == 'Pets') {
                $scope.additionalField.title = 'Please describe your pet to include breed if it is a dog and weight';
                $scope.additionalField.isRequired = true;
                return true;
            } else {
                $scope.additionalField.title = '';
                $scope.additionalField.isRequired = false;
                return false;
            }
        };

        $scope.submit = function(){
            var temp = $scope.validation.validateInfo();
            console.log(temp);

            var payload = {};

            if ( $scope.validation.validateInfo() ) {
                $scope.message.senderMessage = $scope.message.MessageObj.label;
                payload.senderMessage = $scope.message.senderMessage + ' : ' + $scope.message.senderAdditionalMessage;
                payload.senderName = $scope.message.senderFirstName + ' ' + $scope.message.senderLastName;
                payload.senderEmail = $scope.message.senderEmail;
                payload.Phone = $scope.message.Phone;

                if (forListing) {
                    payload.listing = $scope.message.listing;
                    payload.type = 'LISTING';
                } else if (forBroker) {

                } else if (forCommunity) {

                }

                var promise  = $http(
                    {
                        method: 'POST',
                        url: 'http://192.168.0.101:8080/communication/message/send-email-to-owner/',
                        headers : { 'Content-Type' : 'application/json' },
                        data: payload
                    }
                );

                promise.then(
                    function(response){
                        console.log('success');
                        console.log(response);
                    },
                    function(response) {
                        console.log('error');
                        console.log(response);
                    }
                );
            }

            console.log(payload);
            console.log(payload.senderEmail);

        };
    }]);