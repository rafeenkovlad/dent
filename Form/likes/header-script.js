$(document).ready(function(){
    $("#addlikes").on("click", function(){
        $("#addlikes").prop("disabled", true);
        const url = document.URL;
        let addlike = '+1';

        $.ajax({
            url: "",
            method: 'POST',
            data: {addlike: addlike}
        }).done(function(data){
            let count = document.getElementById('count');
            count.innerHTML += '(+1)';

        });
    });
});

var likeTarget = $('.comment-like');
var likeCount = $(likeTarget).find('.comment-like-count').text();

$(likeTarget).click ( function() {
    $(this).removeClass('is-unliked').addClass('is-liked').find('.comment-like-count').text(+likeCount+1);
});