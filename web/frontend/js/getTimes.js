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
                    const p = document.createElement('span');
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

function getWorkSessionsByDate(date) {
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

            const workSessionsTable = document.querySelector('#work_sessions');

            console.log("Work Sessions:", data.work_sessions);
            const workSessions = data.work_sessions.filter(session => {
                const sessionDate = new Date(session.start_date).toISOString().split('T')[0];
                return sessionDate === date;
            });

            const uniqueFirstNames = [...new Set(workSessions.map(session => session.firstname))];
            console.log("Unique First Names:", uniqueFirstNames);

            const userList = document.querySelector('#user-list');
            userList.innerHTML = "";

            const colors = {};

            uniqueFirstNames.forEach(name => {
                const color = `rgb(${randomRGBValue()}, ${randomRGBValue()}, ${randomRGBValue()})`;
                colors[name] = color;
                userList.innerHTML += `<li><span class="user-icon" style="background-color: ${color};"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30"><path d="M8 5v14l11-7z" fill="#f0f2f5"/></svg></span>${name}</li>`;
            });

            userList.innerHTML += `<li onclick="window.location.href='./cards.html'"><span class="user-icon new-user"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30"><path d="M12 5v14M5 12h14" fill="none" stroke="#f0f2f5" stroke-width="3"/></svg></span>Kartenverwaltung</li>`;

            workSessionsTable.innerHTML = ''; // Clear existing rows
            workSessions.forEach(session => {
                const row = workSessionsTable.insertRow();
                
                row.style.backgroundColor = colors[session.firstname];

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
                    const p = document.createElement('span');
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

            console.log(workSessions);

            if(workSessions.length === 0) {
                console.log("No work sessions found for the selected date");
                const row = workSessionsTable.insertRow();
                const cell = row.insertCell().textContent = "Keine Daten für diesen Tag gefunden";
                cell.colSpan = 6;
                setTimeout(hideLoadingScreen, 2000);
                return;
            }

        } else {
            console.error("Error:", data.message);
        }

        // Hide loading screen after a 2-second timeout
        setTimeout(hideLoadingScreen, 2000);
    })
    .catch(error => console.error('Error:', error));
}


const randomRGBValue = () => {
    return Math.floor(Math.random() * 100) + 100;
};

const today = new Date().toISOString().split('T')[0];
console.log("Today:", today);

getWorkSessionsByDate(today);
