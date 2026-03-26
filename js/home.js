// home.js — Home page scripts

function toggleNotif(event){
    event.preventDefault();
    let dropdown = document.getElementById("notifDropdown");
    dropdown.style.display =
        dropdown.style.display === "block" ? "none" : "block";
}

window.onclick = function(e){
    if(!e.target.closest(".bell")){
        document.getElementById("notifDropdown").style.display = "none";
    }
};
