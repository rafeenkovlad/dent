$(document).ready(function(){

    $("#update_profile").on("click", function(event){
        $("#update_profile").prop("disabled", true);

        event.stopPropagation();
        event.preventDefault();

        var form = new FormData($("#save_settings")[0]);


        $.ajax({
            url: ajaxUpdateProfile.url,
            method: 'POST',
            contentType: false,
            processData: false,
            data:form
        }).always(function(data){
            alert('данные сохранены');
            $("#update_profile").prop("disabled", false);
        });
    });
});
