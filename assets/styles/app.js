const currentDate = document.querySelector(".current-date");

let date = new Date();
currYear = date.getFullYear();
currMonth = date.getMonth();

const months = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ];

const renderCalendar = () => {
  let lastDateofMonth = new Date(currYear, currMonth +1, 0).getDate();
  let liTag ="";
  console.log(lastDateofMonth);

  for(let i=1; i<= lastDateofMonth; i++){
    liTag = '<li>1</li>';
  }

  currentDate.innerText = `${months[currMonth]} ${currYear}`;
}
renderCalendar();