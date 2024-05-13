

//Burger Menu ---------------------------------------------------
let sidenav = document.getElementById("mySidenav");
let openBtn = document.getElementById("openBtn");
let closeBtnMenu = document.getElementById("closeBtn");
//Edition duree programme ------------------------------------------
// const formProgramme = document.querySelectorAll('.editProgramme');
// const formEditProgramme = document.querySelectorAll('.formEditProgramme');
// const closeBtnForm = document.querySelectorAll(".close-btn");



//Burger Menu ---------------------------------------------------
openBtn.onclick = openNav;
closeBtnMenu.onclick = closeNav;

//set la largeur de la navigation à 350px;
function openNav(){
    sidenav.classList.add("active");
}

//set la largeur de la navigation à 0;
function closeNav(){
    sidenav.classList.remove('active');
}



// Edition duree programme ------------------------------------------
// for (let i = 0; i < formProgramme.length; i++) {
//     formProgramme[i].addEventListener("click", () => {
//         formEditProgramme[i].classList.toggle('active');
//     });
    
//     closeBtnForm[i].addEventListener("click", () => {
//         formEditProgramme[i].classList.remove('active'); // Utilisez parentElement pour accéder au conteneur .formEditProgramme
//     });
// }




// //Requète Ajax pour la soustraction d'un module--------------------------------------
// // Attend que le DOM soit entièrement chargé
// document.addEventListener('DOMContentLoaded', function() {
//     var deleteButtons = document.querySelectorAll('.delete-module-btn');
//     // Ajoute un écouteur d'événements à chaque bouton
//     deleteButtons.forEach(function(button) {
//         button.addEventListener('click', function() {
//             // Récupère les identifiants de session et de programme à partir des attributs de données HTML
//             var sessionId = button.getAttribute('data-session-id');
//             var programmeId = button.getAttribute('data-programme-id');
//             supprimerModuleDeSession(sessionId, programmeId);
//         });
//     });
// });

// function supprimerModuleDeSession(sessionId, programmeId) {
//     // Créer un nouvel objet XMLHttpRequest
//     let xhr = new XMLHttpRequest();
    
//     // Définir la fonction de rappel pour gérer la réponse de la requête AJAX
//     xhr.onreadystatechange = function() {
//         // Vérifie si la requête est terminée
//         if (xhr.readyState === XMLHttpRequest.DONE) {
//             if (xhr.status === 200) {
//                 window.location.href = '/interface/' + sessionId; // Succès : Redirection vers la page interface
//             } else {
//                 console.error('Erreur lors de la suppression du module de la session');
//             }
//         }
//     };
    
//     xhr.open('POST', '/interface/' + sessionId + '/' + programmeId + '/removeProgrammeToSession', true); // Ouvrir une requête AJAX POST vers l'URL
//     xhr.send(); // Envoyer la requête
// }