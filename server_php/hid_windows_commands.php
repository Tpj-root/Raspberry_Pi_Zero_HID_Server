<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Windows HID Commands - Raspberry Pi Zero HID</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, #0078d4, #005a9e);
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
            background: #e3f2fd;
            border-left: 5px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .notes h3 {
            color: #1976d2;
            margin-top: 0;
        }
        .command-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
        }
        .command-section h3 {
            color: #495057;
            border-bottom: 2px solid #dee2e6;
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
            background: linear-gradient(135deg, #28a745, #20c997);
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
        .command-button.windows-key {
            background: linear-gradient(135deg, #0078d4, #106ebe);
        }
        .command-button.danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        .command-button.warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }
        .command-description {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            border-left: 4px solid #28a745;
            font-size: 0.95em;
            color: #495057;
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
            background: #007bff;
        }
        .key-combination {
            font-family: 'Consolas', monospace;
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.9em;
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
            <a href="hid_linux_commands.php" class="nav-button">Linux Commands</a>
            <a href="hid_windows_commands.php" class="nav-button primary">Windows Commands</a>
        </div>

        <div class="header">
            <h1>🪟 Windows HID Commands</h1>
            <p>Execute common Windows keyboard shortcuts and system commands remotely</p>
        </div>

        <div class="notes">
            <h3>📝 Important Notes</h3>
            <p><strong>Usage:</strong> These commands simulate keyboard shortcuts on the target Windows machine.</p>
            <p><strong>Security:</strong> Some commands may trigger security warnings or require administrator privileges.</p>
            <p><strong>Compatibility:</strong> Commands work on Windows 10/11. Some may vary based on Windows version and configuration.</p>
        </div>

        <div id="message" class="message"></div>

        <!-- System & Navigation Commands -->
        <div class="command-section">
            <h3>🖥️ System & Navigation Commands</h3>
            
            <div class="button-group">
                <button class="command-button windows-key" onclick="sendCommand('win')">
                    <span>Windows Key</span>
                    <span class="key-combination">Win</span>
                </button>
                <button class="command-button" onclick="sendCommand('win+r')">
                    <span>Run Dialog</span>
                    <span class="key-combination">Win + R</span>
                </button>
                <button class="command-button" onclick="sendCommand('win+e')">
                    <span>File Explorer</span>
                    <span class="key-combination">Win + E</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Windows Key:</strong> Opens Start Menu. <strong>Run Dialog:</strong> Opens Run command window. <strong>File Explorer:</strong> Opens Windows File Explorer.
            </div>

            <div class="button-group">
                <button class="command-button" onclick="sendCommand('alt+tab')">
                    <span>Switch Applications</span>
                    <span class="key-combination">Alt + Tab</span>
                </button>
                <button class="command-button" onclick="sendCommand('win+tab')">
                    <span>Task View</span>
                    <span class="key-combination">Win + Tab</span>
                </button>
                <button class="command-button" onclick="sendCommand('win+d')">
                    <span>Show Desktop</span>
                    <span class="key-combination">Win + D</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Switch Applications:</strong> Cycles through open applications. <strong>Task View:</strong> Shows all open windows and virtual desktops. <strong>Show Desktop:</strong> Minimizes all windows to show desktop.
            </div>
        </div>

        <!-- Application Management -->
        <div class="command-section">
            <h3>📊 Application Management</h3>
            
            <div class="button-group">
                <button class="command-button" onclick="sendCommand('alt+f4')">
                    <span>Close Application</span>
                    <span class="key-combination">Alt + F4</span>
                </button>
                <button class="command-button" onclick="sendCommand('ctrl+shift+esc')">
                    <span>Task Manager</span>
                    <span class="key-combination">Ctrl + Shift + Esc</span>
                </button>
                <button class="command-button" onclick="sendCommand('win+ctrl+d')">
                    <span>New Virtual Desktop</span>
                    <span class="key-combination">Win + Ctrl + D</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Close Application:</strong> Closes the active window. <strong>Task Manager:</strong> Directly opens Task Manager. <strong>New Virtual Desktop:</strong> Creates a new virtual desktop.
            </div>

            <div class="button-group">
                <button class="command-button" onclick="sendCommand('win+i')">
                    <span>Windows Settings</span>
                    <span class="key-combination">Win + I</span>
                </button>
                <button class="command-button" onclick="sendCommand('win+x')">
                    <span>Power User Menu</span>
                    <span class="key-combination">Win + X</span>
                </button>
                <button class="command-button" onclick="sendCommand('win+a')">
                    <span>Action Center</span>
                    <span class="key-combination">Win + A</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Windows Settings:</strong> Opens Windows Settings app. <strong>Power User Menu:</strong> Opens advanced system menu. <strong>Action Center:</strong> Opens notifications and quick actions panel.
            </div>
        </div>

        <!-- Text Editing & Selection -->
        <div class="command-section">
            <h3>📝 Text Editing & Selection</h3>
            
            <div class="button-group">
                <button class="command-button" onclick="sendCommand('ctrl+a')">
                    <span>Select All</span>
                    <span class="key-combination">Ctrl + A</span>
                </button>
                <button class="command-button" onclick="sendCommand('ctrl+c')">
                    <span>Copy</span>
                    <span class="key-combination">Ctrl + C</span>
                </button>
                <button class="command-button" onclick="sendCommand('ctrl+v')">
                    <span>Paste</span>
                    <span class="key-combination">Ctrl + V</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Select All:</strong> Selects all text/content in current document. <strong>Copy:</strong> Copies selected text to clipboard. <strong>Paste:</strong> Pastes clipboard content.
            </div>

            <div class="button-group">
                <button class="command-button" onclick="sendCommand('ctrl+x')">
                    <span>Cut</span>
                    <span class="key-combination">Ctrl + X</span>
                </button>
                <button class="command-button" onclick="sendCommand('ctrl+z')">
                    <span>Undo</span>
                    <span class="key-combination">Ctrl + Z</span>
                </button>
                <button class="command-button" onclick="sendCommand('ctrl+y')">
                    <span>Redo</span>
                    <span class="key-combination">Ctrl + Y</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Cut:</strong> Cuts selected text and copies to clipboard. <strong>Undo:</strong> Reverses the last action. <strong>Redo:</strong> Restores the last undone action.
            </div>
        </div>

        <!-- System Control -->
        <div class="command-section">
            <h3>⚙️ System Control</h3>
            
            <div class="button-group">
                <button class="command-button warning" onclick="sendCommand('win+ctrl+left')">
                    <span>Previous Virtual Desktop</span>
                    <span class="key-combination">Win + Ctrl + ←</span>
                </button>
                <button class="command-button warning" onclick="sendCommand('win+ctrl+right')">
                    <span>Next Virtual Desktop</span>
                    <span class="key-combination">Win + Ctrl + →</span>
                </button>
                <button class="command-button warning" onclick="sendCommand('win+ctrl+f4')">
                    <span>Close Virtual Desktop</span>
                    <span class="key-combination">Win + Ctrl + F4</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Virtual Desktop Navigation:</strong> Switch between virtual desktops. <strong>Close Virtual Desktop:</strong> Closes the current virtual desktop.
            </div>

            <div class="button-group">
                <button class="command-button danger" onclick="sendCommand('ctrl+alt+del')">
                    <span>Security Screen</span>
                    <span class="key-combination">Ctrl + Alt + Del</span>
                </button>
                <button class="command-button danger" onclick="sendCommand('alt+tab_hold')">
                    <span>Hold Alt+Tab</span>
                    <span class="key-combination">Alt (hold) + Tab</span>
                </button>
                <button class="command-button" onclick="sendCommand('win+prtscn')">
                    <span>Screenshot</span>
                    <span class="key-combination">Win + PrtScn</span>
                </button>
            </div>
            <div class="command-description">
                <strong>Security Screen:</strong> Opens Windows security options screen. <strong>Hold Alt+Tab:</strong> Keeps application switcher open. <strong>Screenshot:</strong> Takes screenshot and saves to Pictures folder.
            </div>
        </div>

        <!-- Custom Command -->
        <div class="command-section">
            <h3>🔧 Custom Windows Command</h3>
            <div class="button-group">
                <input type="text" id="customWinCommand" placeholder="Enter custom Windows command" 
                       style="padding: 12px; border: 2px solid #dee2e6; border-radius: 8px; flex-grow: 1;">
                <button class="command-button" onclick="sendCustomWinCommand()" 
                        style="white-space: nowrap;">Execute Custom Command</button>
            </div>
            <div class="command-description">
                <strong>Custom Commands:</strong> Enter any Windows shortcut combination (e.g., "win+l" for lock, "win+p" for projection settings).
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
                showMessage('✅ Windows command executed: ' + command, 'success');
            })
            .catch(error => {
                showMessage('❌ Error sending command: ' + error, 'error');
            });
        }

        function sendCustomWinCommand() {
            const command = document.getElementById('customWinCommand').value;
            if (command) {
                sendCommand(command);
                document.getElementById('customWinCommand').value = '';
            } else {
                showMessage('⚠️ Please enter a custom command', 'error');
            }
        }

        // Handle Enter key in custom command input
        document.getElementById('customWinCommand').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendCustomWinCommand();
            }
        });
    </script>
</body>
</html>
