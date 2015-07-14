angular
    .module('app')
    .controller('contactFormController',['$scope','$routeParams','$location','ListingRest',function($scope,$routeParams,$location,ListingRest){
        var path = $location.path();
        var search = $location.search();
        var forListing = path.indexOf('/Contact-Owner') != -1;
        var forBroker = path.indexOf('/Contact-Broker') != -1;
        var forCommunity = path.indexOf('/Contact-Community') != -1;

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

        $scope.message = {};
        $scope.message.subject = 'RE: ';
        $scope.message.senderFirstName = '';
        $scope.message.senderLastName = '';
        $scope.message.senderEmail = '';
        $scope.message.senderPhone = '';
        $scope.message.ownerName = '';
        $scope.message.listing = {};

        $scope.additionalField = {};
        $scope.additionalField.title = '';

        if (forListing) {
            //need to get the listing and send it along
            var promise = ListingRest.getListingById(search['listing-id']);

            promise.then(
                function(response){
                    $scope.message.listing = response.data.listing;
                    $scope.message.subject += $scope.message.listing.MarketingMessage;
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
                return false;
            }

            if ( $scope.message.MessageObj === null) {
                $scope.additionalField.title = '';
                return false;
            }

            if ( $scope.message.MessageObj.value == 'Pets') {
                $scope.additionalField.title = 'Please describe your pet to include breed if it is a dog and weight';
                return true;
            } else {
                $scope.additionalField.title = '';
                return false;
            }
        };

        $scope.submit = function(){
            var temp = $scope.message.MessageObj;

            var payload = {};
            $scope.message.senderMessage = $scope.message.MessageObj.label;
            payload.senderMessage = $scope.message.senderMessage;
            payload.senderFirstName = $scope.message.senderFirstName;
            payload.senderLastName = $scope.message.senderLastName;
            payload.senderEmail = $scope.message.senderEmail;
            payload.Phone = $scope.message.Phone;

            if (forListing) {
                payload.listing = $scope.message.listing;
                payload.type = 'LISTING';
            } else if (forBroker) {

            } else if (forCommunity) {

            }
            console.log(payload);

        };
    }]);