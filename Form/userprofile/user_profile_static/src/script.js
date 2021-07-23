
window.onload = function() {
    // Intensify all images with the 'intense' classname.
    var elements = document.querySelectorAll( '.list-gods-img' );
    Intense( elements );
}

$('input[type="file"]').each(function() {
    // get label text
    var label = $(this).parents('.form-group').find('label').text();
    label = (label) ? label : 'Upload File';

    // wrap the file input
    $(this).wrap('<div class="input-file"></div>');
    // display label
    $(this).before('<span class="btn">'+label+'</span>');
    // we will display selected file here
    $(this).before('<span class="file-selected"></span>');

    // file input change listener
    $(this).change(function(e){
        // Get this file input value
        var val = $(this).val();

        // Let's only show filename.
        // By default file input value is a fullpath, something like
        // C:\fakepath\Nuriootpa1.jpg depending on your browser.
        var filename = val.replace(/^.*[\\\/]/, '');

        // Display the filename
        $(this).siblings('.file-selected').text(filename);
    });
});

// Open the file browser when our custom button is clicked.
$('.input-file .btn').click(function() {
    $(this).siblings('input[type="file"]').trigger('click');
});

jQuery(function(){
    $('.img_list').change(function(){ // событие выбора файла
        var $form = $(this).parents('form');
        var id = $form.attr('id');

        var file = $form.find('input[class=img_list]')[0].files[0];
        var id_god = $form.find('input[id=id_god]').val();
        var id_post = $form.find('input[id=id_post]').val();
        var nonce = $form.find('input[id=nonce_img_upload]').val();
        var url_set_img = $form.attr('action');

        var $td = $(this).parents('td');
        $td.attr('class', 'img_list_td');

        var formData = new FormData();
        formData.append('img_god_upload', file);
        formData.append('id_god', id_god);
        formData.append('id_post', id_post);
        formData.append('nonce_img_upload', nonce);

        //console.log(formData.get('img_god_upload'));
        $.ajax({
            url: url_set_img,
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            cache: false,
            dataType : 'json',
            success: function(data)
            {
                if(data.error==null) {
                    $(".img_list_td").html(data.success);
                }else{
                    $(".img_list_td").html(data.error);
                }
            }

        });
        /*.done(function(data){
            $(".img_list_td").html(data.success);
        });*/
        //$("#"+id).submit(); // отправка формы
    });
});

function imgSubmit($i) { // событие удаление изображения
    var $form = $("#img_delete"+$i);

    var id_img = $form.find('input[id=id_img]').val();
    var id_list = $form.find('input[id=id_list]').val();
    var nonce = $form.find('input[id=nonce_img_del]').val();
    var url_del_img = $form.attr('action');

    var $td = $form.parents('td');
    $td.attr('class', 'img_list_td_del');

    $.ajax({
        url: url_del_img,
        type: 'GET',
        data: {
            'id_img':id_img,
            'id_list':id_list,
            'nonce_img_del': nonce
        },
        success: function(data)
        {
            if(data.error==null) {
                $(".img_list_td_del").html(data.success);
            }else{
                $(".img_list_td_del").html(data.error);
            }
        }
    });


    //$("#" + id_form).submit(); // отправка формы
};

