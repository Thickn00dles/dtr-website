const timeInOutBtn = document.getElementById('timeInOutBtn');
const infoTable = document.getElementById('infoTable');

let username; // Stores the user's name
let timeInAM = null; // Stores time in for AM
let timeOutAM = null; // Stores time out for AM
let timeInPM = null; // Stores time in for PM
let timeOutPM = null; // Stores time out for PM

function updateTimeInfo() {
  const currentDate = new Date();
  const hours = currentDate.getHours();
  const minutes = currentDate.getMinutes().toString().padStart(2, '0'); // Pad minutes with leading zero if needed
  const ampm = hours >= 12 ? 'PM' : 'AM';
  const time = `${hours % 12}:${minutes} ${ampm}`; // Formatted time with AM/PM

  // Create a new table row (only if it doesn't exist)
  let newRow = infoTable.getElementsByTagName('tbody')[0].querySelector(`tr.current-user`);
  if (!newRow) {
    newRow = document.createElement('tr');
    newRow.classList.add('current-user'); // Add class for targeted username update

    const usernameCell = document.createElement('td');
    const timeInAMCell = document.createElement('td');
    const timeOutAMCell = document.createElement('td');
    const timeInPMCell = document.createElement('td');
    const timeOutPMCell = document.createElement('td');
    const dateCell = document.createElement('td');

    usernameCell.textContent = username || 'Enter Username'; // Display "Enter Username" initially
    dateCell.textContent = currentDate.toLocaleDateString();

    newRow.appendChild(usernameCell);
    newRow.appendChild(timeInAMCell);
    newRow.appendChild(timeOutAMCell);
    newRow.appendChild(timeInPMCell);
    newRow.appendChild(timeOutPMCell);
    newRow.appendChild(dateCell);

    infoTable.getElementsByTagName('tbody')[0].appendChild(newRow);
  }

  const timeInAMCell = newRow.children[1];
  const timeOutAMCell = newRow.children[2];
  const timeInPMCell = newRow.children[3];
  const timeOutPMCell = newRow.children[4];
  // Update time based on AM/PM
  if (ampm === 'AM') {
    if (!timeInAM) {
      timeInAM = time;
      timeInAMCell.textContent = `${timeInAM}`;
      timeOutDisabledUntil = currentDate.getTime() + (10 * 60 * 1000); // Disable time in for 10 minutes
      timeInOutBtn.disabled = true; // Disable button after successful time in
    } else if (!timeOutAM) {
      timeOutAM = time;
      timeOutAMCell.textContent = `${timeOutAM}`;
    } else {  
      alert('You already clocked in and out for the morning.');
    }
  } else if (ampm === 'PM') {
    if (!timeInPM) {
      timeInPM = time;
      timeInPMCell.textContent = `${timeInPM}`;
      timeOutDisabledUntil = currentDate.getTime() + (10 * 60 * 1000); // Disable time in for 10 minutes
      timeInOutBtn.disabled = true; // Disable button after successful time in
    } else if (!timeOutPM) {
      timeOutPM = time;
      timeOutPMCell.textContent = `${timeOutPM}`;
    } else {
      alert('You already clocked in and out for the afternoon.');
    }
  }
}

// Function to prompt for username (optional, can be integrated into another part)
function promptUsername() {
  if (!username) {
    username = prompt('Please enter your username:');
  }
}

// Add event listener to the button
timeInOutBtn.addEventListener('click', () => {
  promptUsername(); // Call promptUsername if needed
  updateTimeInfo();
});
// Enable time in button again after 10 minutes
function enableTimeInButton() {
  const now = new Date().getTime();
  if (timeOutDisabledUntil && now >= timeOutDisabledUntil) {
    timeInOutBtn.disabled = false;
    timeOutDisabledUntil = null; // Reset disable time
  }
}

// Call enableTimeInButton every second to check for timeout
setInterval(enableTimeInButton, 1000);
