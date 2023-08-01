const currentDate = document.querySelector(".current-date");
const daysTag = document.querySelector(".days");
const prevNextIcon = document.querySelectorAll(".icons span");

let date = new Date();
let currYear = date.getFullYear();
let currMonth = date.getMonth();
const months = [
  "Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
  "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"
];

const updateSelectedHour = (hour) => {
  const hourSelect = document.querySelector("select[name=hour]");
  hourSelect.value = hour;
};

const renderCalendar = () => {
  const firstDayOfMonth = new Date(currYear, currMonth, 1).getDay();
  const lastDateOfMonth = new Date(currYear, currMonth + 1, 0).getDate();
  const lastDayOfMonth = new Date(currYear, currMonth, lastDateOfMonth).getDay();
  const liTags = [];

  for (let i = firstDayOfMonth; i > 0; i--) {
    liTags.push(`<li class="inactive">${lastDateOfMonth - i + 1}</li>`);
  }

  for (let i = 1; i <= lastDateOfMonth; i++) {
    const isToday = i === date.getDate() && currMonth === date.getMonth() && currYear === date.getFullYear();
    const isValid = new Date(currYear, currMonth, i) >= date; // Vérifie si le jour est après la date d'aujourd'hui
    let classes = isToday ? "active" : ""; // Ajoute la classe "active" si c'est le jour d'aujourd'hui
    if (isValid) {
      classes += " available-day"; // Ajoute la classe "available-day" pour les jours valides
    } else {
      classes += " inactive"; // Ajoute la classe "inactive" pour les jours non valides
    }
    liTags.push(`<li class="${classes}">${i}</li>`);
  }

  for (let i = lastDayOfMonth; i < 6; i++) {
    liTags.push(`<li class="inactive">${i - lastDayOfMonth + 1}</li>`);
  }

  currentDate.innerText = `${months[currMonth]} ${currYear}`;
  daysTag.innerHTML = liTags.join("");
};

const updateCalendar = () => {
  date = new Date(currYear, currMonth, 1);
  renderCalendar();
};

renderCalendar();

prevNextIcon.forEach(icon => {
  icon.addEventListener("click", () => {
    currMonth = icon.id === "prev" ? currMonth - 1 : currMonth + 1;

    if (currMonth < 0 || currMonth > 11) {
      if (currMonth < 0) {
        currYear--;
        currMonth = 11;
      } else {
        currYear++;
        currMonth = 0;
      }
    }
    updateCalendar();
  });
});

// Fonction pour remplir les heures disponibles
const fillAvailableHours = (day) => {
  // Les horaires disponibles de 9h à 12h et de 14h à 17h par tranche de 1h30
  const availableHours = [
    "09:00", "10:30", // Matin
    "14:00", "15:30", // Après-midi
  ];

  const hourInput = document.querySelector("select[name=hour]");
  hourInput.innerHTML = ""; // Vide le champ

  for (const hour of availableHours) {
    const option = document.createElement("option");
    option.value = hour;
    option.innerText = hour;
    hourInput.appendChild(option);
  }
};

// Écoute le clic sur les jours du calendrier pour remplir les heures disponibles
const days = document.querySelectorAll(".days li");
days.forEach(day => {
  day.addEventListener("click", () => {
    console.log("Clic sur le jour !");
    // Vérifie si le jour est disponible en vérifiant sa classe
    if (day.classList.contains("available-day")) {
      const selectedDate = day.innerText;
      updateSelectedHour(""); // Réinitialise l'heure sélectionnée lorsque vous cliquez sur un nouveau jour
      fillAvailableHours(selectedDate);
      // Supprime la classe "selected" de tous les jours du calendrier
      days.forEach(day => day.classList.remove("selected"));
      // Ajoute la classe "selected" au jour sélectionné
      day.classList.add("selected");
    }
  });
});
