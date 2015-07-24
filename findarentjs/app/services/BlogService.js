angular
    .module('app')
    .factory('BlogService',['$http',function($http){
        return{
            getPublishedBlogs : function(){
                return $http.get('http://192.168.0.101:8080/content/content/get-blog-posts');
            }
        }
    }]);