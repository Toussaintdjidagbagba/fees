function sub() {
  
  // Création d'objet XMLHttpRequest
  if (window.XMLHttpRequest)
    var xhr = new XMLHttpRequest();
  else
    var xhr = new ActiveXObject("Microsoft.XMLHTTP");

  // On initialise notre requête avec open()
  
  xhr.onreadystatechange = function(){

    console.log('Excuted', xhr.readyState);
  }
  xhr.open("POST", 'login', true);
  xhr.setRequestHeader("_token", csrf_token());
  // Reponse au format json
//  xhr.responseType = "json";

  // send requete

  xhr.send();

/*
  xhr.onload = function(){
    if (xhr.status != 200) {
      alert('Erreur');
    }else{
      alert('success');
    }
  } */ 
}
/*
$( 'form' ).submit(function (e) {
    var don;
    don = new FormData();
    //don.append( 'file', input.files[0] );

    e.preventDefault();
    var t = $(this);
    n = $(this).closest('form');

    n.valid()( n.ajaxSubmit({
      url: 'login',
        data: don,
        processData: false,
        contentType: false,
        type: 'POST',
        dataType: "json",
        success: function (data) {
            switch (data.response){
              case 1 : 
                window.location.href = "dash1"; break;

            }
        }
    }));


    //e.preventDefault();
});