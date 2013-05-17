function isValid(form)
{   
 	var s = form.value;
 	var re = /^([\.a-zA-Z0-9_-]+)$/;
 	if (! re.test(s)) {
	  	alert ("Seul des lettres et num�ros sont autoris�s: '"+ s + "' ne convient pas.");
 		form.value = '';
 		return false;
 	}
 	if ( s.length > 18 ) {
 	 	alert ("Maximum 18 caract�res: '"+ s + "' ne convient pas.");
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
	  	alert ("Seul des lettres et num�ros sont autoris�s: '"+ s + "' ne convient pas.");
 		form.value = defaultvalue;
 		return false;
 	}
 	if ((s=="mysql")||(s=="information_schema")) {
 	 	alert (s + "est une base syst�me. Choisissez un autre nom pour la base de donn�es eBrigade");
 	 	form.value = defaultvalue;
 		return false;
 	}
 	// All characters are letters or numbers.
    return true;
}

function isValid3(form)
{   
 	var s = form.value;
 	var re = /^([\'\ a-z��������A-Z0-9_-]*)$/;
 	if (! re.test(s)) {
	  	alert ("Attention seuls des lettres et des num�ros sont autoris�s: '"+ s + "' ne convient pas.");
 		form.value = '';
 		return false;
 	}
 	if ( s.length == 0 ) {
 	 	alert ("Attention une cha�ne vide n'est pas possible ici.");
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
	  		alert ("Seul des num�ros sont autoris�s: '"+ s + "' ne convient pas.");
 			form.value = defaultvalue;
 			return false;
 		}
 	}
 	if ( s.length > 10 ) {
 	 	alert ("Maximum 10 caract�res: '"+ s + "' ne convient pas.");
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
	  	alert ("Saisissez un code postal � 5 chiffres: '"+ s + "' ne convient pas.");
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
      // Cette fonction v�rifie le format JJ/MM/AAAA saisi et la validit� de la date.
      // Le s�parateur est d�fini dans la variable separateur
      var amin=1901; // ann�e mini
      var amax=2050; // ann�e maxi
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
         alert("L'ann�e n'est pas correcte."); ok=0;
      }
      if ( ((d.substring(2,3)!=separateur)||(d.substring(5,6)!=separateur)) && (ok==1) ) {
         alert("Les s�parateurs doivent �tre des "+separateur); ok=0;
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
      // Cette fonction v�rifie le format JJ-MM-AAAA saisi et la validit� de la date.
      // Le s�parateur est d�fini dans la variable separateur
      var amin=1901; // ann�e mini
      var amax=2050; // ann�e maxi
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
         alert("L'ann�e n'est pas correcte."); ok=0;
      }
      if ( ((d.substring(2,3)!=separateur)||(d.substring(5,6)!=separateur)) && (ok==1) ) {
         alert("Les s�parateurs doivent �tre des "+separateur); ok=0;
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
