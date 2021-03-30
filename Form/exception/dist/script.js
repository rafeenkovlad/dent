$(window).load(function(){
	        	$('#myModal').modal('show');
	        });

			var url= document.URL;

			var lang = url.search ('/fr/');

			if(lang == -1){
			document.body.innerHTML = document.body.innerHTML.replace('replace', 'Nog geen fan van moto.be?<br/>Vind onze Facebook-pagina leuk!');
			}

			else{
				document.body.innerHTML = document.body.innerHTML.replace('replace', 'Pas encore fan de moto.be ?<br/>Likez notre page facebook !');
			}