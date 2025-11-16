<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raspberry Pi Zero HID Controller</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .notes {
            background-color: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        .code-block {
            background-color: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 20px 0;
        }
        .control-panel {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .button-group {
            margin: 15px 0;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        button:active {
            transform: scale(0.98);
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
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
    <div class="header">
        <h1>Raspberry Pi Zero HID Controller</h1>
        <p>Web-based HID Device Control Interface</p>
    </div>

    <div class="notes">
        <h3>About this HID Controller</h3>
        <p>This interface allows you to send keyboard input through your Raspberry Pi Zero configured as a HID device. The Pi will act as a keyboard when connected to another computer.</p>
    </div>

    <div class="code-block">
// HID Key Codes for reference
h = \x0b
e = \x08
l = \x0f
o = \x12
w = \x1a
r = \x15
d = \x07

Device: /dev/hidg0
    </div>

    <div class="control-panel">
        <h2>HID Control Panel</h2>
        
        <div class="status">
            <strong>Status:</strong> 
            <?php
            if (file_exists('/dev/hidg0')) {
                echo "HID Device Active - /dev/hidg0";
            } else {
                echo "HID Device Not Found!";
            }
            ?>
        </div>

        <div id="message" class="message"></div>

        <div class="button-group">
            <h3>Send Text Commands</h3>
            <button onclick="sendCommand('hello')">Send "hello"</button>
            <button onclick="sendCommand('world')">Send "world"</button>
            <button onclick="sendCommand('helloworld')">Send "hello world"</button>
            <button onclick="sendCommand('test')">Send "test"</button>
        </div>

        <div class="button-group">
            <h3>Special Commands</h3>
            <button onclick="sendCommand('enter')">Enter Key</button>
            <button onclick="sendCommand('tab')">Tab Key</button>
            <button onclick="sendCommand('space')">Space</button>
            <button onclick="sendCommand('backspace')">Backspace</button>
        </div>

        <div class="button-group">
            <h3>Custom Text</h3>
            <input type="text" id="customText" placeholder="Enter custom text" style="padding: 8px; width: 200px;">
            <button onclick="sendCustomText()">Send Custom Text</button>
        </div>

        <div class="button-group">
            <h3>Auto Typing</h3>
            <button onclick="startAutoTyping()">Start Auto Typing (hello world every 10s)</button>
            <button onclick="stopAutoTyping()">Stop Auto Typing</button>
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
            }, 3000);
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
                showMessage('Command sent: ' + command, 'success');
            })
            .catch(error => {
                showMessage('Error sending command: ' + error, 'error');
            });
        }

        function sendCustomText() {
            const text = document.getElementById('customText').value;
            if (text) {
                sendCommand(text);
                document.getElementById('customText').value = '';
            }
        }

        let autoTypingInterval;

        function startAutoTyping() {
            autoTypingInterval = setInterval(() => {
                sendCommand('helloworld');
            }, 10000);
            showMessage('Auto typing started - sending "hello world" every 10 seconds', 'success');
        }

        function stopAutoTyping() {
            if (autoTypingInterval) {
                clearInterval(autoTypingInterval);
                showMessage('Auto typing stopped', 'success');
            }
        }

        // Handle Enter key in custom text input
        document.getElementById('customText').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendCustomText();
            }
        });
    </script>
</body>
</html>
