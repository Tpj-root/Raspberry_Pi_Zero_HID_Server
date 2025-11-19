<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Keyboard - Raspberry Pi HID</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }

        .header h1 {
            color: #333;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .nav-button {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .nav-button.primary {
            background: #e44d26;
        }

        #message {
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            display: none;
            font-weight: 500;
            text-align: center;
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

        /* Keyboard Styles */
        .keyboard {
            background: #2c3e50;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
            gap: 4px;
        }

        .key {
            background: #34495e;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 8px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            flex: 1;
            max-width: 70px;
            box-shadow: 0 3px 0 #2c3e50;
        }

        .key:hover {
            background: #4a6572;
            transform: translateY(-2px);
        }

        .key:active {
            transform: translateY(1px);
            box-shadow: 0 1px 0 #2c3e50;
        }

        .key.active {
            background: #27ae60 !important;
            transform: translateY(1px);
            box-shadow: 0 1px 0 #2c3e50;
        }

        /* Special key sizes */
        .key.esc { background: #e74c3c; }
        .key.tab { flex: 1.5; max-width: 105px; }
        .key.caps { flex: 1.8; max-width: 126px; }
        .key.enter { flex: 2.2; max-width: 154px; background: #3498db; }
        .key.shift { flex: 2.5; max-width: 175px; background: #9b59b6; }
        .key.ctrl, .key.alt, .key.super { flex: 1.2; max-width: 84px; background: #e67e22; }
        .key.space { flex: 6; max-width: 420px; }
        .key.backspace { flex: 1.8; max-width: 126px; background: #e74c3c; }
        .key.function { background: #8e44ad; }

        /* Function row */
        .function-row .key {
            flex: 1;
            max-width: 70px;
            background: #8e44ad;
        }

        /* Navigation keys */
        .nav-keys .key {
            background: #16a085;
        }

        /* Number pad */
        .numpad {
            background: #34495e;
            padding: 15px;
            border-radius: 8px;
            margin-left: 10px;
        }

        .numpad-row {
            display: flex;
            gap: 4px;
            margin-bottom: 4px;
        }

        .numpad .key {
            flex: 1;
            max-width: 60px;
            background: #4a6572;
        }

        .keyboard-main {
            display: flex;
        }

        .keyboard-left {
            flex: 3;
        }

        .keyboard-right {
            flex: 1;
            max-width: 200px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .key {
                padding: 10px 6px;
                font-size: 12px;
                min-height: 45px;
                max-width: 60px;
            }

            .keyboard-main {
                flex-direction: column;
            }

            .keyboard-right {
                max-width: none;
                margin-left: 0;
                margin-top: 10px;
            }

            .numpad {
                margin-left: 0;
            }

            .nav-buttons {
                justify-content: center;
            }

            .nav-button {
                padding: 8px 15px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .key {
                padding: 8px 4px;
                font-size: 11px;
                min-height: 40px;
                max-width: 50px;
            }

            .key.tab { max-width: 80px; }
            .key.caps { max-width: 100px; }
            .key.enter { max-width: 120px; }
            .key.shift { max-width: 140px; }
            .key.space { max-width: 300px; }
        }

        /* Status indicator */
        .status {
            text-align: center;
            padding: 10px;
            background: #ecf0f1;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .keyboard-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .control-button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .control-button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-buttons">
            <a href="index.php" class="nav-button">🏠 Dashboard</a>
            <a href="hid_control.php" class="nav-button">⌨️ Basic Control</a>
            <a href="hid_windows_commands.php" class="nav-button">🪟 Windows</a>
            <a href="hid_linux_commands.php" class="nav-button">🐧 Linux</a>
            <a href="keyboard.php" class="nav-button primary">⌨️ Virtual Keyboard</a>
        </div>

        <div class="header">
            <h1>⌨️ Virtual Keyboard</h1>
            <p>Full 104-key keyboard with visual feedback - Tap keys to send HID commands</p>
        </div>

        <div id="message" class="message"></div>

        <div class="status">
            🔄 <span id="connectionStatus">Ready to send keyboard commands</span>
        </div>

        <div class="keyboard-controls">
            <button class="control-button" onclick="sendText('Hello World!')">Type "Hello World!"</button>
            <button class="control-button" onclick="sendCommand('enter')">Press Enter</button>
            <button class="control-button" onclick="sendCommand('tab')">Press Tab</button>
            <button class="control-button" onclick="clearActiveKeys()">Clear All Highlights</button>
        </div>

        <div class="keyboard">
            <!-- Function Keys Row -->
            <div class="keyboard-row function-row">
                <button class="key function" onclick="sendCommand('esc')">Esc</button>
                <button class="key function" onclick="sendCommand('f1')">F1</button>
                <button class="key function" onclick="sendCommand('f2')">F2</button>
                <button class="key function" onclick="sendCommand('f3')">F3</button>
                <button class="key function" onclick="sendCommand('f4')">F4</button>
                <button class="key function" onclick="sendCommand('f5')">F5</button>
                <button class="key function" onclick="sendCommand('f6')">F6</button>
                <button class="key function" onclick="sendCommand('f7')">F7</button>
                <button class="key function" onclick="sendCommand('f8')">F8</button>
                <button class="key function" onclick="sendCommand('f9')">F9</button>
                <button class="key function" onclick="sendCommand('f10')">F10</button>
                <button class="key function" onclick="sendCommand('f11')">F11</button>
                <button class="key function" onclick="sendCommand('f12')">F12</button>
            </div>

            <div class="keyboard-main">
                <div class="keyboard-left">
                    <!-- Main Keyboard Area -->
                    
                    <!-- Number Row -->
                    <div class="keyboard-row">
                        <button class="key" onclick="sendText('`')">` ~</button>
                        <button class="key" onclick="sendText('1')">1 !</button>
                        <button class="key" onclick="sendText('2')">2 @</button>
                        <button class="key" onclick="sendText('3')">3 #</button>
                        <button class="key" onclick="sendText('4')">4 $</button>
                        <button class="key" onclick="sendText('5')">5 %</button>
                        <button class="key" onclick="sendText('6')">6 ^</button>
                        <button class="key" onclick="sendText('7')">7 &</button>
                        <button class="key" onclick="sendText('8')">8 *</button>
                        <button class="key" onclick="sendText('9')">9 (</button>
                        <button class="key" onclick="sendText('0')">0 )</button>
                        <button class="key" onclick="sendText('-')">- _</button>
                        <button class="key" onclick="sendText('=')">= +</button>
                        <button class="key backspace" onclick="sendCommand('backspace')">Backspace</button>
                    </div>

                    <!-- QWERTY Row -->
                    <div class="keyboard-row">
                        <button class="key tab" onclick="sendCommand('tab')">Tab</button>
                        <button class="key" onclick="sendText('q')">Q</button>
                        <button class="key" onclick="sendText('w')">W</button>
                        <button class="key" onclick="sendText('e')">E</button>
                        <button class="key" onclick="sendText('r')">R</button>
                        <button class="key" onclick="sendText('t')">T</button>
                        <button class="key" onclick="sendText('y')">Y</button>
                        <button class="key" onclick="sendText('u')">U</button>
                        <button class="key" onclick="sendText('i')">I</button>
                        <button class="key" onclick="sendText('o')">O</button>
                        <button class="key" onclick="sendText('p')">P</button>
                        <button class="key" onclick="sendText('[')">[ {</button>
                        <button class="key" onclick="sendText(']')">] }</button>
                        <button class="key" onclick="sendText('\\')">\ |</button>
                    </div>

                    <!-- ASDF Row -->
                    <div class="keyboard-row">
                        <button class="key caps" onclick="toggleCapsLock()">Caps Lock</button>
                        <button class="key" onclick="sendText('a')">A</button>
                        <button class="key" onclick="sendText('s')">S</button>
                        <button class="key" onclick="sendText('d')">D</button>
                        <button class="key" onclick="sendText('f')">F</button>
                        <button class="key" onclick="sendText('g')">G</button>
                        <button class="key" onclick="sendText('h')">H</button>
                        <button class="key" onclick="sendText('j')">J</button>
                        <button class="key" onclick="sendText('k')">K</button>
                        <button class="key" onclick="sendText('l')">L</button>
                        <button class="key" onclick="sendText(';')">; :</button>
                        <button class="key" onclick="sendText("'")">' "</button>
                        <button class="key enter" onclick="sendCommand('enter')">Enter</button>
                    </div>

                    <!-- ZXCV Row -->
                    <div class="keyboard-row">
                        <button class="key shift" onclick="sendCommand('shift')">Shift</button>
                        <button class="key" onclick="sendText('z')">Z</button>
                        <button class="key" onclick="sendText('x')">X</button>
                        <button class="key" onclick="sendText('c')">C</button>
                        <button class="key" onclick="sendText('v')">V</button>
                        <button class="key" onclick="sendText('b')">B</button>
                        <button class="key" onclick="sendText('n')">N</button>
                        <button class="key" onclick="sendText('m')">M</button>
                        <button class="key" onclick="sendText(',')">, <</button>
                        <button class="key" onclick="sendText('.')">. ></button>
                        <button class="key" onclick="sendText('/')">/ ?</button>
                        <button class="key shift" onclick="sendCommand('shift')">Shift</button>
                    </div>

                    <!-- Bottom Row -->
                    <div class="keyboard-row">
                        <button class="key ctrl" onclick="sendCommand('ctrl')">Ctrl</button>
                        <button class="key super" onclick="sendCommand('super')">Win</button>
                        <button class="key alt" onclick="sendCommand('alt')">Alt</button>
                        <button class="key space" onclick="sendText(' ')">Space</button>
                        <button class="key alt" onclick="sendCommand('alt')">Alt</button>
                        <button class="key super" onclick="sendCommand('super')">Win</button>
                        <button class="key ctrl" onclick="sendCommand('ctrl')">Ctrl</button>
                    </div>

                    <!-- Navigation Keys -->
                    <div class="keyboard-row nav-keys">
                        <button class="key" onclick="sendCommand('print')">Print</button>
                        <button class="key" onclick="sendCommand('scrolllock')">Scroll</button>
                        <button class="key" onclick="sendCommand('pause')">Pause</button>
                        <button class="key" onclick="sendCommand('insert')">Insert</button>
                        <button class="key" onclick="sendCommand('home')">Home</button>
                        <button class="key" onclick="sendCommand('pageup')">PgUp</button>
                        <button class="key" onclick="sendCommand('delete')">Delete</button>
                        <button class="key" onclick="sendCommand('end')">End</button>
                        <button class="key" onclick="sendCommand('pagedown')">PgDn</button>
                        <button class="key" onclick="sendCommand('up')">↑</button>
                    </div>

                    <!-- Arrow Keys -->
                    <div class="keyboard-row nav-keys">
                        <div style="flex: 1;"></div>
                        <button class="key" onclick="sendCommand('left')">←</button>
                        <button class="key" onclick="sendCommand('down')">↓</button>
                        <button class="key" onclick="sendCommand('right')">→</button>
                    </div>
                </div>

                <!-- Number Pad -->
                <div class="keyboard-right">
                    <div class="numpad">
                        <div class="numpad-row">
                            <button class="key" onclick="sendCommand('numlock')">Num</button>
                            <button class="key" onclick="sendText('/')">/</button>
                            <button class="key" onclick="sendText('*')">*</button>
                            <button class="key" onclick="sendText('-')">-</button>
                        </div>
                        <div class="numpad-row">
                            <button class="key" onclick="sendText('7')">7</button>
                            <button class="key" onclick="sendText('8')">8</button>
                            <button class="key" onclick="sendText('9')">9</button>
                            <button class="key" onclick="sendText('+')" style="min-height: 104px; margin-top: -52px;">+</button>
                        </div>
                        <div class="numpad-row">
                            <button class="key" onclick="sendText('4')">4</button>
                            <button class="key" onclick="sendText('5')">5</button>
                            <button class="key" onclick="sendText('6')">6</button>
                        </div>
                        <div class="numpad-row">
                            <button class="key" onclick="sendText('1')">1</button>
                            <button class="key" onclick="sendText('2')">2</button>
                            <button class="key" onclick="sendText('3')">3</button>
                            <button class="key" onclick="sendCommand('enter')" style="min-height: 104px; margin-top: -52px;">Enter</button>
                        </div>
                        <div class="numpad-row">
                            <button class="key" onclick="sendText('0')" style="flex: 2; max-width: 124px;">0</button>
                            <button class="key" onclick="sendText('.')">.</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let capsLockActive = false;
        let shiftActive = false;

        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = text;
            messageDiv.className = 'message ' + type;
            messageDiv.style.display = 'block';
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 3000);
        }

        function highlightKey(keyElement) {
            keyElement.classList.add('active');
            setTimeout(() => {
                keyElement.classList.remove('active');
            }, 300);
        }

        function sendCommand(command) {
            const keyElement = event.target;
            highlightKey(keyElement);

            fetch('hid_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'command=' + encodeURIComponent(command)
            })
            .then(response => response.text())
            .then(data => {
                console.log('Command sent:', command, 'Response:', data);
                showMessage('✅ Key pressed: ' + command, 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('❌ Error sending key: ' + command, 'error');
            });
        }

        function sendText(text) {
            const keyElement = event.target;
            highlightKey(keyElement);

            // Apply caps lock if active
            if (capsLockActive && text.length === 1 && /[a-z]/.test(text)) {
                text = text.toUpperCase();
            }

            // Apply shift for single characters
            if (shiftActive && text.length === 1) {
                const shiftMap = {
                    '`': '~', '1': '!', '2': '@', '3': '#', '4': '$', '5': '%',
                    '6': '^', '7': '&', '8': '*', '9': '(', '0': ')', '-': '_',
                    '=': '+', '[': '{', ']': '}', '\\': '|', ';': ':', "'": '"',
                    ',': '<', '.': '>', '/': '?'
                };
                text = shiftMap[text] || text.toUpperCase();
            }

            fetch('hid_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'command=' + encodeURIComponent(text)
            })
            .then(response => response.text())
            .then(data => {
                console.log('Text sent:', text, 'Response:', data);
                showMessage('✅ Character sent: ' + text, 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('❌ Error sending character: ' + text, 'error');
            });
        }

        function toggleCapsLock() {
            capsLockActive = !capsLockActive;
            const capsKey = event.target;
            highlightKey(capsKey);
            
            if (capsLockActive) {
                capsKey.style.background = '#27ae60';
                showMessage('✅ Caps Lock ON', 'success');
            } else {
                capsKey.style.background = '';
                showMessage('✅ Caps Lock OFF', 'success');
            }
        }

        function clearActiveKeys() {
            const keys = document.querySelectorAll('.key.active');
            keys.forEach(key => key.classList.remove('active'));
            
            // Reset caps lock
            capsLockActive = false;
            const capsKey = document.querySelector('.key.caps');
            if (capsKey) capsKey.style.background = '';
            
            showMessage('✅ All keys cleared', 'success');
        }

        // Touch device support
        document.addEventListener('touchstart', function(e) {
            if (e.target.classList.contains('key')) {
                e.preventDefault();
            }
        }, { passive: false });

        // Keyboard shortcut support
        document.addEventListener('keydown', function(e) {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                const key = e.key.toLowerCase();
                
                // Map keyboard events to our virtual keyboard
                if (key === 'capslock') {
                    toggleCapsLock();
                } else if (key === 'shift') {
                    shiftActive = true;
                }
            }
        });

        document.addEventListener('keyup', function(e) {
            if (e.key.toLowerCase() === 'shift') {
                shiftActive = false;
            }
        });
    </script>
</body>
</html>