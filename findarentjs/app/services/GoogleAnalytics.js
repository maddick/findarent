angular
    .module('app')
    .factory('GoogleAnalytics',['$window','$location',function($window,$location){
        return{
            track : function(){
                //$window.ga('create','UA-65299743-1','auto');
                $window.ga('send','pageview', { page: $location.url()});
            }
        }
    }]);