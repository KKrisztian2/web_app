var feladatokSzama = 0;

var elso_form = document.getElementById("elso");
var masodik_form = document.getElementById("masodik");
var harmadik_form = document.getElementById("harmadik");

var tovabb_gomb = document.getElementsByClassName("alap_gomb");

for (var i = 0; i < tovabb_gomb.length - 1; i++) {
	tovabb_gomb[i].addEventListener("click", kovetkezo);
}

// Összes témakör és szintek checkbox kezelés
var osszes_temakor = document.getElementById("osszes_temakor");
osszes_temakor.addEventListener("change", temakorok_checkboxok);

// Kijelöl minden checkboxot, kivéve az “összes” sajátját (keresőbarát verzió)
var temakorok = document.querySelectorAll("#elso input[type='checkbox']:not(#osszes_temakor)");
for (var i = 0; i < temakorok.length; i++) {
	temakorok[i].addEventListener("change", osszes_temakor_checked);
}

var osszes_szint = document.getElementById("osszes_szint");
osszes_szint.addEventListener("change", szintek_checkboxok);
var szintek = document.querySelectorAll("#masodik > input[name^='szint']");

function temakorok_checkboxok() {
	if (!this.checked) {
		temakorok.forEach(cb => cb.disabled = false);
	} else {
		temakorok.forEach(cb => {
			if (cb.offsetParent !== null) { // csak a látható checkboxokat kezeli
				cb.checked = true;
				cb.disabled = true;
			}
		});
	}
}

function kovetkezo() {
	this.parentElement.style.display = "none";
	this.parentElement.nextElementSibling.style.display = "block";
}

function szintek_checkboxok() {
	var szintek = document.querySelectorAll("#masodik > input[name^='szint']");
	if (!this.checked) {
		for (var i = 0; i < szintek.length; i++) {
			szintek[i].disabled = false;
		}
	} else {
		for (var i = 0; i < szintek.length; i++) {
			szintek[i].checked = true;
			szintek[i].disabled = true;
		}
	}
}

function osszes_temakor_checked() {
	var db = 0;
	for (var i = 0; i < temakorok.length; i++) {
		if (temakorok[i].checked) {
			db++;
		}
	}
	if (db === temakorok.length) {
		osszes_temakor.checked = true;
	} else {
		osszes_temakor.checked = false;
	}
}

function osszes_szint_checked() {
	var db = 0;
	for (var i = 0; i < szintek.length; i++) {
		if (szintek[i].checked) {
			db++;
		}
	}
	if (db === szintek.length) {
		osszes_szint.checked = true;
	} else {
		osszes_szint.checked = false;
	}
}

var temakor_tovabb = document.getElementById("temakor_tovabb");
if (temakor_tovabb) temakor_tovabb.addEventListener("click", szintekLekerese);

var szint_tovabb = document.getElementById("szint_tovabb");
if (szint_tovabb) szint_tovabb.addEventListener("click", feladatokSzamaLekeres);

var szintek = Array.from(document.querySelectorAll("#masodik > input[name^='szint']"));

function szintekLekerese() {
	const kivalasztottTemak = Array.from(temakorok)
		.filter(cb => osszes_temakor.checked || cb.checked)
		.map(cb => cb.value);

	ajax("../AJAX/index.php", "action=szintek&temakorok=" + encodeURIComponent(JSON.stringify(kivalasztottTemak)), szintekMegjelenit);
}

function szintekMegjelenit(json) {
	const elerhetoSzintek = JSON.parse(json); // pl. [1,3,4]
	szintek.forEach(cb => {
		cb.classList.toggle("hidden", !elerhetoSzintek.includes(parseInt(cb.value)));
		cb.previousElementSibling.classList.toggle("hidden", !elerhetoSzintek.includes(parseInt(cb.value)));
	});
	kovetkezo(document.getElementById("masodik"));
}

function feladatokSzamaLekeres() {
	const kivalasztottTemak = Array.from(temakorok)
		.filter(cb => osszes_temakor.checked || cb.checked)
		.map(cb => cb.value);
	const kivalasztottSzintek = szintek
		.filter(cb => osszes_szint.checked || cb.checked)
		.map(cb => parseInt(cb.value));

	ajax(
		"../AJAX/index.php",
		"action=feladatokSzama&temakorok=" + encodeURIComponent(JSON.stringify(kivalasztottTemak)) +
		"&szintek=" + encodeURIComponent(JSON.stringify(kivalasztottSzintek)),
		feladatokSzamaBeallit
	);

	kovetkezo(document.getElementById("harmadik"));
}

function feladatokSzamaBeallit(valasz) {
	try {
		const adat = JSON.parse(valasz);
		if (typeof adat === "object" && adat.cnt !== undefined) {
			feladatokSzama = parseInt(adat.cnt);
		} else {
			feladatokSzama = parseInt(valasz);
		}
	} catch {
		feladatokSzama = parseInt(valasz);
	}

	console.log("Feladatok maximális száma:", feladatokSzama);
}

function feladatSzamaEllenorzes() {
	const kivalasztott = parseInt(document.getElementById("szam").value);
	if (feladatokSzama < kivalasztott) {
		document.getElementById("warning").style.display = "block";
		document.getElementById("warning_text").innerText =
			"Maximum csak " + feladatokSzama + " feladat választható!";
		return false;
	}
	return true;
}

function ajax(url, adat, fuggveny) {
	const xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function () {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			fuggveny(xhttp.responseText);
		}
	};
	xhttp.open("POST", url, true);
	xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhttp.send(adat);
}

// Dialóg bezárás
var close_dialog = document.getElementById("close_dialog");
if (close_dialog) close_dialog.addEventListener("click", dialogBezar);

function dialogBezar() {
	var dialogAblak = this.parentElement.parentElement;
	dialogAblak.removeAttribute("style");
}
