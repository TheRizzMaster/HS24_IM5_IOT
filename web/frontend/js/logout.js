// JavaScript function to handle logout
function logout() {
    fetch('https://taim.ing/php/logout.php', {
        method: 'POST',
        credentials: 'include' // Include cookies in the request
    })
    .then(response => response.json())
    .then(data => {
        console.log(data.message); // e.g., 'Logout successful'
        // Redirect to login page
        window.location.href = 'index.html';
    })
    .catch(error => console.error('Error:', error));
}