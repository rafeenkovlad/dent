/*
For stackoverflow question: http://stackoverflow.com/questions/17859134/how-do-i-create-rating-histogram-in-jquery-mobile-just-like-rating-bar-in-google#17859134
*/

$(document).ready(function() {
    let raitData= $('span.bar-block > span.bar').attr('data-rait');
    let rait = {
        one:10,
        two:20,
        three:30,
        four:40,
        five:50
    };



    Object.keys(rait).forEach(function(key, index) {
        let i = false;
        const starsRait = '<i class="icon-star"></i>';
        if(raitData > rait[key]){
            $('span.bar-block > span.bar').attr('id', 'bar-'+key);
            $('span.index-count').append(starsRait);
        }

        // key: the name of the object key
        // index: the ordinal position of the key within the object
    });

    $('.bar span').hide();
    $('#bar-five').animate({
        width: '80%'}, 1000);
    $('#bar-four').animate({
        width: '65%'}, 1000);
    $('#bar-three').animate({
        width: '50%'}, 1000);
    $('#bar-two').animate({
        width: '35%'}, 1000);
    $('#bar-one').animate({
        width: '20%'}, 1000);

    setTimeout(function() {
        $('.bar span').fadeIn('slow');
    }, 1000);



});