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