Here is a clean, simple, nice **README.md** version you can use:

---

# Raspberry Pi Zero HID Server

This project uses a **Raspberry Pi Zero** to run a lightweight Apache web server and host simple website files.

## 1. Install Apache

```bash
sudo apt update
sudo apt install apache2 -y
sudo apt install php libapache2-mod-php -y

```

## 2. Start + Enable Apache

```bash
sudo systemctl start apache2
sudo systemctl enable apache2
```

After this, Apache will automatically run after every reboot.

You can now open your Pi’s IP in any browser (mobile/PC) to see the default page.

---

## 3. Update Your Web Files

Place all your website files inside the local **server/** folder.
Use the `server_update.sh` script to replace the content in:

```
/var/www/html/
```

### server_update.sh

```bash
#!/bin/bash

SCRIPT_DIR="$(dirname "$0")"
SRC_DIR="$SCRIPT_DIR/server"
DEST_DIR="/var/www/html"

echo "Removing old files..."
sudo rm -rf ${DEST_DIR}/*

echo "Copying new files..."
sudo cp -r ${SRC_DIR}/* ${DEST_DIR}/

echo "Update complete!"
```

### Run the update

```bash
chmod +x server_update.sh
./server_update.sh
```

This will:

* Remove old files in `/var/www/html/`
* Copy your new files from the **server/** folder
* Refresh your website instantly

---

## Folder Structure

```
Raspberry_Pi_Zero_HID_Server/
│
├── README.md
├── server/
│   └── index.html  (your website files)
└── server_update.sh
```

---




Set Proper Permissions

```
# Add www-data user to the appropriate group
sudo usermod -a -G plugdev www-data

# Set permissions for the HID device
sudo chmod 666 /dev/hidg0

# Make the permission change persistent (create udev rule)
sudo nano /etc/udev/rules.d/99-hidg.rules

```


find device 

```
firstpi@raspberrypi:~ $ 
firstpi@raspberrypi:~ $ ls /dev/hidg0
/dev/hidg0
firstpi@raspberrypi:~ $ 
firstpi@raspberrypi:~ $ 
firstpi@raspberrypi:~ $ ls /sys/kernel/config/usb_gadget/hidg
bcdDevice  bDeviceClass     bDeviceSubClass  configs    idProduct  max_speed  strings
bcdUSB     bDeviceProtocol  bMaxPacketSize0  functions  idVendor   os_desc    UDC
firstpi@raspberrypi:~ $ ls /sys/kernel/config/usb_gadget/hidg/functions/
hid.usb0
firstpi@raspberrypi:~ $ 


firstpi@raspberrypi:~ $ cat /sys/kernel/config/usb_gadget/hidg/UDC
20980000.usb
firstpi@raspberrypi:~ $ 

```

❗ Warning
🟢 Working
❌ Error


