angular
    .module('app')
    .directive('phoneValidation',[function(){
        return{
            restrict: 'A',
            link: function(scope, element, attrs){
                var phoneRegEx = /^[0-9]{10}&/;

                var delayValidation = function(){
                    if (delayValidation.timeout) {
                        clearTimeout(delayValidation.timeout);
                    }

                    var updateValue = attrs.phoneValidation;

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