app
    .directive('autocomplete',['autocompleteDataService',function(autocompleteDataService){
        return{
            restrict : 'A',
            link : function(scope, element, attrs){
                $(element).autocomplete({
                    source : function(request, response){
                        var promise = autocompleteDataService.getSuggestions($(element).val());
                        promise.then(
                            function(data){
                                response($.map( data.suggestedValues, function(item){
                                    return {
                                        label : item.Location,
                                        value : item.Location
                                    }
                                }));
                            },
                            function(data){
                                //error handling
                            }
                        );
                    }
                });
            }
        }
    }]);