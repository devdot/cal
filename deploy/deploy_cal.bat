rm com_cal.zip

php deploy.php


7z a com_cal.zip ../admin -x!admin/cal.xml
7z a com_cal.zip ../site
7z a com_cal.zip ../cal.xml
pause
