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


