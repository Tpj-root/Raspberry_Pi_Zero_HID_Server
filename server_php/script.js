function showMessage(text, type) {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = text;
    messageDiv.className = 'message ' + type;
    messageDiv.style.display = 'block';
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 4000);
}

function sendCommand(command, buttonElement = null) {
    console.log('Sending command:', command);
    
    // Show sending state for button if provided
    if (buttonElement) {
        showButtonState(buttonElement, 'sending', 'Sending...');
    }
    
    // Show main message
    showMessage('🔄 Sending command: ' + command, 'sending');

    fetch('hid_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'command=' + encodeURIComponent(command)
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(data => {
        console.log('Response data:', data);
        
        // Check if the response indicates success (your PHP returns various success messages)
        if (data && !data.includes('Error') && !data.includes('error') && 
            !data.includes('Unknown') && !data.includes('Failed')) {
            
            // Show success state for button if provided
            if (buttonElement) {
                showButtonState(buttonElement, 'success', 'Sent!');
            }
            showMessage('✅ ' + data, 'success');
        } else {
            throw new Error(data || 'Unknown error occurred');
        }
        
        // Reset button state after 2 seconds
        if (buttonElement) {
            setTimeout(() => {
                resetButtonState(buttonElement);
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        // Show error state for button if provided
        if (buttonElement) {
            showButtonState(buttonElement, 'error', 'Failed!');
        }
        showMessage('❌ Error: ' + error.message, 'error');
        
        // Reset button state after 2 seconds
        if (buttonElement) {
            setTimeout(() => {
                resetButtonState(buttonElement);
            }, 2000);
        }
    });
}

// Helper functions for button states
function showButtonState(buttonElement, state, message) {
    // Extract command from onclick attribute
    const command = buttonElement.getAttribute('onclick').match(/'([^']+)'/)[1];
    
    // Remove any existing state classes
    buttonElement.classList.remove('loading', 'success', 'error');
    
    // Add current state
    buttonElement.classList.add(state);
    
    // Show loader if available
    const loader = buttonElement.querySelector('.button-loader');
    if (loader) {
        loader.style.display = state === 'sending' ? 'block' : 'none';
    }
    
    // Update status message
    const statusElement = document.getElementById(`status-${command}`) || 
                         document.getElementById(`feedback-${command}`) ||
                         buttonElement.parentNode.querySelector('.button-status') ||
                         buttonElement.parentNode.querySelector('.button-feedback');
    
    if (statusElement) {
        statusElement.textContent = message;
        statusElement.className = statusElement.classList.contains('button-status') ? 
                                `button-status ${state}` : `button-feedback ${state}`;
    }
}

function resetButtonState(buttonElement) {
    // Remove state classes from button
    buttonElement.classList.remove('loading', 'success', 'error');
    
    // Hide loader
    const loader = buttonElement.querySelector('.button-loader');
    if (loader) {
        loader.style.display = 'none';
    }
    
    // Extract command from onclick attribute
    const command = buttonElement.getAttribute('onclick').match(/'([^']+)'/)[1];
    
    // Reset status message
    const statusElement = document.getElementById(`status-${command}`) || 
                         document.getElementById(`feedback-${command}`) ||
                         buttonElement.parentNode.querySelector('.button-status') ||
                         buttonElement.parentNode.querySelector('.button-feedback');
    
    if (statusElement) {
        statusElement.textContent = '';
        statusElement.className = statusElement.classList.contains('button-status') ? 
                                'button-status' : 'button-feedback';
    }
}

function sendCustomLinuxCommand() {
    const command = document.getElementById('customLinuxCommand').value;
    if (command) {
        sendCommand(command);
        document.getElementById('customLinuxCommand').value = '';
    } else {
        showMessage('⚠️ Please enter a custom command', 'error');
    }
}

// Handle Enter key in custom command input
document.getElementById('customLinuxCommand').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendCustomLinuxCommand();
    }
});

// Add click animation to all buttons
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.command-button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            this.style.transform = 'translateY(2px)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
});
