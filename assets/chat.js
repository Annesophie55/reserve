// chat.js
const token = localStorage.getItem('jwt');

const conn = new WebSocket('ws://localhost:8080', [], {
    headers: {
        Authorization: "Bearer " + token
    }
});


conn.onopen = function(e) {
    console.log("Connection established!");
};

conn.onmessage = function(e) {
  console.log(e.data);
  
  // Analyse du message reçu
  const receivedMessage = JSON.parse(e.data);

  console.log(receivedMessage);
  
  // Supposons que vous ayez une fonction qui ajoute une bulle lorsqu'un nouveau message est reçu
  ajouterBulleDeNotification(receivedMessage);
};

function ajouterBulleDeNotification(message) {
  // Ajout d'une bulle de notification à l'interface utilisateur
  // Vous pouvez personnaliser cette fonction selon vos besoins
  const bulle = document.createElement('div');
  bulle.innerText = 'Nouveau message reçu !';
  bulle.onclick = function() {
    // Logique pour ouvrir la conversation correspondante
  };

  // Supposons que #chatBubble est le conteneur où vous souhaitez ajouter la bulle
  document.querySelector('#chatBubble').appendChild(bulle);
}


document.getElementById("message-form").addEventListener("submit", function(e) {
  e.preventDefault(); // Empêcher la soumission du formulaire
  const messageContent = document.getElementById("message-content").value;
  const recipientId = document.getElementById("query").value; // Supposons que cela contient l'ID du destinataire
  const senderId = localStorage.getItem('userId');

  
  const messageData = {
    content: messageContent,
    senderId: senderId,
    recipientId: recipientId
  };
  console.log("Envoi du message :", messageData);
  if (conn.readyState === WebSocket.OPEN) {
    conn.send(JSON.stringify(messageData));
  }
  
  console.log("Message envoyé");
});

