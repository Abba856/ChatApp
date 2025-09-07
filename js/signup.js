// Function to initialize the signup form
function initSignupForm() {
    console.log("Initializing signup form");
    
    // Get form elements
    const form = document.querySelector(".signup form");
    const continueBtn = document.querySelector(".signup form .button input[type='submit']");
    const errorText = document.querySelector(".signup form .error-text");
    
    // Check if elements exist
    if (!form) {
        console.error("Form not found");
        return;
    }
    
    if (!continueBtn) {
        console.error("Continue button not found");
        return;
    }
    
    if (!errorText) {
        console.error("Error text element not found");
        return;
    }
    
    console.log("All elements found");
    
    // Handle form submission
    function handleFormSubmit(e) {
        // Prevent default form submission
        e.preventDefault();
        console.log("Form submission prevented");
        
        // Validate form
        const fname = form.querySelector("input[name='fname']").value;
        const lname = form.querySelector("input[name='lname']").value;
        const email = form.querySelector("input[name='email']").value;
        const password = form.querySelector("input[name='password']").value;
        
        if (!fname || !lname || !email || !password) {
            errorText.style.display = "block";
            errorText.textContent = "All fields are required!";
            return;
        }
        
        // Show loading state
        const originalValue = continueBtn.value;
        continueBtn.value = "Processing...";
        continueBtn.disabled = true;
        errorText.style.display = "none";
        
        // Create XMLHttpRequest
        const xhr = new XMLHttpRequest();
        
        // Set up the request
        xhr.open('POST', 'php/signup.php', true);
        
        // Handle the response
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    const response = xhr.responseText;
                    console.log("Server response:", response);
                    
                    if (response.trim() === "success") {
                        // Redirect to users page
                        window.location.href = "users.php";
                    } else {
                        // Show error message
                        errorText.style.display = "block";
                        errorText.textContent = response;
                        // Reset button
                        continueBtn.value = originalValue;
                        continueBtn.disabled = false;
                    }
                } else {
                    // Handle HTTP errors
                    errorText.style.display = "block";
                    errorText.textContent = "Server error. Please try again. (HTTP " + xhr.status + ")";
                    // Reset button
                    continueBtn.value = originalValue;
                    continueBtn.disabled = false;
                    console.error("HTTP Error:", xhr.status);
                }
            }
        };
        
        // Handle network errors
        xhr.onerror = function() {
            errorText.style.display = "block";
            errorText.textContent = "Network error. Please check your connection.";
            // Reset button
            continueBtn.value = originalValue;
            continueBtn.disabled = false;
            console.error("Network error");
        };
        
        // Send the form data
        const formData = new FormData(form);
        console.log("Sending form data");
        xhr.send(formData);
    }
    
    // Attach event listeners
    form.addEventListener('submit', handleFormSubmit);
    continueBtn.addEventListener('click', handleFormSubmit);
    
    console.log("Event listeners attached");
}

// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSignupForm);
} else {
    // DOM is already loaded
    initSignupForm();
}

// Also try to initialize after a short delay to ensure elements are ready
setTimeout(initSignupForm, 100);