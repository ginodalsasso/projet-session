//Burger Menu ------------------------------------------
var sidenav = document.getElementById("mySidenav");
var openBtn = document.getElementById("openBtn");
var closeBtnMenu = document.getElementById("closeBtn");
//Edition duree programme ------------------------------------------
var formProgramme = document.querySelectorAll('.editProgramme');
var formEditProgramme = document.querySelectorAll('.formEditProgramme');
var closeBtnForm = document.querySelectorAll(".close-btn");



// Burger Menu ---------------------------------------------------
openBtn.onclick = toggleNav;
closeBtnMenu.onclick = toggleNav;

function toggleNav(){
    sidenav.classList.toggle("active");
}


// Edition duree programme ------------------------------------------
for (let i = 0; i < formProgramme.length; i++) {
    formProgramme[i].addEventListener("click", () => {
        formEditProgramme[i].classList.toggle('active');
    });
    
    closeBtnForm[i].addEventListener("click", () => {
        formEditProgramme[i].classList.remove('active'); // Utilisez parentElement pour accéder au conteneur .formEditProgramme
    });
}




// Requète Ajax pour l'édition d'une session (en javascript) --------------------------------------
// Attend que le DOM soit entièrement chargé
document.addEventListener('DOMContentLoaded', function() {
    var editSessionBtns = document.querySelectorAll('.editSessionBtn');
    
    editSessionBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var sessionId = btn.getAttribute('data-session-id');
            editSessionRequest(sessionId);
        });
    });
});

function editSessionRequest(sessionId) {
    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // La requête a réussi
                // Redirection vers la route 'app_session'
                window.location.href = '/session';
            } else {
                // La requête a échoué
                console.error("Erreur de requête: " + xhr.status);
                // Afficher un message d'erreur
                alert("Une erreur est survenue lors de l'ajout/modification de la session.");
            }
        }
    };
    
    xhr.open("POST", '/session/' + sessionId + '/editSession', true);
    xhr.send();  // Envoyer la requête
}


//Requète Ajax pour la soustraction d'un module (en javascript) --------------------------------------
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

// Requète Ajax pour la soustraction d'un module (en jQuery) --------------------------------------
$(document).ready(function() {
    // événements click à tous les éléments avec la classe '.delete-module-btn'
    $('.delete-module-btn').on('click', function() {
        // Récupère les identifiants de session et de programme à partir des attributs
        var sessionId = $(this).data('session-id');
        var programmeId = $(this).data('programme-id');
        supprimerModuleDeSession(sessionId, programmeId);
    });
});

function supprimerModuleDeSession(sessionId, programmeId) {
    // Effectue une requête AJAX POST vers l'URL spécifiée
    $.ajax({
        type: 'POST',
        url: '/interface/' + sessionId + '/' + programmeId + '/removeProgrammeToSession',
        success: function(response) {
            window.location.href = '/interface/' + sessionId; // Succès : Redirection vers la page interface
        },
        error: function(xhr, status, error) { //// Fonction d'erreur appelée si la requête échoue
            console.log('Erreur lors de la suppression du module de la session');
        }
    });
}