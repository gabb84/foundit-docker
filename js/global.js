// toggleMenu — sidebar + overlay toggle
function toggleMenu(){
    document.getElementById("sidebar").classList.toggle("active");
    var overlay = document.getElementById("overlay") || document.getElementById("menuOverlay");
    if(overlay) overlay.classList.toggle("active");
}
