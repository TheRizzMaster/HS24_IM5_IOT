// Check for session on page load
window.addEventListener('load', () => {
    fetch('https://taim.ing/php/protected-endpoint.php', {
        method: 'GET',
        credentials: 'include' // Include cookies with the request
    })
    .then(response => {
        if (!response.ok) {
            // If the session is invalid (e.g., 401 Unauthorized), redirect to login
            window.location.href = '/';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Optionally, redirect on network errors as well
        window.location.href = '/';
    });
});