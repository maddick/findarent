angular
    .module('app')
    .directive('autocomplete',function(){
        return{
            restrict : 'A',
            link : function(scope, element, attrs){
                $(element).autocomplete({
                    source : function(request, response){
                        $.ajax({
                            type: 'GET',
                            url: 'http://192.168.0.101:8080/listing/search/get-autocomplete-suggestions/autocomplete-data/' + $('#cityStateOrZip').val(),
                            success: function (data) {
                                response($.map( data.suggestedValues, function(item){
                                    return {
                                        label : item.Location,
                                        value : item.Location
                                    }
                                }));
                            },
                            error : function (textStatus) {
                                console.log(textStatus);
                            }
                        });
                    },
                    select : function(event, ui){
                        scope.listingSearchParams.cityStateOrZip = ui.item.value;
                        $(element).val(ui.item.value);
                    }
                });
            }
        }
    });