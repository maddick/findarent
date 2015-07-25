angular
    .module('app')
    .controller('blogViewController',['$scope','$sce','BlogService',function($scope,$sce,BlogService){
        var promise = BlogService.getPublishedBlogs();

        $scope.blogs = [];
        $scope.allBlogs = [];
        var randomIndexes = [];

        var randomBlogPosts = function(indexes,randMod,numIndexes){
            numIndexes = (numIndexes === undefined || numIndexes > 3) ? 3 : numIndexes;
            if ( numIndexes !== 0 ) {
                var randNum = Math.floor(Math.random() * randMod);
                if ( indexes.length === 0 ) {
                    indexes.push(randNum);
                    randomBlogPosts( indexes, randMod, (numIndexes - 1))
                } else {
                    var existingValue = false;
                    for( var i = 0; i < indexes.length; i++ ) {
                        if (randNum === indexes[i]) {
                            existingValue = true;
                        }
                    }
                    if (existingValue) {
                        randomBlogPosts(indexes, randMod, numIndexes);
                    } else {
                        indexes.push(randNum);
                        randomBlogPosts(indexes, randMod, (numIndexes - 1));
                    }
                }
            }
        };

        promise.then(
            function(response){
                $scope.allBlogs = response.data['blogs'];

                randomBlogPosts(randomIndexes, $scope.allBlogs.length);

                for ( var i = 0; i < randomIndexes.length; i++ ) {
                    $scope.blogs.push($scope.allBlogs[randomIndexes[i]]);

                    console.log($scope.blogs);

                    var strInputCode = $scope.blogs[i]['post_content'];
                    if (strInputCode !== null) {
                        //var strTagStrippedText = strInputCode.replace(/<\/?[a-zA-Z0-9=:;,."'#!\/\-\s_]+(?:\s\/>|>|$)/g, "");
                        var strTagStrippedText = strInputCode.replace(/<\/?.+(?:\s\/>|>|$)/g, "");
                        strTagStrippedText = strTagStrippedText.replace(/&[#]?(?:[a-zA-Z]+|[0-9]+);/g,"");
                        $scope.blogs[i]['post_content'] = strTagStrippedText;
                        console.log(strTagStrippedText);
                    }
                }
            },
            function(response){

            }
        );
    }]);