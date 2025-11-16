## 1. Create the PHP Script for HID Control

Create a new PHP file in your web directory:

```bash
sudo nano /var/www/html/hid_control.php
```

```php
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
```

## 2. Create the PHP Handler Script

Create the backend PHP script that will handle the HID commands:

```bash
sudo nano /var/www/html/hid_handler.php
```

```php
<?php
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$command = $_POST['command'] ?? '';

if (empty($command)) {
    http_response_code(400);
    echo "No command provided";
    exit;
}

// HID key codes (USB HID Usage IDs for keyboard)
$key_codes = [
    'a' => "\x04", 'b' => "\x05", 'c' => "\x06", 'd' => "\x07", 'e' => "\x08",
    'f' => "\x09", 'g' => "\x0a", 'h' => "\x0b", 'i' => "\x0c", 'j' => "\x0d",
    'k' => "\x0e", 'l' => "\x0f", 'm' => "\x10", 'n' => "\x11", 'o' => "\x12",
    'p' => "\x13", 'q' => "\x14", 'r' => "\x15", 's' => "\x16", 't' => "\x17",
    'u' => "\x18", 'v' => "\x19", 'w' => "\x1a", 'x' => "\x1b", 'y' => "\x1c",
    'z' => "\x1d",
    '1' => "\x1e", '2' => "\x1f", '3' => "\x20", '4' => "\x21", '5' => "\x22",
    '6' => "\x23", '7' => "\x24", '8' => "\x25", '9' => "\x26", '0' => "\x27",
    ' ' => "\x2c", // Space
];

// Special commands
$special_commands = [
    'enter' => "\x28",
    'tab' => "\x2b",
    'backspace' => "\x2a",
    'space' => "\x2c",
];

$device = '/dev/hidg0';

if (!file_exists($device)) {
    http_response_code(500);
    echo "HID device not found";
    exit;
}

function send_key($device, $key_code) {
    // Key down: send the key code
    file_put_contents($device, "\x00\x00" . $key_code . "\x00\x00\x00\x00\x00");
    usleep(50000); // 50ms delay
    
    // Key up: send all zeros
    file_put_contents($device, "\x00\x00\x00\x00\x00\x00\x00\x00");
    usleep(50000); // 50ms delay
}

try {
    if (array_key_exists($command, $special_commands)) {
        // Handle special commands
        send_key($device, $special_commands[$command]);
        echo "Special command executed: " . $command;
    } else {
        // Handle text input
        $chars = str_split(strtolower($command));
        foreach ($chars as $char) {
            if (isset($key_codes[$char])) {
                send_key($device, $key_codes[$char]);
            }
        }
        echo "Text sent: " . $command;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>
```

## 3. Set Proper Permissions

Set the correct permissions for the web server to access the HID device:

```bash
# Add www-data user to the appropriate group
sudo usermod -a -G plugdev www-data

# Set permissions for the HID device
sudo chmod 666 /dev/hidg0

# Make the permission change persistent (create udev rule)
sudo nano /etc/udev/rules.d/99-hidg.rules
```

Add this line to the udev rules file:
```
SUBSYSTEM=="usb", MODE="0666", GROUP="plugdev"
```

Reload udev rules:
```bash
sudo udevadm control --reload-rules
sudo udevadm trigger
```

## 4. Test the Web Interface

Now access your web interface by navigating to:
```
http://[your-pi-ip-address]/hid_control.php
```

## 5. Create a Startup Script (Optional)

To ensure your HID gadget starts automatically, create a startup script:

```bash
sudo nano /usr/local/bin/start_hid_gadget.sh
```

```bash
#!/bin/bash

# HID Gadget setup script
MODULE_DIR="/sys/kernel/config/usb_gadget"
GADGET_NAME="hidg"

# Check if gadget already exists
if [ ! -d "$MODULE_DIR/$GADGET_NAME" ]; then
    # Create gadget directory
    mkdir -p $MODULE_DIR/$GADGET_NAME
    cd $MODULE_DIR/$GADGET_NAME
    
    # Set vendor and product IDs
    echo "0x1d6b" > idVendor
    echo "0x0104" > idProduct
    
    # Set device version
    echo "0x0100" > bcdDevice
    
    # Set device class, subclass, and protocol
    echo "0x00" > bDeviceClass
    echo "0x00" > bDeviceSubClass
    echo "0x00" > bDeviceProtocol
    
    # Create configuration
    mkdir -p configs/c.1/strings/0x409
    echo "Config 1: HID" > configs/c.1/strings/0x409/configuration
    echo "0x80" > configs/c.1/bmAttributes
    echo "500" > configs/c.1/MaxPower
    
    # Create HID function
    mkdir -p functions/hid.usb0
    echo "1" > functions/hid.usb0/protocol
    echo "1" > functions/hid.usb0/subclass
    echo "8" > functions/hid.usb0/report_length
    
    # Set HID report descriptor (simple keyboard)
    echo -ne "\x05\x01\x09\x06\xa1\x01\x05\x07\x19\xe0\x29\xe7\x15\x00\x25\x01\x75\x01\x95\x08\x81\x02\x95\x01\x75\x08\x81\x03\x95\x05\x75\x01\x05\x08\x19\x01\x29\x05\x91\x02\x95\x01\x75\x03\x91\x03\x95\x06\x75\x08\x15\x00\x25\x65\x05\x07\x19\x00\x29\x65\x81\x00\xc0" > functions/hid.usb0/report_desc
    
    # Link function to configuration
    ln -s functions/hid.usb0 configs/c.1/
    
    # Enable the gadget
    echo "20980000.usb" > UDC
fi
```

Make it executable:
```bash
sudo chmod +x /usr/local/bin/start_hid_gadget.sh
```

## 6. Security Considerations

For better security, you might want to add authentication:

```bash
sudo apt update
sudo apt install apache2-utils
sudo htpasswd -c /etc/apache2/.htpasswd admin
```

Then create a protected directory:
```bash
sudo nano /etc/apache2/sites-available/000-default.conf
```

Add inside the `<VirtualHost>` section:
```
<Directory "/var/www/html/hid_control.php">
    AuthType Basic
    AuthName "Restricted Content"
    AuthUserFile /etc/apache2/.htpasswd
    Require valid-user
</Directory>
```

Restart Apache:
```bash
sudo systemctl restart apache2
```

## Usage

1. Connect your Pi Zero to a target computer via USB
2. Access the web interface from any device on the same network
3. Use the buttons to send HID commands to the target computer

The interface includes:
- Pre-defined text commands
- Special keys (Enter, Tab, etc.)
- Custom text input
- Auto-typing functionality
- Real-time status display

This setup transforms your Raspberry Pi Zero into a web-controllable HID device that can simulate keyboard input on any connected computer!
