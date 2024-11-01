function getWorkSessions() {
    fetch('https://taim.ing/php/getTimes.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if (data.status === "success") {
            console.log("Work Sessions:", data.work_sessions);
            const workSessions = data.work_sessions;
            const workSessionsTable = document.querySelector('#work_sessions');
            workSessions.forEach(session => {
                const row = workSessionsTable.insertRow();
                
                // Format start date and time
                row.insertCell().textContent = new Date(session.start_date).toLocaleDateString('de-CH');
                row.insertCell().textContent = session.firstname + ' ' + session.lastname;
                row.insertCell().textContent = new Date(session.start_time).toLocaleTimeString('de-CH');
                
                // Check if end_time is null to determine active session
                if (session.end_time === null) {
                    row.insertCell().textContent = "Aktive Sitzung";
                    row.insertCell().textContent = "-";
                } else {
                    row.insertCell().textContent = new Date(session.end_time).toLocaleTimeString('de-CH');

                    // Calculate duration
                    const startTime = new Date(session.start_time);
                    const endTime = new Date(session.end_time);
                    const duration = new Date(endTime - startTime);
                    const hours = String(duration.getUTCHours()).padStart(2, '0');
                    const minutes = String(duration.getUTCMinutes()).padStart(2, '0');
                    row.insertCell().textContent = `${hours}:${minutes}`;
                }

                // Description cell
                const descriptionCell = row.insertCell();
                if (session.description) {
                    const p = document.createElement('p');
                    p.textContent = session.description;
                    descriptionCell.appendChild(p);
                } else {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.placeholder = 'Beschreibung hinzufügen';
                    const button = document.createElement('button');
                    button.textContent = 'Add';
                    button.onclick = () => addDescription(session.session_id, input.value);
                    descriptionCell.appendChild(input);
                    descriptionCell.appendChild(button);
                }
            });
        } else {
            console.error("Error:", data.message);
        }

        // Hide loading screen after a 2-second timeout
        setTimeout(hideLoadingScreen, 2000);
    })
    .catch(error => console.error('Error:', error));
}

function addDescription(sessionId, description_val) {
    console.log("Adding description for session:", sessionId, description_val);

    fetch('https://taim.ing/php/getTimes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            session_id: sessionId,
            description: description_val
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if (data.status === "success") {
            console.log("Description added successfully");
            window.location.reload();
        } else {
            console.error("Error:", data.message);
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

getWorkSessions();
