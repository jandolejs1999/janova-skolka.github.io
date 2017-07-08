function spustit(){
	var button = document.getElementById("main_button");
	var names = document.getElementsByTagName("input");
	for(var i = 0; i < names.length; i++){
		document.getElementById(names[i].getAttribute("id")).oninput = kontrola;
	}
	button.onclick = hlaska;
	kontrola();
}

function hlaska(){
	var jmeno = document.getElementById("name").value;
	var datum = document.getElementById("birth_year").value;
	var vyska = document.getElementById("height").value;

	if(idValid("name") && idValid("birth_year") && idValid("height")){
		alert("Zadali jste tyto údaje:" + "\nJméno: " + jmeno + "\nNarozen: " + datum + "\nVýška: " + vyska + " m");
	}
	else{
		alert("CHYBA: Pole obsahují neplatnou hodnotu");
	}
}

function kontrola(){
	var button = document.getElementById("main_button");

	if(idValid("name") && idValid("birth_year") && idValid("height")){
		button.removeAttribute("disabled");
	}
	else{
		button.setAttribute("disabled", "disabled");
	}
}

function idValid(name){
	var input = document.getElementById(name);
	var value = input.value;

	var requiredFormat = input.getAttribute("data-format");
	if(requiredFormat) {
		requiredFormat = new RegExp("^" + requiredFormat + "$");
	}

	var isEmpty = value.match(/^\s*$/);
	var isRequired = input.getAttribute("data-required") == "yes";
	var isValidFormat = requiredFormat ? value.match(requiredFormat) : true; // If format not defined => always valid

	if(isEmpty){
		return ! isRequired;
	}

	return isValidFormat;
}
