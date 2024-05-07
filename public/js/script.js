const formProgramme = document.querySelectorAll('.editProgramme');
const formEditProgramme = document.querySelectorAll('.formEditProgramme');
const closeBtn = document.querySelectorAll(".close-btn");

for (let i = 0; i < formProgramme.length; i++) {
    formProgramme[i].addEventListener("click", () => {
        formEditProgramme[i].classList.toggle('active');
    });

    closeBtn[i].addEventListener("click", () => {
        formEditProgramme[i].classList.remove('active'); // Utilisez parentElement pour accéder au conteneur .formEditProgramme
    });
}