function isValid(form)
{   
 	var s = form.value;
 	var re = /^([\.a-zA-Z0-9_-]+)$/;
 	if (! re.test(s)) {
	  	alert ("Seul des lettres et numéros sont autorisés: '"+ s + "' ne convient pas.");
 		form.value = '';
 		return false;
 	}
 	if ( s.length > 18 ) {
 	 	alert ("Maximum 18 caractères: '"+ s + "' ne convient pas.");
 		form.value = '';
 		return false;
 	}

 	// All characters are letters or numbers.
    return true;
}

function isValid2(form, defaultvalue)
{   
 	var s = form.value;
 	var re = /^([\.a-zA-Z0-9_-]*)$/;
 	if (! re.test(s)) {
	  	alert ("Seul des lettres et numéros sont autorisés: '"+ s + "' ne convient pas.");
 		form.value = defaultvalue;
 		return false;
 	}
 	if ((s=="mysql")||(s=="information_schema")) {
 	 	alert (s + "est une base système. Choisissez un autre nom pour la base de données eBrigade");
 	 	form.value = defaultvalue;
 		return false;
 	}
 	// All characters are letters or numbers.
    return true;
}

function isValid3(form)
{   
 	var s = form.value;
 	var re = /^([\'\ a-zéèêçïëàüA-Z0-9_-]*)$/;
 	if (! re.test(s)) {
	  	alert ("Attention seuls des lettres et des numéros sont autorisés: '"+ s + "' ne convient pas.");
 		form.value = '';
 		return false;
 	}
 	if ( s.length == 0 ) {
 	 	alert ("Attention une chaîne vide n'est pas possible ici.");
 	 	return false;
 	}
 	// All characters are letters or numbers.
    return true;
} 

function checkPhone(form,defaultvalue)
{   
 	var s = form.value;
 	var re = /^([0-9]+)$/;
 	if ( s.length > 0 ) {
 		if (! re.test(s)) {
	  		alert ("Seul des numéros sont autorisés: '"+ s + "' ne convient pas.");
 			form.value = defaultvalue;
 			return false;
 		}
 	}
 	if ( s.length > 10 ) {
 	 	alert ("Maximum 10 caractères: '"+ s + "' ne convient pas.");
 		form.value = defaultvalue;
 		return false;
 	}

 	// All characters are numbers.
    return true;
}

function checkNumber(form,defaultvalue)
{   
 	var s = form.value;
 	var re = /^([0-9]+)$/;
 	if (! re.test(s)) {
	  	alert ("Saisissez un nombre: '"+ s + "' ne convient pas.");
 		form.value = defaultvalue;
 		return false;
 	}
 	// All characters are numbers.
    return true;
}

function checkZipcode(form,defaultvalue)
{   
 	var s = form.value;
 	var re = /^([0-9]+)$/;
 	if (! re.test(s)) {
	  	alert ("Saisissez un code postal à 5 chiffres: '"+ s + "' ne convient pas.");
 		form.value = defaultvalue;
 		return false;
 	}
 	if ( s.length > 5 ) {
 	 	alert ("Maximum 5 chiffres: '"+ s + "' ne convient pas.");
 		form.value = defaultvalue;
 		return false;
 	}
 	// All characters are numbers.
    return true;
}

function checkDate(form) {
 	  var d = form.value;
 	  if (d=='') return true; 
      // Cette fonction vérifie le format JJ/MM/AAAA saisi et la validité de la date.
      // Le séparateur est défini dans la variable separateur
      var amin=1901; // année mini
      var amax=2050; // année maxi
      var separateur="/"; // separateur entre jour/mois/annee
      var j=(d.substring(0,2));
      var m=(d.substring(3,5));
      var a=(d.substring(6));
      var ok=1;
      if ( ((isNaN(j))||(j<1)||(j>31)) && (ok==1) ) {
         alert("Le jour n'est pas correct."); ok=0;
      }
      if ( ((isNaN(m))||(m<1)||(m>12)) && (ok==1) ) {
         alert("Le mois n'est pas correct."); ok=0;
      }
      if ( ((isNaN(a))||(a<amin)||(a>amax)) && (ok==1) ) {
         alert("L'année n'est pas correcte."); ok=0;
      }
      if ( ((d.substring(2,3)!=separateur)||(d.substring(5,6)!=separateur)) && (ok==1) ) {
         alert("Les séparateurs doivent être des "+separateur); ok=0;
      }
      if (ok==1) {
         var d2=new Date(a,m-1,j);
         j2=d2.getDate();
         m2=d2.getMonth()+1;
         a2=d2.getFullYear();
         if (a2<=100) {a2=1900+a2}
         if ( (j!=j2)||(m!=m2)||(a!=a2) ) {
            alert("La date "+d+" n'existe pas !");
            ok=0;
         }
      }
      if (ok==0) {
       	form.value = '';
       	return false;	
      }
      return true;
   }
   
function checkDate2(form) {
 	  var d = form.value;
 	  if (d=='') return true; 
      // Cette fonction vérifie le format JJ-MM-AAAA saisi et la validité de la date.
      // Le séparateur est défini dans la variable separateur
      var amin=1901; // année mini
      var amax=2050; // année maxi
      var separateur="-"; // separateur entre jour-mois-annee
      var j=(d.substring(0,2));
      var m=(d.substring(3,5));
      var a=(d.substring(6));
      var ok=1;
      if ( ((isNaN(j))||(j<1)||(j>31)) && (ok==1) ) {
         alert("Le jour n'est pas correct."); ok=0;
      }
      if ( ((isNaN(m))||(m<1)||(m>12)) && (ok==1) ) {
         alert("Le mois n'est pas correct."); ok=0;
      }
      if ( ((isNaN(a))||(a<amin)||(a>amax)) && (ok==1) ) {
         alert("L'année n'est pas correcte."); ok=0;
      }
      if ( ((d.substring(2,3)!=separateur)||(d.substring(5,6)!=separateur)) && (ok==1) ) {
         alert("Les séparateurs doivent être des "+separateur); ok=0;
      }
      if (ok==1) {
         var d2=new Date(a,m-1,j);
         j2=d2.getDate();
         m2=d2.getMonth()+1;
         a2=d2.getFullYear();
         if (a2<=100) {a2=1900+a2}
         if ( (j!=j2)||(m!=m2)||(a!=a2) ) {
            alert("La date "+d+" n'existe pas !");
            ok=0;
         }
      }
      if (ok==0) {
       	form.value = '';
       	return false;	
      }
      return true;
   }


function mailCheck(form,defaultvalue) {

 	var s = form.value;
 	var re = /^[\w_.~-]+@[\w][\w.\-]*[\w]\.[\w][\w.]*[a-zA-Z]$/;
 	if ((! re.test(s)) && ( s != '' )){
	  	alert ("L'adresse email saisie est incorrecte: '"+ s + "' ne convient pas.");
 		form.value = defaultvalue;
 		return false;
 	}
 	// All characters are letters or numbers.
    return true;
}
