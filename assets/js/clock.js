function startClock(elementId) {
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        document.getElementById(elementId).textContent = timeString;
    }
    updateClock();
    setInterval(updateClock, 1000);
}
// Usage: <span id="clock"></span> and call startClock('clock') on page load. 