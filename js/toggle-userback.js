document.addEventListener('DOMContentLoaded', function () {
    console.log("DOM fully loaded and parsed");

    // Function to log the initial state of the overlay and toggle button.
    function logInitialState(overlay, toggleButton) {
        const initialOverlayDisplay = window.getComputedStyle(overlay).display;
        const isOverlayVisible = initialOverlayDisplay !== 'none';
        const isToggleEnabled = toggleButton.classList.contains('show');

        // Log the initial states.
        console.log("Initial overlay visibility:", isOverlayVisible ? "Visible" : "Hidden");
        console.log("Initial toggle button state:", isToggleEnabled ? "Enabled" : "Disabled");
    }

    // Function to keep the toggle button and overlay states synchronized.
    function syncToggleAndOverlayState(overlay, toggleButton) {
        // Determine the current state of the overlay based on its display property
        const isOverlayVisible = window.getComputedStyle(overlay).display !== 'none';

        // Synchronize the toggle button state with the overlay visibility
        if (isOverlayVisible) {
            toggleButton.classList.add('show'); // Ensure the toggle is in the "show" state
        } else {
            toggleButton.classList.remove('show'); // Ensure the toggle is not in the "show" state
        }

        // Log the synchronized states
        console.log("Synchronized state -> Overlay:", isOverlayVisible ? "Visible" : "Hidden");
        console.log("Synchronized state -> Toggle Button:", toggleButton.classList.contains('show') ? "Enabled" : "Disabled");
    }

    // Function to toggle the visibility of the Userback overlay.
    function matchboxToggleUserback() {
        console.log("matchboxToggleUserback() called");

        const overlay = document.getElementById('userback_button_container');
        const toggleButton = document.getElementById('matchbox-pill-toggle');

        if (overlay && toggleButton) {
            // Determine if the overlay is currently visible using computed styles
            const isCurrentlyVisible = window.getComputedStyle(overlay).display !== 'none';

            // Log the current state before toggling
            console.log("Current overlay state (before toggle):", isCurrentlyVisible ? "Visible" : "Hidden");
            console.log("Current toggle button state (before toggle):", toggleButton.classList.contains('show') ? "Enabled" : "Disabled");

            // Toggle the overlay visibility
            overlay.style.display = isCurrentlyVisible ? 'none' : 'block';

            // Synchronize the toggle state with the new overlay state
            syncToggleAndOverlayState(overlay, toggleButton);

            // Log the new state after toggling
            console.log("New overlay state (after toggle):", overlay.style.display === 'block' ? "Visible" : "Hidden");
            console.log("New toggle button state (after toggle):", toggleButton.classList.contains('show') ? "Enabled" : "Disabled");
        } else {
            console.warn("Userback overlay or toggle button not found.");
        }
    }

    // Function to set the initial state of the toggle and Userback overlay.
    function initializeToggleState() {
        const overlay = document.getElementById('userback_button_container');
        const toggleButton = document.getElementById('matchbox-pill-toggle');

        if (overlay && toggleButton) {
            // Get the initial display style of the overlay using computed styles
            const initialOverlayDisplay = window.getComputedStyle(overlay).display;

            // Determine if the overlay is initially visible or hidden.
            const isOverlayVisible = initialOverlayDisplay !== 'none';

            // Log the initial state of the overlay and toggle button
            logInitialState(overlay, toggleButton);

            // Set the initial state of the overlay and button based on the computed style
            if (isOverlayVisible) {
                overlay.style.display = 'block'; // Explicitly set to visible if not set
                toggleButton.classList.add('show'); // Set the toggle button to "show" state
                console.log("Initial state set: Overlay is visible, toggle button set to 'Enabled'.");
            } else {
                overlay.style.display = 'none'; // Explicitly set to hidden if not set
                toggleButton.classList.remove('show'); // Set the toggle button to "hide" state
                console.log("Initial state set: Overlay is hidden, toggle button set to 'Disabled'.");
            }
        } else {
            console.warn("Overlay or toggle button not found for initialization.");
        }
    }

    // Add event listener to the toggle button to control the overlay visibility.
    const toggleButton = document.getElementById('matchbox-pill-toggle');
    if (toggleButton) {
        toggleButton.addEventListener('click', matchboxToggleUserback);
        console.log("Pill toggle button event listener added.");
    } else {
        console.warn("Pill toggle button not found.");
    }

    // Set up a timer to initialize the state when the DOM is fully loaded
    const initInterval = setInterval(function () {
        const overlay = document.getElementById('userback_button_container');
        const toggleButton = document.getElementById('matchbox-pill-toggle');

        if (overlay && toggleButton) {
            initializeToggleState();
            clearInterval(initInterval); // Clear the interval once the elements are found and initialized
        } else {
            console.log("Waiting for overlay and toggle button to be available...");
        }
    }, 500); // Check every 500ms
});
