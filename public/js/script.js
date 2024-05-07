const formProgramme = document.querySelectorAll('.editProgramme')
const formEditProgramme = document.querySelectorAll('.formEditProgramme')
const closeBtn = document.getElementById("close-btn")

for(let i = 0; i < formProgramme.length; i++){
    formProgramme[i].addEventListener("click", ()=>{
        formEditProgramme[i].classList.toggle('active');
    });
}

closeBtn.addEventListener("click", () => {
    formEditProgramme.classList.remove('active');
    });
