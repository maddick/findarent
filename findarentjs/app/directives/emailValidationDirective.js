angular
    .module('app')
    .directive('emailValidation',['$timeout',function($timeout){
        return {
            restrict: 'A',
            link: function (scope, element, attrs){
                //var emailRegEx = /^(?:[a-zA-Z0-9#\-_~!$&'\(\)\*\+\.,;=:]+)@(?:(?:[a-zA-Z0-9]+)\-?(?:[a-zA-Z0-9]+)\.?)+(?:[a-zA-Z0-9]+)$/;
                var emailRegEx = /\b[a-zA-Z0-9\._%\+\-]+@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4}\b/;

                //this function will alter a validation boolean passed in by way of the attribute
                //value i.e., email-validation="the_boolean_passed_here". That boolean is used in
                //the controller for the page to disable the send email button.
                var delayValidation = function(){
                    if (delayValidation.timeout) {
                        clearTimeout(delayValidation.timeout);
                    }

                    var updateValue = attrs.emailValidation;

                    if ( updateValue !== undefined && updateValue !== '' && updateValue !== null ) {
                        delayValidation.timeout = setTimeout( function(){
                            var valid = emailRegEx.test(element.val());
                            if (!valid) {
                                scope.$apply(function(){scope.validation[updateValue] = true;});
                            } else {
                                scope.$apply(function(){scope.validation[updateValue] = false;});
                            }
                        },100);
                    }
                };

                $(element).keyup(function(){
                    delayValidation();
                });

                $(element).change(function(){
                    delayValidation();
                });
            }
        };
    }]);