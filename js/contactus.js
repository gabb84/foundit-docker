// contactus.js — toast animation on message sent

document.addEventListener("DOMContentLoaded", function(){
    var toast = document.getElementById("toast");
    if(toast){
        // Slide in
        setTimeout(function(){ toast.classList.add("show"); }, 100);
        // Slide out after 3.5s
        setTimeout(function(){ toast.classList.remove("show"); }, 3600);
    }
});