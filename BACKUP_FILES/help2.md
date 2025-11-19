firstpi@raspberrypi:/etc/udev/rules.d $ ls -al
total 16
drwxr-xr-x 2 root root 4096 Nov 16 08:16 .
drwxr-xr-x 4 root root 4096 Nov 19 12:00 ..
-rw-r--r-- 1 root root 1883 Feb 24  2022 99-com.rules
-rw-r--r-- 1 root root   47 Nov 16 08:16 99-hidg.rules
firstpi@raspberrypi:/etc/udev/rules.d $ 


1. From local to remote:
scp /path/to/local/file user@remote_ip:/path/to/remote/directory




2. From remote to local:

scp firstpi@192.168.1.5:/etc/udev/rules.d.zip .




3. Copy a whole directory:

scp -r /path/to/local/folder user@remote_ip:/path/to/remote/





sudo zip -r bin.zip bin



firstpi@raspberrypi:/usr/local $ pwd
/usr/local
firstpi@raspberrypi:/usr/local $ 
firstpi@raspberrypi:/usr/local $ 
firstpi@raspberrypi:/usr/local $ 
firstpi@raspberrypi:/usr/local $ sudo zip -r bin.zip bin
  adding: bin/ (stored 0%)
  adding: bin/test-hid-keyboard.sh (deflated 52%)
  adding: bin/setup-hid-gadget.sh (deflated 62%)
  adding: bin/test-special-keys.sh (deflated 53%)
firstpi@raspberrypi:/usr/local $ 



scp firstpi@192.168.1.5:/usr/local/bin.zip .





firstpi@raspberrypi:/etc/systemd $ ls
journald.conf  networkd.conf  sleep.conf   timesyncd.conf
logind.conf    pstore.conf    system       user
network        resolved.conf  system.conf  user.conf
firstpi@raspberrypi:/etc/systemd $ pwd
/etc/systemd
firstpi@raspberrypi:/etc/systemd $ 





sudo zip -r system.zip system


scp firstpi@192.168.1.5:/etc/systemd/system.zip .





firstpi@raspberrypi:/boot $ sudo zip text_files.zip *.txt
  adding: cmdline.txt (deflated 10%)
  adding: config.txt (deflated 50%)
  adding: issue.txt (deflated 15%)


firstpi@raspberrypi:/boot $ pwd

firstpi@raspberrypi:/boot $ 




scp firstpi@192.168.1.5:/boot/text_files.zip .





delete files


cd /etc/udev/rules.d && sudo rm 99-hidg.rules

cd /usr/local/bin && sudo rm -rf *

cd /etc/systemd/system && sudo 

raspberrypi:/etc/systemd/system $ sudo rm -rf setup-hid.service
firstpi@raspberrypi:/etc/systemd/system $ sudo rm -rf hid-type.service
firstpi@raspberrypi:/etc/systemd/system $ 
firstpi@raspberrypi:/etc/systemd/system $ sudo rm -rf hid-gadget.service






Bus 001 Device 005: ID 1d6b:0104 Linux Foundation Multifunction Composite Gadget



find the all the seive startup


