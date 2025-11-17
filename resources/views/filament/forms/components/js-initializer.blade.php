<div x-data x-init="
const fieldId = '{{ $fieldId }}';
let attempts = 0;
const maxAttempts = 5;

// Use a function to repeatedly try initialization
const tryInit = () => {
    const inputElement = document.getElementById(fieldId);
    
    // If the element is found AND the global function exists
    if (inputElement && window.initCustomDatePicker) {
        window.initCustomDatePicker(fieldId);
    } else if (attempts < maxAttempts) {
        // If not found, try again shortly
        attempts++;
        setTimeout(tryInit, 50 * attempts); // Exponential backoff for retries
    } else {
        // Log warning if the element never appeared after max attempts
        console.warn(`[Custom DatePicker Fix] Could not initialize field: ${fieldId} after ${maxAttempts} attempts.`);
    }
};

// Start the guaranteed initialization process
tryInit();


">

</div>