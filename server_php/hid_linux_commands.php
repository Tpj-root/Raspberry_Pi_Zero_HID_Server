<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linux HID Commands - Raspberry Pi Zero HID</title>
    <link rel="stylesheet" href="style.css">
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
        <!-- Side status version -->
        <div class="button-with-status">
            <div class="button-wrapper">
                <button class="command-button terminal" onclick="sendCommand('ctrl+alt+t', this)">
                    <div class="button-content">
                        <span class="button-text">Open Terminal <span class="desktop-environment">(GNOME/Ubuntu)</span></span>
                        <span class="key-combination">Ctrl + Alt + T</span>
                    </div>
                    <div class="button-loader"></div>
                </button>
                <span class="button-status" id="status-ctrl+alt+t"></span>
            </div>
            <div class="command-description">
                <strong>Open Terminal:</strong> Opens new terminal window (common shortcut in Ubuntu/GNOME desktop environments).
            </div>
        </div>
        
        <div class="button-with-status">
            <div class="button-wrapper">
                <button class="command-button terminal" onclick="sendCommand('ctrl+shift+t', this)">
                    <div class="button-content">
                        <span class="button-text">New Terminal Tab</span>
                        <span class="key-combination">Ctrl + Shift + T</span>
                    </div>
                    <div class="button-loader"></div>
                </button>
                <span class="button-status" id="status-ctrl+shift+t"></span>
            </div>
            <div class="command-description">
                <strong>New Terminal Tab:</strong> Opens a new tab in the currently focused terminal window (works in most terminal emulators).
            </div>
        </div>
        
        <div class="button-with-status">
            <div class="button-wrapper">
                <button class="command-button terminal" onclick="sendCommand('ctrl+d', this)">
                    <div class="button-content">
                        <span class="button-text">Close Terminal</span>
                        <span class="key-combination">Ctrl + D</span>
                    </div>
                    <div class="button-loader"></div>
                </button>
                <span class="button-status" id="status-ctrl+d"></span>
            </div>
            <div class="command-description">
                <strong>Close Terminal:</strong> Closes terminal window or exits current shell session (sends EOF signal).
            </div>
        </div>
    </div>
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

    <script src="script.js"></script>
</body>
</html>
