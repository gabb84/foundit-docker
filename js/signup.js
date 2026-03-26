// signup.js — Sign-up page scripts

function toggleLoginPassword() {
    const password = document.getElementById("loginPassword");
    const icon = document.getElementById("loginEye");

    if (password.type === "password") {
        password.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        password.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}

document.querySelector("form").addEventListener("submit", function(e){
    let email = document.getElementById("email").value;
    if(!email.endsWith("@student.hau.edu.ph")){
        alert("Use HAU student Email");
        e.preventDefault();
    }
});
