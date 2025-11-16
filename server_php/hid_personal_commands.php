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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">

            <h1>🐧 My Linux HID Control</h1>
            <p>Send keyboard shortcuts and commands remotely via your mobile phone</p>

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
                <button class="command-button terminal" onclick="sendCommand('ctrl+shift+t')">
                    <span>New Terminal Tab</span>
                    <span class="key-combination">Ctrl + Shift + T</span>
                </button>
                <button class="command-button terminal" onclick="sendCommand('ctrl+d')">
                    <span>Close Terminal</span>
                    <span class="key-combination">Ctrl + D</span>
                </button>
            </div>
<!--             <div class="command-description">
                <strong>Open Terminal:</strong> Opens new terminal window (common shortcut in Ubuntu/GNOME). <strong>New Terminal Tab:</strong> Opens new tab in existing terminal. <strong>Close Terminal:</strong> Closes terminal window or exits shell.
            </div> -->

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
 <!--            <div class="command-description">
                <strong>Interrupt Process:</strong> Stops currently running process. <strong>Suspend Process:</strong> Puts current process in background. <strong>Move to Line Start:</strong> Moves cursor to beginning of command line.
            </div> -->

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
<!--             <div class="command-description">
                <strong>Move to Line End:</strong> Moves cursor to end of command line. <strong>Clear to Line Start:</strong> Deletes from cursor to beginning of line. <strong>Clear to Line End:</strong> Deletes from cursor to end of line.
            </div> -->
        </div>

        <div class="command-section">
            <h3>💻Custom Commands</h3>
            
            <div class="button-group">
<!--                 <button class="command-button terminal" onclick="sendCommand('ctrl+shift+t')">
                    <span>New Terminal Tab</span>
                    <span class="key-combination">Ctrl + Shift + T</span>
                </button> -->
                <button class="command-button" onclick="sendCommand('date | tr \" \" \"_\"')">
                    <span>Send Current Date (with underscores)</span>
                    <span class="key-combination">date | tr " " "_"</span>
                </button>
            </div>
<!--             <div class="command-description">
                <strong>Open Terminal:</strong> Opens new terminal window (common shortcut in Ubuntu/GNOME). <strong>New Terminal Tab:</strong> Opens new tab in existing terminal. <strong>Close Terminal:</strong> Closes terminal window or exits shell.
            </div> -->
        </div>

        <!-- One-click text commands -->
        <div class="button-group">
            <h3>Quick Text Commands</h3>
            <button class="command-button" onclick="sendCommand('Hello World!')">Send "Hello World!"</button>
            <button class="command-button" onclick="sendCommand('sudo apt update')">Send "sudo apt update"</button>
            <button class="command-button" onclick="sendCommand('ls -la')">Send "ls -la"</button>
            <button class="command-button" onclick="sendCommand('pwd')">Send "pwd"</button>
            <button class="command-button" onclick="sendCommand('whoami')">Send "whoami"</button>
			<button class="command-button" onclick="sendCommand('whoami enter')">
			    <span>Send "whoami" + Enter</span>
			</button>
            <button class="command-button" onclick="sendCommand('date')">Send "date"</button>
        </div>
        
        <!-- One-click special commands -->
        <div class="button-group">
            <h3>Quick Special Commands</h3>
            <button class="command-button" onclick="sendCommand('enter')">↵ Enter Key</button>
            <button class="command-button" onclick="sendCommand('tab')">⇥ Tab Key</button>
            <button class="command-button" onclick="sendCommand('space')">␣ Space Bar</button>
            <button class="command-button" onclick="sendCommand('backspace')">⌫ Backspace</button>
            <button class="command-button" onclick="sendCommand('esc')">⎋ Escape</button>
        </div>
        
        <!-- One-click system commands -->
        <div class="button-group">
            <h3>Quick System Commands</h3>
            <button class="command-button" onclick="sendCommand('ctrl+c')">Ctrl + C (Copy/Interrupt)</button>
            <button class="command-button" onclick="sendCommand('ctrl+v')">Ctrl + V (Paste)</button>
            <button class="command-button" onclick="sendCommand('ctrl+a')">Ctrl + A (Select All)</button>
            <button class="command-button" onclick="sendCommand('ctrl+z')">Ctrl + Z (Undo)</button>
            <button class="command-button" onclick="sendCommand('ctrl+l')">Ctrl + L (Clear Terminal)</button>
        </div>


        <!-- Desktop Environment Commands -->
<!--         <div class="command-section">
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
        </div> -->

        <!-- System & Window Management -->
        <div class="command-section">
            <h3>⚙️ System & Window Management</h3>
            
            <div class="button-group">
<!--                 <button class="command-button" onclick="sendCommand('ctrl+alt+l')">
                    <span>Lock Screen</span>
                    <span class="key-combination">Ctrl + Alt + L</span>
                </button>
                <button class="command-button warning" onclick="sendCommand('ctrl+alt+del')">
                    <span>Logout Dialog</span>
                    <span class="key-combination">Ctrl + Alt + Del</span>
                </button> -->
                <button class="command-button" onclick="sendCommand('print')">
                    <span>Screenshot</span>
                    <span class="key-combination">PrtScn</span>
                </button>
            </div>
<!--             <div class="command-description">
                <strong>Lock Screen:</strong> Locks the screen (common in many distributions). <strong>Logout Dialog:</strong> Opens system logout/shutdown dialog. <strong>Screenshot:</strong> Takes full screen screenshot.
            </div> -->

<!--             <div class="button-group">
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
        </div> -->

        <!-- Text Editing & Browser -->
<!--         <div class="command-section">
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
        </div> -->

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

        function sendCommand(command) {
            fetch('hid_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'command=' + encodeURIComponent(command)
            })
            .then(response => response.text())
            .then(data => {
                showMessage('✅ Linux command executed: ' + command, 'success');
            })
            .catch(error => {
                showMessage('❌ Error sending command: ' + error, 'error');
            });
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
    </script>
</body>
</html>
