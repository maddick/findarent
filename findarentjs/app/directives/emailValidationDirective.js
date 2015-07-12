angular
    .module('app')
    .directive('emailvalidation',[function(){
        return {
            restrict: 'A',
            link: function (scope, element, attrs){
                var emailRegEx = /^(?:[a-zA-Z0-9#\-_~!$&'\(\)\*\+\.,;=:]+)@(?:(?:[a-zA-Z0-9]+)\-?(?:[a-zA-Z0-9]+)\.?)+(?:[a-zA-Z0-9]+)$/;
                $(element).change(function(){
                    var valid = emailRegEx.test($(element).val());
                    var isEmpty = $(element).val() === '' || $(element).length == 0;
                    //console.log('isEmpty: ' +isEmpty + ' valid: ' + valid);
                    if (!valid && !isEmpty) {
                        $(element).parent().parent().addClass('has-error');
                    } else if (!valid && isEmpty) {
                        $(element).parent().parent().removeClass('has-error');
                    } else {
                        $(element).parent().parent().removeClass('has-error');
                    }
                });

                $(element).keyup(function(){
                    var valid = emailRegEx.test($(element).val());
                    var isEmpty = $(element).val() === '' || $(element).length == 0;
                    //console.log('isEmpty: ' +isEmpty + ' valid: ' + valid);
                    if (!valid && !isEmpty) {
                        $(element).parent().parent().addClass('has-error');
                    } else if (!valid && isEmpty) {
                        $(element).parent().parent().removeClass('has-error');
                    } else {
                        $(element).parent().parent().removeClass('has-error');
                    }
                });
            }
        };
    }]);