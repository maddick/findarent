angular
    .module('app')
    .controller('contactFormController',['$scope','$routeParams','$location','$http','ListingRest','BrokerRest','CommunityRest','$window',function($scope,$routeParams,$location,$http,ListingRest,BrokerRest,CommunityRest,$window){
        var path = $location.path();
        var listingId = ($routeParams.listingId !== undefined) ? $routeParams.listingId : '';
        var brokerId = ($routeParams.brokerId !== undefined) ? $routeParams.brokerId : '';
        var communityId = ($routeParams.communityId !== undefined) ? $routeParams.communityId : '';
        var forListing = path.indexOf('/Contact-Owner') != -1;
        var forBroker = path.indexOf('/Contact-Broker') != -1;
        var forCommunity = path.indexOf('/Contact-Community') != -1;

        //show loading screen
        $('#display-contact-loading').fadeIn();

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

        $scope.emailResult = {};

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

                    $('#display-contact-loading').fadeOut(400, function(){
                        $('#contact-owner-section').fadeIn();
                    });
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

                    $('#display-contact-loading').fadeOut(400, function(){
                        $('#contact-owner-section').fadeIn();
                    });
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

                    $('#display-contact-loading').fadeOut(400, function(){
                        $('#contact-owner-section').fadeIn();
                    });
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
            $('#email-submit-button').prop('disabled',true);
            var temp = $scope.validation.validateInfo();

            var payload = {};

            if ( $scope.validation.validateInfo() ) {
                $scope.message.senderMessage = $scope.message.MessageObj.label;
                payload.senderMessage = $scope.message.senderMessage + ' : ' + $scope.message.senderAdditionalMessage;
                //payload.senderName = $scope.message.senderFirstName + ' ' + $scope.message.senderLastName;
                payload.senderFirstName = $scope.message.senderFirstName;
                payload.senderLastName = $scope.message.senderLastName;
                payload.senderEmail = $scope.message.senderEmail;
                payload.senderPhone = $scope.message.senderPhone;

                if (forListing) {
                    payload.resource = $scope.message.resource;
                    payload.type = 'LISTING';
                } else if (forBroker) {
                    payload.resource = $scope.message.resource;
                    payload.type = 'BROKER';
                } else if (forCommunity) {
                    payload.resource = $scope.message.resource;
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
                        $scope.emailResult.MessageTitle = 'Success!';
                        if (forListing) {
                            $scope.emailResult.Message = 'Thank you for submitting your inquiry about ' +
                            'Listing #' + $scope.message.resource.ListingID + ' at FindARent.net. We have ' +
                            'contacted the property owner/manager with your inquiry - the owner/manager will be ' +
                            'contacting you shortly.';
                        } else if (forBroker) {
                            $scope.emailResult.Message = 'Your email has been successfully sent to ' +
                            $scope.message.resource.FirstName + ' ' + $scope.message.resource.LastName + '. ' +
                            'Click the button \"Back To Searching\" to return to the page you were viewing.';
                        } else if (forCommunity) {
                            $scope.emailResult.Message = 'Your email has been successfully sent to ' +
                            $scope.message.resource.Community + '. Click the button \"Back To Searching\" to return ' +
                            'to the page you were viewing.';
                        }

                        $('#contact-owner-section').fadeOut(400,function(){
                            $('#contact-email-message').fadeIn();
                        });
                    },
                    function(response) {
                        //console.log('error');
                        //console.log(response);
                        $scope.emailResult.MessageTitle = 'Error!';
                        if (forListing) {
                            $scope.emailResult.Message = 'There was an error with your request. Please return to the ' +
                            'page for the listing you would like to send a request for and click the contact owner button ' +
                            'to try again.';
                        } else if (forBroker) {
                            $scope.emailResult.Message = 'There was an error with your request.  Please return to the ' +
                            'page for the broker you wish to contact and click the Broker email button again.';
                        } else if (forCommunity) {
                            $scope.emailResult.Message = 'There was an error with your request.  Please return to the ' +
                            'page for the community you wish to contact and click the Community email button again.';
                        }

                        $('#contact-owner-section').fadeOut(400,function(){
                            $('#contact-email-message').fadeIn();
                        });
                    }
                );
            }

            //console.log(payload);
            //console.log(payload.senderEmail);

        };

        $scope.goBack = function(){
            $window.history.back();
        };
    }]);