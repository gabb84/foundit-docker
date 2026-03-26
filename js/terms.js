// terms.js — Accordion toggle for Terms & Conditions sections

function toggleSection(header) {
    var section = header.parentElement;
    var isOpen = section.classList.contains("open");

    // Close all open sections first
    document.querySelectorAll(".section.open").forEach(function(s) {
        s.classList.remove("open");
    });

    // If it wasn't open, open it now
    if (!isOpen) {
        section.classList.add("open");
    }
}

// Open the first section by default on page load
document.addEventListener("DOMContentLoaded", function () {
    var first = document.querySelector(".section");
    if (first) first.classList.add("open");
});