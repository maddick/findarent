angular
    .module('app')
    .directive('autocomplete',['$http',function($http){
        return{
            restrict : 'A',
            link : function(scope, element, attrs){
                scope.suggestedValues = {};
                $http.get('http://192.168.0.101:8080/listing/search/get-all-cities-and-zip-codes/')
                    .then(
                        function(value){
                            scope.suggestedValues = value.data['cities-and-zip-codes'];
                            var values = $.map( scope.suggestedValues, function(item){
                                return {
                                    label : item.city_name + ', ' + item.state_abbr + ' ' + item.zip_code,
                                    value : item.city_name + ', ' + item.state_abbr + ' ' + item.zip_code
                                }
                            });
                            $(element).autocomplete({
                                source : function(request, response){
                                    var results = $.ui.autocomplete.filter(values, request.term);
                                    response(results.slice(0,10));
                                },
                                select : function(event, ui){
                                    scope.listingSearchParams.cityStateOrZip = ui.item.value;
                                    $(element).val(ui.item.value);
                                }
                            });
                        },
                        function(error){
                            console.log(error);
                        }
                );
            }
        }
    }]);