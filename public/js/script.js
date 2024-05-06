const formProgramme = document.querySelectorAll('.editProgramme')
const formEditProgramme = document.querySelectorAll('.formEditProgramme')

for(let i in formProgramme){
    formProgramme[i].addEventListener("click", ()=>{
        formEditProgramme[i].classList.toggle('active')
    })
}
