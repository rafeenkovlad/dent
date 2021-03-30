$(window).load(function(){
	$('#myModal').modal('show');
});

var url= document.URL;

let name = document.getElementsByName("alert_name")[0].value;

document.body.innerHTML = document.body.innerHTML.replace('replace', `${name}`);
