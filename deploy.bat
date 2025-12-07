@echo off
echo Deploying EWU Lost & Found to C:\xampp\htdocs\EWU-LostFound...
xcopy "C:\Users\User\.gemini\antigravity\scratch\EWU-LostFound" "C:\xampp\htdocs\EWU-LostFound" /E /I /Y
if %errorlevel% equ 0 (
    echo Deployment Successful!
    echo You can now access the site at http://localhost/EWU-LostFound
) else (
    echo Deployment Failed. Please check permissions or run as Administrator.
)

