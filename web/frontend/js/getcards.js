function fetchCards() {
    fetch('https://taim.ing/php/getCards.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            console.log("Assigned Cards:", data.cards.assigned);
            console.log("Unassigned Cards:", data.cards.unassigned);

            const assignedCards = data.cards.assigned;
            const unassignedCards = data.cards.unassigned;

            const userCardsTable = document.querySelector('#user_cards');
            const unassignedCardsTable = document.querySelector('#new_cards');

            // Clear existing table rows

            //show cards based on<th>Card ID</th><th>Vorname</th><th>Nachname</th>
            assignedCards.forEach(card => {
                const row = userCardsTable.insertRow();
                row.insertCell().textContent = card.card_id;
                row.insertCell().textContent = card.firstname;
                row.insertCell().textContent = card.lastname;
            });

            unassignedCards.forEach(card => {
                const row = unassignedCardsTable.insertRow();
                row.insertCell().textContent = card.card_id;
                row.insertCell().innerHTML = `<button onclick="assignCard('${card.card_id}')">Assign</button>`;
            });

        } else {
            console.error("Error:", data.message);
        }

        //timeout 2 seconds
        setTimeout(hideLoadingScreen, 2000);
    })
    .catch(error => console.error('Error:', error));
}

function addUserDetails(card_id_val, firstname_val, lastname_val) {

    console.log("Adding user details for card:", card_id_val , firstname_val, lastname_val);

    fetch('https://taim.ing/php/registerChip.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'add_user_details',
            card_id: card_id_val,
            firstname: firstname_val,
            lastname: lastname_val
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if (data.status === "success") {
            console.log("User details added successfully");
            window.location.reload();
        } else {
            console.error("Error:", data.message);
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// Example usage
//addUserDetails('12345ABC', 'John', 'Doe');


function assignCard(card_id) {
    //open modal and ask for user details
    const modal = document.querySelector('#newCardModal');
    modal.style.display = 'block';
    //set card details into input
    document.querySelector('#card_id').value = card_id;
}

function closeNewCardModal() {
    const modal = document.querySelector('#newCardModal');
    modal.style.display = 'none';
}

fetchCards();
