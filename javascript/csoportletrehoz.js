// ======= Globális változók =======
const nevsor_ul = document.querySelector("#nevsor ul");
const csoport_ul = document.querySelector("#csoport ul");
const input = document.getElementById("kereses");

const gomb_bedob = document.getElementById("bedob");
const gomb_kidob = document.getElementById("kidob");
const uj_csoport = document.getElementById("alap_gomb");
const bezar_gomb = document.getElementById("bezar_gomb");
const letrehoz_gomb = document.getElementById("letrehoz");
const uzenet_div = document.getElementById("uzenet");

const csoportnevek = document.querySelectorAll(".csoport h3");
const megjeleno = document.getElementsByClassName("megjeleno");

const osszes_diak_li = Array.from(nevsor_ul.querySelectorAll("li")).map(li => {
    const [azon, ...nevResz] = li.textContent.split(" - ");
    return {
        id: li.dataset.id,
        azonosito: azon ? azon.trim() : "",
        nev: nevResz.join(" - ").trim()
    };
});

function kattintaskor() {
    this.kattint = !this.kattint;
    this.style.backgroundColor = this.kattint ? "#80ff84" : "#ccc";
}

function frissit_nevsor() {
    nevsor_ul.innerHTML = "";
    osszes_diak_li.forEach(diak => {
        if (![...csoport_ul.querySelectorAll("li")].some(li => li.dataset.id === diak.id)) {
            const li = document.createElement("li");
            li.textContent = `${diak.azonosito} - ${diak.nev}`;
            li.dataset.id = diak.id;
            li.dataset.azonosito = diak.azonosito;
            li.dataset.nev = diak.nev;
            li.kattint = false;
            li.addEventListener("click", kattintaskor);
            nevsor_ul.appendChild(li);
        }
    });
}

input.addEventListener("keyup", () => {
    const search = input.value.trim().toLowerCase();
    Array.from(nevsor_ul.querySelectorAll("li")).forEach(li => {
        const nev = li.dataset.nev?.toLowerCase() || "";
        const azon = li.dataset.azonosito?.toLowerCase() || "";
        li.style.display = (nev.includes(search) || azon.includes(search)) ? "block" : "none";
    });
});

gomb_bedob.addEventListener("click", (e) => {
    e.preventDefault();
    Array.from(nevsor_ul.querySelectorAll("li")).forEach(li => {
        if (li.kattint) {
            li.kattint = false;
            li.style.backgroundColor = "#ccc";
            csoport_ul.appendChild(li);
        }
    });
});

gomb_kidob.addEventListener("click", (e) => {
    e.preventDefault();
    Array.from(csoport_ul.querySelectorAll("li")).forEach(li => {
        if (li.kattint) {
            li.kattint = false;
            li.style.backgroundColor = "#ccc";
            nevsor_ul.appendChild(li);
        }
    });
});

if (uj_csoport) {
    uj_csoport.addEventListener("click", () => {
        document.getElementById("flex").style.display = "flex";
        bezar_gomb.style.display = "block";
        letrehoz_gomb.style.display = "block";
        letrehoz_gomb.textContent = "Létrehoz";
        letrehoz_gomb.removeAttribute("data-id");
        csoport_ul.innerHTML = "";
        document.getElementById("csoportnev").value = "";

        nevsor_ul.innerHTML = "";
        osszes_diak_li.forEach(diak => {
            const li = document.createElement("li");
            li.textContent = `${diak.azonosito} - ${diak.nev}`;
            li.dataset.id = diak.id;
            li.dataset.azonosito = diak.azonosito;
            li.dataset.nev = diak.nev;
            li.kattint = false;
            li.addEventListener("click", kattintaskor);
            nevsor_ul.appendChild(li);
        });
    });
}

bezar_gomb.addEventListener("click", () => {
    document.getElementById("flex").style.display = "none";
});

document.querySelectorAll(".csoport.tanar").forEach(div => {
    div.addEventListener("click", () => {
        document.getElementById("flex").style.display = "flex";
        bezar_gomb.style.display = "block";
        letrehoz_gomb.style.display = "block";
        letrehoz_gomb.textContent = "Mentés";
        letrehoz_gomb.dataset.id = div.dataset.id;

        document.getElementById("csoportnev").value = div.querySelector("h3").textContent;

        const tagok = Array.from(div.querySelectorAll("ul li")).map(li => {
            const [azon, ...nevResz] = li.textContent.split(" - ");
            return {
                id: li.dataset.id,
                azonosito: azon ? azon.trim() : "",
                nev: nevResz.join(" - ").trim()
            };
        });

        csoport_ul.innerHTML = "";
        tagok.forEach(diak => {
            const li = document.createElement("li");
            li.textContent = `${diak.azonosito} - ${diak.nev}`;
            li.dataset.id = diak.id;
            li.dataset.azonosito = diak.azonosito;
            li.dataset.nev = diak.nev;
            li.kattint = false;
            li.addEventListener("click", kattintaskor);
            csoport_ul.appendChild(li);
        });
        frissit_nevsor();
    });
});

letrehoz_gomb.addEventListener("click", () => {
    const csoport_nev = document.getElementById("csoportnev").value.trim();
    const diakok = Array.from(csoport_ul.querySelectorAll("li")).map(li => li.dataset.id);
    const csoport_id = letrehoz_gomb.dataset.id || "";

    if (!csoport_nev || diakok.length === 0) {
        alert("Töltsd ki a csoport nevét és legyen legalább egy tag!");
        return;
    }

    let adat = "csoportnev=" + encodeURIComponent(csoport_nev);
    diakok.forEach(id => {
        adat += "&diakok[]=" + encodeURIComponent(id);
    });
    if (csoport_id) adat += "&csoport_id=" + encodeURIComponent(csoport_id);

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState === 4 && xhttp.status === 200) {
            alert(xhttp.responseText);
            location.reload();
        }
    };
    xhttp.open("POST", "../csoportok_letrehozasa/valasz.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(adat);
});

bezar_gomb.addEventListener("click", () => {
    bezar_gomb.style.display = "none";
});

for (let i = 0; i < megjeleno.length; i++) {
    megjeleno[i].style.position = "absolute";
    megjeleno[i].style.display = "none";
    megjeleno[i].style.background = "#fff";
    megjeleno[i].style.border = "1px solid #ccc";
    megjeleno[i].style.padding = "5px";
    megjeleno[i].style.borderRadius = "8px";
    megjeleno[i].style.boxShadow = "0 0 5px rgba(0,0,0,0.1)";
}

for (let i = 0; i < csoportnevek.length; i++) {
    const csoport = csoportnevek[i];
    csoport.addEventListener("mousemove", function(e) {
        megjeleno[i].style.display = "block";
        megjeleno[i].style.left = e.pageX + 10 + "px";
        megjeleno[i].style.top = e.pageY + 10 + "px";
    });
    csoport.addEventListener("mouseleave", function() {
        megjeleno[i].style.display = "none";
    });
}
