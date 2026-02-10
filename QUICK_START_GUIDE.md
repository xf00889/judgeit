# Quick Start Guide - Judging Platform

## For Server Administrator

### Easy Method (Batch File):

1. **Double-click `start-server.bat`**
   - Right-click and select "Run as Administrator"
   - The server will start automatically
   - Your IP address will be displayed
   - Share the URL with all judges and tabulators

### Alternative Method (PowerShell):

1. **Right-click `start-server.ps1`**
   - Select "Run with PowerShell"
   - If prompted, allow execution

### Manual Method:

1. Open Command Prompt as Administrator
2. Navigate to project folder:
   ```cmd
   cd c:\laragon\www\judgingplatform
   ```
3. Run:
   ```cmd
   php artisan serve --host=0.0.0.0 --port=8000
   ```

## For Judges and Tabulators

1. **Connect to the network:**
   - LAN Cable: Plug into the network switch
   - WiFi: Connect to the designated network

2. **Open your browser**

3. **Enter the URL provided by the administrator:**
   ```
   http://192.168.X.X:8000
   ```
   (Replace with actual IP address)

4. **Login with your credentials:**
   - Username: (provided by admin)
   - Password: (provided by admin)

## Troubleshooting

### Server won't start?
- Make sure you're running as Administrator
- Check if port 8000 is already in use
- Verify PHP is installed and in PATH

### Can't connect from other devices?
- Verify all devices are on the same network
- Check the IP address is correct
- Try pinging the server: `ping SERVER_IP`
- Check Windows Firewall settings

### Need to create users?
Run from Command Prompt:
```cmd
php create_user.php
```

## Network Setup Checklist

- [ ] Server laptop connected to network switch/router
- [ ] All client laptops connected to same network
- [ ] Firewall rule added (automatic with batch file)
- [ ] Server started and IP address noted
- [ ] IP address shared with all users
- [ ] User accounts created
- [ ] Test login from at least one client device

## Event Day Checklist

- [ ] All devices charged/plugged in
- [ ] Network cables organized and labeled
- [ ] Backup server laptop ready
- [ ] Spare network cables available
- [ ] User credentials printed/documented
- [ ] Test connection from all devices
- [ ] Admin account accessible
- [ ] Backup of database taken

## Support

For technical issues during the event:
1. Check this guide first
2. Restart the server if needed
3. Verify network connections
4. Check firewall settings
5. Contact system administrator
