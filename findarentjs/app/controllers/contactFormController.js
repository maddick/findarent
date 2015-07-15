angular
    .module('app')
    .controller('contactFormController',['$scope','$routeParams','$location','$http','ListingRest','BrokerRest','CommunityRest',function($scope,$routeParams,$location,$http,ListingRest,BrokerRest,CommunityRest){
        var path = $location.path();
        var listingId = ($routeParams.listingId !== undefined) ? $routeParams.listingId : '';
        var brokerId = ($routeParams.brokerId !== undefined) ? $routeParams.brokerId : '';
        var communityId = ($routeParams.communityId !== undefined) ? $routeParams.communityId : '';
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

        //setup an object to hold our message variables
        $scope.message = {};
        $scope.message.subject = 'RE: ';
        $scope.message.senderFirstName = '';
        $scope.message.senderLastName = '';
        $scope.message.senderEmail = '';
        $scope.message.senderPhone = '';
        $scope.message.ownerName = '';
        $scope.message.resource = {};
        $scope.message.senderAdditionalMessage = '';

        //create an object to handle the additional field
        $scope.additionalField = {};
        $scope.additionalField.title = '';
        $scope.additionalField.isRequired = false;

        $scope.phoneInfo = {};

        //create an object for form validation
        $scope.validation = {};
        $scope.validation.senderEmailNotValid = true;
        $scope.validation.formIsValid = false;
        $scope.validation.senderFirstNameValid = false;
        $scope.validation.senderAdditionalMessageValid = false;
        $scope.validation.validateInfo = function() {
            $scope.validation.senderFirstNameValid = $scope.message.senderFirstName !== undefined &&
                $scope.message.senderFirstName !== null &&
                $scope.message.senderFirstName !== '';

            $scope.validation.senderAdditionalMessageValid = ($scope.additionalField.isRequired) ? $scope.message.senderAdditionalMessage !== undefined &&
                $scope.message.senderAdditionalMessage !== null &&
                $scope.message.senderAdditionalMessage !== '' : true;

            return ($scope.validation.senderFirstNameValid && $scope.validation.senderAdditionalMessageValid && !$scope.validation.senderEmailNotValid);

        };

        //get the appropriate resource (i.e., a listing, community, or broker)
        var promise = null;
        if (forListing) {
            promise = ListingRest.getListingById(listingId);

            promise.then(
                function(response){
                    $scope.message.resource = response.data.listing[0];
                    $scope.message.subject += $scope.message.resource.Headline;
                    $scope.phoneInfo.extension = $scope.message.resource.ListingID;
                },
                function(response){
                    //TODO: set an error message or redirect
                }
            );
        } else if (forBroker) {
            promise = BrokerRest.getBrokerById(brokerId);

            promise.then(
                function(response){
                    $scope.message.resource = response.data.brokers[0];
                    $scope.message.subject += $scope.message.resource.FirstName + ' ' + $scope.message.resource.LastName + ' - Broker # ' +$scope.message.resource.BrokerID;
                    $scope.phoneInfo.extension = $scope.message.resource.BrokerID;
                },
                function(response){
                    //TODO: set an error message or redirect
                }
            );
        } else if (forCommunity) {
            promise = CommunityRest.getCommunityById(communityId);

            promise.then(
                function(response){
                    $scope.message.resource = response.data.communities[0];
                    $scope.message.subject += $scope.message.resource.Community + ' - Community # ' + $scope.message.resource.CommunityID;
                    $scope.phoneInfo.extension = $scope.message.resource.CommunityID;
                },
                function(response){
                    //TODO: set an error message or redirect
                }
            );
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
                    payload.listing = $scope.message.resource;
                    payload.type = 'LISTING';
                } else if (forBroker) {
                    payload.listing = $scope.message.resource;
                    payload.type = 'BROKER';
                } else if (forCommunity) {
                    payload.listing = $scope.message.resource;
                    payload.type = 'COMMUNITY';
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