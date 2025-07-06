// MFA JS
let mfaTimer;
function startMFATimer(duration, displayId) {
    let timer = duration, minutes, seconds;
    mfaTimer = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        document.getElementById(displayId).textContent = minutes + ":" + seconds;
        if (--timer < 0) {
            clearInterval(mfaTimer);
            document.getElementById(displayId).textContent = "Expired";
        }
    }, 1000);
}
// Usage: <span id="mfa-timer"></span> and call startMFATimer(120, 'mfa-timer') for 2 min timer. 