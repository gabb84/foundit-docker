// list.js — List item page scripts

function toggleOtherCategory(){
    var category = document.getElementById("category").value;
    var otherBox = document.getElementById("otherCategoryBox");
    otherBox.style.display = (category === "Others") ? "block" : "none";
}

/* DRAG & DROP */
const dragArea = document.getElementById("dragArea");
const fileInput = document.getElementById("fileInput");

dragArea.addEventListener("click", function(e){
    if(e.target === fileInput) return;
    fileInput.click();
});

fileInput.addEventListener("change", function(){
    if(this.files[0]){
        dragArea.querySelector("p").textContent = this.files[0].name;
    }
});

/* DYNAMIC VERIFICATION QUESTIONS */
function getQuestionCount(){
    return document.querySelectorAll("#questionsContainer .question-row").length;
}

function updateQuestionNumbers(){
    const rows = document.querySelectorAll("#questionsContainer .question-row");
    rows.forEach(function(row, index){
        row.querySelector("p").textContent = "Question " + (index + 1);
    });
}

function addQuestion(){
    const container = document.getElementById("questionsContainer");
    const count = getQuestionCount() + 1;

    const row = document.createElement("div");
    row.className = "question-row";
    row.innerHTML =
        '<div class="question-header">' +
            '<p>Question ' + count + '</p>' +
            '<button type="button" class="remove-question" onclick="removeQuestion(this)">&#10005;</button>' +
        '</div>' +
        '<input type="text" name="questions[]" placeholder="Enter verification question" required>';

    container.appendChild(row);
}

function removeQuestion(btn){
    const row = btn.closest(".question-row");
    row.remove();
    updateQuestionNumbers();
}