import { Calendar } from 'fullcalendar';
import frLocale from '@fullcalendar/core/locales/fr';
import timeGridPlugin from '@fullcalendar/timegrid';


document.addEventListener('DOMContentLoaded', function() {
    const calendarElForm = document.getElementById('formCalendar');

    let lastClickedDate = null;

    // Fonction pour afficher les horaires disponibles
    function displayAvailableTimes(selectedDate) {
        console.log("Date sélectionnée:", selectedDate);
        fetch(`/available-slots?date=${selectedDate}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log("Créneaux disponibles: ", data);
            updateSlotsDropdown(data);
        })
        .catch(error => {
            console.error("Erreur lors de la récupération des créneaux disponibles:", error);
        });
    }

    // Fonction pour mettre à jour le menu déroulant des créneaux
    function updateSlotsDropdown(data) {
        const selectElement = document.querySelector('#availableSlots');
        selectElement.innerHTML = ''; // Effacer le menu déroulant
    
        data.forEach(slot => {
            const optionElement = document.createElement('option');
            optionElement.value = `${slot.start}-${slot.end}`;
    
            // Calculer l'heure de fin basée sur une durée de 1h30 pour le rendez-vous
            let startDate = new Date(slot.start);
            console.log(startDate);
            let endDate = new Date(startDate.getTime() + 1.5 * 60 * 60 * 1000);
            console.log(endDate);

            console.log("Début de la mise à jour des créneaux");
            console.log("Slot:", slot);

            let formattedStartDate = startDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
            let formattedEndDate = endDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    
            optionElement.textContent = `De ${formattedStartDate} à ${formattedEndDate}`;

            
            selectElement.appendChild(optionElement);

            console.log(selectElement);

            selectElement.addEventListener('change', (event) => {
                console.log("Créneau sélectionné:", event.target.value);
                document.getElementById('selectedSlot').value = event.target.value;
            });
            
            


        });
    }
    if (calendarElForm) {
    // Créer une instance de FullCalendar
    const calendar = new Calendar(calendarElForm, {
        initialView: 'dayGridMonth',

        headerToolbar: {
            left: 'title',
            right: 'prev,next'
        },
        locale: frLocale,
        fixedWeekCount: false,
        dateClick: function(info) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const selectedDateObj = new Date(info.dateStr);
                    
            if (selectedDateObj < today) {
                console.log("Date antérieure, non sélectionnable");
                return;
            }
            
            const dayOfWeek = new Date(info.dateStr).getDay();
            if (dayOfWeek === 3 || dayOfWeek === 6 || dayOfWeek === 0) {
                console.log("Jour non travaillé");
                return; // Sortie précoce si c'est un jour non travaillé
            }
            
            // Réinitialisez la couleur de l'élément day-top du jour précédemment cliqué
            if (lastClickedDate) {
                const lastTopEl = lastClickedDate.querySelector('.fc-daygrid-day-top');
                if (lastTopEl) {
                    lastTopEl.style.backgroundColor = ''; // Reset background color of the top of the previously clicked day
                }
            }
            
            // Mettre en surbrillance le jour actuellement cliqué
            const currentTopEl = info.dayEl.querySelector('.fc-daygrid-day-top');
            if (currentTopEl) {
                currentTopEl.style.backgroundColor = 'rgb(109, 3, 102)'; // Set the background color of the top of the currently clicked day
            }
            
            // Mémorisez le jour actuellement cliqué pour une utilisation ultérieure
            lastClickedDate = info.dayEl;
            
            document.getElementById('selectedDate').value = info.dateStr;
            const selectedDate = info.dateStr;
            displayAvailableTimes(selectedDate);
        },
        
        
        selectAllow: function(selectInfo) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
        
            var day = new Date(selectInfo.startStr).getDay();
        
            // Vérifiez également si la date sélectionnée est antérieure à aujourd'hui
            if (new Date(selectInfo.startStr) < today) {
                return false;
            }
        
            return day !== 3 && day !== 6 && day !== 0;
        },
        events: [
            {
                daysOfWeek: [3, 6, 0],
                rendering: 'background',
                backgroundColor: 'white',
            }
        ],

        
        dayCellDidMount: function (info) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (info.date < today || [0, 3, 6].includes(info.date.getDay())) {
                let topEl = info.el.querySelector('.fc-daygrid-day-top');
                if (topEl) {
                    topEl.style.backgroundColor = '#FCE8F8';
                }
            }
        },
        
    });

    calendar.render();
}
});

// AGENDA ADMIN
document.addEventListener('DOMContentLoaded', function() {

    const adminCalendarEl = document.getElementById('adminCalendar');
    if (adminCalendarEl) {
    
    let eventsData;
    try {
        eventsData = JSON.parse(adminCalendar.getAttribute('data-events'));
    } catch (erreur) {
        console.error("Erreur lors de l'analyse des données des événements :", erreur);
    }
     
    
    if (eventsData) {

    const adminCalendar = new Calendar(adminCalendarEl, {
        locale: frLocale,
        plugins: [timeGridPlugin],
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev',
            center: 'title',
            right: 'addEventButton, next'
        },
        allDaySlot: false,
        slotMinTime: "09:00:00",
        slotMaxTime: "22:00:00",
        slotDuration: '00:30:00',
        titleFormat:
            {year: 'numeric', month: 'long', day: 'numeric'
        },
        events: eventsData,
        eventClick: function(info) {
            const eventId = info.event.id;
            if (eventId) {
                window.location.href = `/rdv/${eventId}/details`;
            }
            else{
                console.log('Il y a une erreur lors de la récupération du rdv')
            }
        },
        customButtons: {
            addEventButton: {
                text: 'Ajouter événement',
                click: function() {
                    window.location.href = `/appointements`;
                }
            }
        }
    });
    adminCalendar.render();
    } else {
    console.error("Aucune donnée d'événement disponible pour initialiser le calendrier");
}}
});

document.addEventListener('DOMContentLoaded', function() {
    const serviceItem = document.getElementById('serviceItem');

    if (serviceItem) {
        serviceItem.addEventListener('mouseenter', function() {
            fetch(servicesJsonUrl)
                .then(response => response.json())
                .then(data => {
                    var services = data.services;
                    var modalContent = document.querySelector('.modal ul');
                    
                    modalContent.innerHTML = '';
                    
                    services.forEach(function(service) {
                        var serviceLink = document.createElement('a');
                        serviceLink.href = service.url;
                        serviceLink.textContent = service.name;

                        var listItem = document.createElement('li');
                        listItem.appendChild(serviceLink);

                        modalContent.appendChild(listItem);
                    });
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        });
    }
});

// Effet pour l'affichage des commentaires sur la page home
document.addEventListener('DOMContentLoaded', function() {
    const aosClasses = ['fade-up', 'fade-down', 'fade-left', 'fade-right', 'fade-down-right', 'fade-down-left', 'flip-left', 'flip-right', 'flip-up', 'flip-down', 'zoom-in', 'zoom-in-up', 'zoom-in-down', 'zoom-in-left', 'zoom-in-right', 'zoom-out', 'zoom-out-up', 'zoom-out-down', 'zoom-out-right', 'zoom-out-left']; // Liste des classes AOS possibles
  
    const aosComments = document.querySelectorAll('.aos-comment');
  
    aosComments.forEach(function(comment) {
      const randomAOSClass = aosClasses[Math.floor(Math.random() * aosClasses.length)];
      comment.setAttribute('data-aos', randomAOSClass);
    });
  
    AOS.init(); // Initialisez AOS après avoir attribué les classes AOS aléatoires
  });
  

