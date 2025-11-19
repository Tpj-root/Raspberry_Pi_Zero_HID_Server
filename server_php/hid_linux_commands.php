<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linux HID Commands - Raspberry Pi Zero HID</title>
    <style>
        body {
            font-family: 'Ubuntu', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            min-height: 100vh;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-top: 20px;
        }
        .header {
            background: linear-gradient(135deg, #e44d26, #f16529);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        .notes {
            background: #fff3e0;
            border-left: 5px solid #ff9800;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .notes h3 {
            color: #e65100;
            margin-top: 0;
        }
        .command-section {
            background: #fafafa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
        }
        .command-section h3 {
            color: #37474f;
            border-bottom: 2px solid #b0bec5;
            padding-bottom: 10px;
            margin-top: 0;
        }
        .button-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .command-button {
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            text-align: left;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .command-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .command-button:active {
            transform: translateY(0);
        }
        .command-button.terminal {
            background: linear-gradient(135deg, #37474f, #263238);
        }
        .command-button.danger {
            background: linear-gradient(135deg, #f44336, #d32f2f);
        }
        .command-button.warning {
            background: linear-gradient(135deg, #ff9800, #f57c00);
        }
        .command-button.gnome {
            background: linear-gradient(135deg, #4a6572, #344955);
        }
        .command-description {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #4caf50;
            font-size: 0.95em;
            color: #455a64;
        }
        .message {
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            display: none;
            font-weight: 500;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .nav-button {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .nav-button.primary {
            background: #e44d26;
        }
        .key-combination {
            font-family: 'Ubuntu Mono', monospace;
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9em;
        }
        .desktop-environment {
            font-size: 0.8em;
            opacity: 0.8;
            margin-left: 10px;
        }
        /* Option 1 Styles */
		.button-with-status {
		    display: flex;
		    align-items: center;
		    gap: 10px;
		    margin-bottom: 10px;
		}
		
		.button-status {
		    font-size: 12px;
		    padding: 4px 8px;
		    border-radius: 4px;
		    min-width: 80px;
		    text-align: center;
		    transition: all 0.3s ease;
		}
		
		.button-status.sending {
		    background: #ffa500;
		    color: white;
		}
		
		.button-status.success {
		    background: #4CAF50;
		    color: white;
		}
		
		.button-status.error {
		    background: #f44336;
		    color: white;
		}
		
		/* Option 2 Styles */
		.button-container {
		    margin-bottom: 15px;
		    position: relative;
		}
		
		.button-content {
		    display: flex;
		    flex-direction: column;
		    align-items: center;
		}
		
		.button-loader {
		    width: 20px;
		    height: 20px;
		    border: 2px solid #f3f3f3;
		    border-top: 2px solid #3498db;
		    border-radius: 50%;
		    animation: spin 1s linear infinite;
		    display: none;
		    margin-top: 5px;
		}
		
		.button-feedback {
		    font-size: 12px;
		    margin-top: 5px;
		    padding: 4px 8px;
		    border-radius: 4px;
		    text-align: center;
		    min-height: 20px;
		    transition: all 0.3s ease;
		}
		
		.button-feedback.success {
		    background: #d4edda;
		    color: #155724;
		    border: 1px solid #c3e6cb;
		}
		
		.button-feedback.error {
		    background: #f8d7da;
		    color: #721c24;
		    border: 1px solid #f5c6cb;
		}
		
		.button-feedback.sending {
		    background: #fff3cd;
		    color: #856404;
		    border: 1px solid #ffeaa7;
		}
		
		/* Button active states */
		.command-button:active {
		    transform: translateY(2px);
		}
		
		.command-button.loading {
		    opacity: 0.7;
		    cursor: not-allowed;
		}
		
		.command-button.success {
		    background: #4CAF50;
		}
		
		.command-button.error {
		    background: #f44336;
		}
		
		@keyframes spin {
		    0% { transform: rotate(0deg); }
		    100% { transform: rotate(360deg); }
		}
    </style>
</head>
<body>
<div class="nav-buttons" style="background: #2c3e50; padding: 15px 20px; display: flex; gap: 10px; flex-wrap: wrap;">
    <a href="index.php" style="background: #3498db; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 500;">🏠 Dashboard</a>
    <a href="hid_control.php" style="background: #27ae60; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 500;">⌨️ Basic Control</a>
    <a href="hid_windows_commands.php" style="background: #0078d4; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 500;">🪟 Windows</a>
    <a href="hid_linux_commands.php" style="background: #e44d26; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 500;">🐧 Linux</a>
</div>
    <div class="container">
        <div class="nav-buttons">
            <a href="hid_control.php" class="nav-button">Basic HID Control</a>
            <a href="hid_windows_commands.php" class="nav-button">Windows Commands</a>
            <a href="hid_linux_commands.php" class="nav-button primary">Linux Commands</a>
        </div>

        <div class="header">
            <h1>🐧 Linux HID Commands</h1>
            <p>Execute common Linux keyboard shortcuts and terminal commands remotely</p>
        </div>

        <div class="notes">
            <h3>📝 Important Notes</h3>
            <p><strong>Desktop Environments:</strong> Commands are optimized for GNOME, KDE, and Xfce. Some may vary between environments.</p>
            <p><strong>Terminal Commands:</strong> Terminal shortcuts work in most Linux terminal emulators (GNOME Terminal, Konsole, xterm, etc.).</p>
            <p><strong>Window Managers:</strong> Some commands may behave differently in tiling window managers like i3 or sway.</p>
        </div>
		<div id="message" class="message"></div>
		
		<!-- Terminal Commands -->
		<div class="command-section">
		    <h3>💻 Terminal Commands</h3>
		    
		    <div class="button-group">
		        <!-- Option 1: Simple status display -->
		        <div class="button-with-status">
		            <button class="command-button terminal" onclick="sendCommand('ctrl+alt+t', this)">
		                <span>Open Terminal <span class="desktop-environment">(GNOME/Ubuntu)</span></span>
		                <span class="key-combination">Ctrl + Alt + T</span>
		            </button>
		            <span class="button-status" id="status-ctrl+alt+t"></span>
		        </div>
		        
		        <!-- Option 2: Enhanced with loader -->
		        <div class="button-container">
		            <button class="command-button terminal" onclick="sendCommand('ctrl+shift+t', this)">
		                <div class="button-content">
		                    <span class="button-text">New Terminal Tab</span>
		                    <span class="key-combination">Ctrl + Shift + T</span>
		                </div>
		                <div class="button-loader"></div>
		            </button>
		            <div class="button-feedback" id="feedback-ctrl+shift+t"></div>
		        </div>
		        
		        <div class="button-container">
		            <button class="command-button terminal" onclick="sendCommand('ctrl+d', this)">
		                <div class="button-content">
		                    <span class="button-text">Close Terminal</span>
		                    <span class="key-combination">Ctrl + D</span>
		                </div>
		                <div class="button-loader"></div>
		            </button>
		            <div class="button-feedback" id="feedback-ctrl+d"></div>
		        </div>
		    </div>
		</div>
		            <div class="command-description">
                <strong>Open Terminal:</strong> Opens new terminal window (common shortcut in Ubuntu/GNOME). <strong>New Terminal Tab:</strong> Opens new tab in existing terminal. <strong>Close Terminal:</strong> Closes terminal window or exits shell.
            </div>

            <div class="button-group">
                <button class="command-button terminal" onclick="sendCommand('ctrl+c')">
                    <span>Interrupt Process</span>
                    <span class="key-combination">Ctrl + C</span>
                </button>
                <button class="command-button terminal" onclick="sendCommand('ctrl+z')">
                    <span>Suspend Process</span>
                    <span class="key-combination">Ctrl + Z</span>
                </button>
                <button class="command-button terminal" onclick="sendCommand('ctrl+a')">
                    <span>Move to Line Start</span>
                    <span class="key-combination">Ctrl + A</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Interrupt Process:</strong> Stops currently running process. <strong>Suspend Process:</strong> Puts current process in background. <strong>Move to Line Start:</strong> Moves cursor to beginning of command line.
            </div>

            <div class="button-group">
                <button class="command-button terminal" onclick="sendCommand('ctrl+e')">
                    <span>Move to Line End</span>
                    <span class="key-combination">Ctrl + E</span>
                </button>
                <button class="command-button terminal" onclick="sendCommand('ctrl+u')">
                    <span>Clear to Line Start</span>
                    <span class="key-combination">Ctrl + U</span>
                </button>
                <button class="command-button terminal" onclick="sendCommand('ctrl+k')">
                    <span>Clear to Line End</span>
                    <span class="key-combination">Ctrl + K</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Move to Line End:</strong> Moves cursor to end of command line. <strong>Clear to Line Start:</strong> Deletes from cursor to beginning of line. <strong>Clear to Line End:</strong> Deletes from cursor to end of line.
            </div>
        </div>

        <!-- Desktop Environment Commands -->
        <div class="command-section">
            <h3>🖥️ Desktop Environment</h3>
            
            <div class="button-group">
                <button class="command-button gnome" onclick="sendCommand('super')">
                    <span>Activities Overview <span class="desktop-environment">(GNOME)</span></span>
                    <span class="key-combination">Super</span>
                </button>
                <button class="command-button gnome" onclick="sendCommand('alt+f2')">
                    <span>Run Command <span class="desktop-environment">(GNOME)</span></span>
                    <span class="key-combination">Alt + F2</span>
                </button>
                <button class="command-button gnome" onclick="sendCommand('alt+tab')">
                    <span>Switch Applications</span>
                    <span class="key-combination">Alt + Tab</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Activities Overview:</strong> Opens GNOME Activities overview. <strong>Run Command:</strong> Opens command runner dialog. <strong>Switch Applications:</strong> Cycles through open applications.
            </div>

            <div class="button-group">
                <button class="command-button" onclick="sendCommand('alt+f1')">
                    <span>Application Menu</span>
                    <span class="key-combination">Alt + F1</span>
                </button>
                <button class="command-button" onclick="sendCommand('alt+f4')">
                    <span>Close Window</span>
                    <span class="key-combination">Alt + F4</span>
                </button>
                <button class="command-button" onclick="sendCommand('alt+f7')">
                    <span>Move Window</span>
                    <span class="key-combination">Alt + F7</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Application Menu:</strong> Opens application menu in many desktop environments. <strong>Close Window:</strong> Closes active window. <strong>Move Window:</strong> Allows moving window with arrow keys.
            </div>
        </div>

        <!-- System & Window Management -->
        <div class="command-section">
            <h3>⚙️ System & Window Management</h3>
            
            <div class="button-group">
                <button class="command-button" onclick="sendCommand('ctrl+alt+l')">
                    <span>Lock Screen</span>
                    <span class="key-combination">Ctrl + Alt + L</span>
                </button>
                <button class="command-button warning" onclick="sendCommand('ctrl+alt+del')">
                    <span>Logout Dialog</span>
                    <span class="key-combination">Ctrl + Alt + Del</span>
                </button>
                <button class="command-button" onclick="sendCommand('print')">
                    <span>Screenshot</span>
                    <span class="key-combination">PrtScn</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Lock Screen:</strong> Locks the screen (common in many distributions). <strong>Logout Dialog:</strong> Opens system logout/shutdown dialog. <strong>Screenshot:</strong> Takes full screen screenshot.
            </div>

            <div class="button-group">
                <button class="command-button" onclick="sendCommand('alt+print')">
                    <span>Screenshot Window</span>
                    <span class="key-combination">Alt + PrtScn</span>
                </button>
                <button class="command-button" onclick="sendCommand('shift+print')">
                    <span>Screenshot Area</span>
                    <span class="key-combination">Shift + PrtScn</span>
                </button>
                <button class="command-button" onclick="sendCommand('super+a')">
                    <span>Show Applications <span class="desktop-environment">(GNOME)</span></span>
                    <span class="key-combination">Super + A</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Screenshot Window:</strong> Takes screenshot of active window only. <strong>Screenshot Area:</strong> Allows selecting area for screenshot. <strong>Show Applications:</strong> Displays all installed applications.
            </div>
        </div>

        <!-- Text Editing & Browser -->
        <div class="command-section">
            <h3>📝 Text Editing & Browser</h3>
            
            <div class="button-group">
                <button class="command-button" onclick="sendCommand('ctrl+w')">
                    <span>Close Tab</span>
                    <span class="key-combination">Ctrl + W</span>
                </button>
                <button class="command-button" onclick="sendCommand('ctrl+tab')">
                    <span>Next Tab</span>
                    <span class="key-combination">Ctrl + Tab</span>
                </button>
                <button class="command-button" onclick="sendCommand('ctrl+shift+tab')">
                    <span>Previous Tab</span>
                    <span class="key-combination">Ctrl + Shift + Tab</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Close Tab:</strong> Closes current browser/editor tab. <strong>Next Tab:</strong> Switches to next tab. <strong>Previous Tab:</strong> Switches to previous tab.
            </div>

            <div class="button-group">
                <button class="command-button" onclick="sendCommand('ctrl+l')">
                    <span>Address Bar</span>
                    <span class="key-combination">Ctrl + L</span>
                </button>
                <button class="command-button" onclick="sendCommand('ctrl+h')">
                    <span>History</span>
                    <span class="key-combination">Ctrl + H</span>
                </button>
                <button class="command-button" onclick="sendCommand('ctrl+r')">
                    <span>Refresh</span>
                    <span class="key-combination">Ctrl + R</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Address Bar:</strong> Focuses browser address bar. <strong>History:</strong> Opens browser history. <strong>Refresh:</strong> Reloads current page.
            </div>
        </div>

        <!-- Custom Command -->
        <div class="command-section">
            <h3>🔧 Custom Linux Command</h3>
            <div class="button-group">
                <input type="text" id="customLinuxCommand" placeholder="Enter custom Linux command" 
                       style="padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; flex-grow: 1;">
                <button class="command-button" onclick="sendCustomLinuxCommand()" 
                        style="white-space: nowrap;">Execute Custom Command</button>
            </div>
            <div class="command-description">
                <strong>Custom Commands:</strong> Enter any Linux shortcut combination (e.g., "ctrl+alt+down" for workspace down, "super+space" for switcher).
            </div>
        </div>
    </div>

<script>
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
        .then(response => response.text())
        .then(data => {
            // Show success state for button if provided
            if (buttonElement) {
                showButtonState(buttonElement, 'success', 'Sent!');
            }
            showMessage('✅ Linux command executed: ' + command, 'success');
            
            // Reset button state after 2 seconds
            if (buttonElement) {
                setTimeout(() => {
                    resetButtonState(buttonElement);
                }, 2000);
            }
        })
        .catch(error => {
            // Show error state for button if provided
            if (buttonElement) {
                showButtonState(buttonElement, 'error', 'Failed!');
            }
            showMessage('❌ Error sending command: ' + error, 'error');
            
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
</script>
</body>
</html>
