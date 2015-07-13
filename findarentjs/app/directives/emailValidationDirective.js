angular
    .module('app')
    .directive('emailValidation',['$timeout',function($timeout){
        return {
            restrict: 'A',
            link: function (scope, element, attrs){
                var emailRegEx = /^(?:[a-zA-Z0-9#\-_~!$&'\(\)\*\+\.,;=:]+)@(?:(?:[a-zA-Z0-9]+)\-?(?:[a-zA-Z0-9]+)\.?)+(?:[a-zA-Z0-9]+)$/;

                var delayValidation = function(){
                    if (delayValidation.timeout) {
                        clearTimeout(delayValidation.timeout);
                    }

                    var updateValue = attrs.emailValidation;

                    delayValidation.timeout = setTimeout( function(){
                        var valid = emailRegEx.test(element.val());
                        var isEmpty = element.val() === '' || element.val() === null;
                        if (!valid && !isEmpty) {
                            element.parent().parent().addClass('has-error');
                            scope.$apply(function(){scope.validation[updateValue] = true;});
                        } else if (!valid && isEmpty) {
                            element.parent().parent().removeClass('has-error');
                            scope.$apply(function(){scope.validation[updateValue] = false;});
                        } else {
                            element.parent().parent().removeClass('has-error');
                            scope.$apply(function(){scope.validation[updateValue] = false;});
                        }
                    },250);
                };

                $(element).keyup(function(){
                    delayValidation()
                });
            }
        };
    }]);