$.getScript( scriptUrl+"userprofile/user_profile_static/src/assets_img/css/styles.css");


var conf = {
    "totalpages": $('#paginate-page').attr('count-page'), // Total number of pages
    "show": 5, // [ Use Odd number ] How many to show
    "render": document.getElementById("paginate-page"), // Where to render the pagination
    "callback": callback // What happens when a page is clicked.
};

function callback(e){
    var page = e.target.getAttribute('data-page');

    /*$(function(){
        $.ajax({
            url: '',
            method: 'GET',
            data: {
                page:(page-1) },
            success : function(data){
                document.write(data);
                $('tbody.user-list').html(data);
            }

        })
    });*/
    $('tbody.user-list').load(apiUrl+'/?page='+(page-1)
        +'&user_id='+selfUserId
        +'&id_wp_page='+$('.user-list').attr('id')
        +'&id_post='+selfIdPost
        +'&nonce_img_upload='+$('input#nonce_img_upload').val()
        +'&nonce_img_del='+$('input#nonce_img_del').val());
    $.getScript( scriptUrl+"js/jquery.min.js");
    $.getScript( scriptUrl+"userprofile/user_profile_static/src/assets_img/intense.js");
    $.getScript( scriptUrl+"userprofile/user_profile_static/src/script.js");
    $.getScript( scriptUrl+"userprofile/user_profile_static/src/readmore.js");

     //alert(page);
    Paginate(page, conf)
}
Paginate(1, conf);

/*
-----------------------------------------
          PAGINATE
    Implementation below. Dont touch
-----------------------------------------
*/
function Paginate(p, conf){
    p = parseInt(p);
    if(conf.totalpages < conf.show)	conf.show = conf.totalpages;
    if(p > conf.totalpages) p = conf.totalpages;
    if(p < 1) p = 1;

    var hf2 = parseInt(Math.floor(conf.show/2));
    var hf1 = hf2;
    if(conf.show%2 == 0) // even number
        hf1--;


    conf.from = parseInt(p - hf1);
    conf.to = parseInt(p + hf2);
    while(conf.from < 1){
        conf.from ++;
    }
    while(conf.to > conf.totalpages){
        conf.to --;
    }
    var pivot = (p - conf.from); // Balancing (adding missing Left to Right)
    while(pivot < hf2 &&  conf.to < conf.totalpages){
        conf.to++;
        pivot++;
    }

    pivot = ( conf.to - p); // Balancing (adding missing Right to Left)
    if(conf.show%2 == 0){
        while(pivot <= hf1 &&  conf.from > 1){
            conf.from--;
            pivot++;
        }
    }else{
        while(pivot < hf1 &&  conf.from > 1){
            conf.from--;
            pivot++;
        }
    }

    var prev = ((p - 1) < 1) ? 1 : (p - 1),
        next = ((p + 1) >= conf.totalpages) ? conf.totalpages : (p + 1);

    var ul = document.createElement('ul');
    ul.setAttribute("class", "pagination");


    // Li <Last>
    var liFirst = document.createElement('li');
    liFirst.innerHTML = 'Первая';
    liFirst.setAttribute("data-page", 1);
    if(p == 1)
        liFirst.setAttribute("class", 'pageb page-disable');
    else{
        liFirst.setAttribute("class", 'pageb');
        registerClick(liFirst, conf.callback);
    }
    ul.appendChild(liFirst);

    // Li <Next >
    var liPrev = document.createElement('li');
    liPrev.innerHTML = ' < ';
    liPrev.setAttribute("data-page", prev);
    if(p == prev)
        liPrev.setAttribute("class", 'pageb page-disable');
    else{
        liPrev.setAttribute("class", 'pageb');
        registerClick(liPrev, conf.callback);
    }
    ul.appendChild(liPrev);

    for(var i = conf.from ; i <= conf.to; i++){
        var _page = document.createElement('li');
        _page.innerHTML = i;
        _page.setAttribute("data-page", i);
        if(i == p)
            _page.setAttribute("class", 'pageb active-page');
        else{
            _page.setAttribute("class", 'pageb');
            registerClick(_page, conf.callback);
        }
        ul.appendChild(_page);
    }

    // Li <First>
    var liNext = document.createElement('li');
    liNext.innerHTML = ' > ';
    liNext.setAttribute("data-page", next);
    if(p == next)
        liNext.setAttribute("class", 'pageb page-disable');
    else{
        liNext.setAttribute("class", 'pageb');
        registerClick(liNext, conf.callback);
    }
    ul.appendChild(liNext);

    // Li < Last>
    var liLast = document.createElement('li');
    liLast.innerHTML = 'Последняя';
    liLast.setAttribute("data-page", conf.totalpages);
    if(p == conf.totalpages)
        liLast.setAttribute("class", 'pageb page-disable');
    else{
        liLast.setAttribute("class", 'pageb');
        registerClick(liLast, conf.callback);
    }

    ul.appendChild(liLast);
    // return ul;
    conf.render.innerHTML = ""
    conf.render.appendChild(ul)

}
function registerClick(elem, doThis){
    elem.addEventListener("click", function(e){
        doThis(e);
    })
}