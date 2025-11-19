# Raspberry Pi HID Gadget Web Controller

A web-based HID (Human Interface Device) controller that turns your Raspberry Pi into a USB keyboard device. This project allows you to send keyboard commands remotely through a web interface.

## 🚀 Features

- **Web-based Control**: Send keyboard commands via a simple web interface
- **HID Gadget Emulation**: Raspberry Pi acts as a USB keyboard
- **Persistent Setup**: Automatic configuration on boot
- **Secure Access**: Optional password protection for web interface
- **Easy Deployment**: Simple setup scripts and systemd service

## 📋 Prerequisites

- Raspberry Pi (Tested on Pi Zero)
- Raspberry Pi OS (Bullseye or later)
- Apache web server
- PHP support

## 🛠️ Installation & Setup

### 1. HID Gadget Setup Script

Create the HID gadget configuration script:

```bash
sudo nano /usr/local/bin/setup-hid-gadget.sh
```

**How it works:**
- Creates a virtual USB HID gadget in the kernel configfs
- Sets up vendor/product IDs (Linux Foundation)
- Configures the device as a keyboard with proper HID report descriptor
- Enables the gadget and sets proper permissions

```bash
#!/bin/bash

# HID Gadget setup script
MODULE_DIR="/sys/kernel/config/usb_gadget"
GADGET_NAME="hidg"

# Wait for configfs to be available
while [ ! -d "$MODULE_DIR" ]; do
    sleep 1
done

# Check if gadget already exists
if [ ! -d "$MODULE_DIR/$GADGET_NAME" ]; then
    echo "Setting up HID gadget..."
    
    # Create gadget directory
    mkdir -p $MODULE_DIR/$GADGET_NAME
    cd $MODULE_DIR/$GADGET_NAME
    
    # Set vendor and product IDs (Linux Foundation)
    echo "0x1d6b" > idVendor
    echo "0x0104" > idProduct
    
    # Set device version
    echo "0x0100" > bcdDevice
    
    # Set USB version
    echo "0x0200" > bcdUSB
    
    # Set device class, subclass, and protocol
    echo "0x00" > bDeviceClass
    echo "0x00" > bDeviceSubClass
    echo "0x00" > bDeviceProtocol
    
    # Set maximum packet size
    echo "0x40" > bMaxPacketSize0
    
    # Create English string configuration
    mkdir -p strings/0x409
    echo "fedcba9876543210" > strings/0x409/serialnumber
    echo "Raspberry Pi" > strings/0x409/manufacturer
    echo "Pi Zero HID Keyboard" > strings/0x409/product
    
    # Create configuration
    mkdir -p configs/c.1/strings/0x409
    echo "Keyboard Configuration" > configs/c.1/strings/0x409/configuration
    echo "0x80" > configs/c.1/bmAttributes
    echo "100" > configs/c.1/MaxPower
    
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
    
    echo "HID gadget setup complete"
else
    echo "HID gadget already exists"
fi

# Set permissions for the HID device
if [ -e "/dev/hidg0" ]; then
    chmod 666 /dev/hidg0
    echo "Permissions set for /dev/hidg0"
fi
```

Make the script executable:
```bash
sudo chmod +x /usr/local/bin/setup-hid-gadget.sh
```

### 2. Systemd Service

Create a systemd service for automatic startup:

```bash
sudo nano /etc/systemd/system/hid-gadget.service
```

**How it works:**
- Runs the setup script on system boot
- Ensures the HID gadget is created before network services start
- Provides proper service management through systemd

```
[Unit]
Description=Setup HID USB Gadget
After=network.target
Wants=network.target

[Service]
Type=oneshot
RemainAfterExit=yes
ExecStart=/usr/local/bin/setup-hid-gadget.sh
ExecStop=/bin/echo "HID gadget stopped"
TimeoutSec=30

[Install]
WantedBy=multi-user.target
```

Enable and start the service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable hid-gadget.service
sudo systemctl start hid-gadget.service
```

### 3. Set Proper Permissions

Configure permissions for web server access to the HID device:

**How it works:**
- Adds the web server user to the appropriate group
- Sets persistent permissions through udev rules
- Ensures Apache can access the HID device

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

### 4. Additional UDEV Rule

Create additional udev rules for persistent permissions:

```bash
sudo nano /etc/udev/rules.d/99-hidg.rules
```

```
# Set permissions for HID gadget
SUBSYSTEM=="usb", ATTRS{idVendor}=="1d6b", ATTRS{idProduct}=="0104", MODE="0666"
SUBSYSTEM=="hid", KERNEL=="hidg0", MODE="0666"
```

Reload udev rules:
```bash
sudo udevadm control --reload-rules
sudo udevadm trigger
```

### 5. Web Server Configuration

Set index.php as the default page:

```bash
sudo nano /etc/apache2/mods-enabled/dir.conf
```

Ensure the configuration includes `index.php` first:
```
<IfModule mod_dir.c>
    DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
</IfModule>
```

### 6. Security (Optional)

For better security, add authentication:

**How it works:**
- Creates password protection for the web interface
- Uses Apache's basic authentication
- Restricts access to authorized users only

```bash
sudo apt update
sudo apt install apache2-utils
sudo htpasswd -c /etc/apache2/.htpasswd admin
```

Add protection to your web directory in Apache configuration:
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

## 🔧 Testing

### Post-Installation Check

After reboot, verify everything is working:

```bash
# Check if service is running
sudo systemctl status hid-gadget.service

# Check if HID device exists
ls -la /dev/hidg0

# Check if gadget is configured
ls /sys/kernel/config/usb_gadget/hidg/

# Check UDC
cat /sys/kernel/config/usb_gadget/hidg/UDC
```

### Manual Test Script

Create a test script to verify HID functionality:

```bash
sudo nano /usr/local/bin/test-hid.sh
```

**How it works:**
- Sends actual HID keycodes to the virtual keyboard device
- Tests both key press and key release events
- Verifies the HID gadget is working correctly

```bash
#!/bin/bash

HID_DEVICE="/dev/hidg0"

if [ ! -e "$HID_DEVICE" ]; then
    echo "HID device not found at $HID_DEVICE"
    exit 1
fi

echo "Testing HID device..."

# Function to send key
send_key() {
    local key_code=$1
    # Key down
    echo -ne "\x00\x00\x$key_code\x00\x00\x00\x00\x00" > $HID_DEVICE
    sleep 0.1
    # Key up
    echo -ne "\x00\x00\x00\x00\x00\x00\x00\x00" > $HID_DEVICE
    sleep 0.1
}

# Test with letter 'a' (HID code 0x04)
echo "Sending 'a' key..."
send_key "04"

echo "HID test completed"
```

Make it executable and run:
```bash
sudo chmod +x /usr/local/bin/test-hid.sh
/usr/local/bin/test-hid.sh
```

## 🌐 Usage

1. Connect your Raspberry Pi to a computer via USB
2. Access the web interface through your browser
3. Use the interface to send keyboard commands to the connected computer

## 🔒 Security Notes

- The HID device has broad permissions (666) - consider tightening this for production use
- Web interface authentication is recommended for network access
- Consider using HTTPS for remote access

## 🐛 Troubleshooting

- If the HID device doesn't appear, check the systemd service status
- Verify the Raspberry Pi is properly connected via USB
- Check Apache error logs for web interface issues
- Ensure all scripts have proper executable permissions

## 📝 License

This project is open source and available under the MIT License.

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!
