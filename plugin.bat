REM @echo off
cd /d %~dp0
set packagename=info.falkenbann.guildman

REM wegen Visiual Studio liegt meine \lib im root.

REM ich habe noch keine englische Sprachdatei, daher wird sie einfach kopiert.
copy plugin\language\de.xml plugin\language\en.xml

REM Clean up
rmdir plugin\files /s /q
rmdir plugin\templates /s /q
rmdir plugin\acptemplates /s /q
del plugin\*.tar
del %packagename%.tar 
del %packagename%.tar.gz


REM copy new files

xcopy wcf\lib\*.* plugin\files\lib\ /e /y
xcopy wcf\acp\templates\*.* plugin\acptemplates\ /e /y 
xcopy wcf\templates\*.* plugin\templates\ /e /y 
REM xcopy wcf\style\*.* plugin\files\style\ /e /y 
REM xcopy wcf\fonts\*.* plugin\files\fonts\ /e /y 
xcopy wcf\js\*.* plugin\files\js\ /e /y 
xcopy wcf\images\*.* plugin\files\images\ /e /y 
xcopy wcf\*.php plugin\files\ /y 

REM begin packing
cd plugin\

cd templates\
7za a -ttar template.tar *.*
cd .. 
cd acptemplates\
7za a -ttar acptemplate.tar *.*
cd .. 

move acptemplates\acptemplate.tar acptemplate.tar
move templates\template.tar template.tar
cd files
7za a -ttar files.tar lib\ -r
7za a -ttar files.tar fonts\ -r
7za a -ttar files.tar style\ -r
7za a -ttar files.tar images\ -r
7za a -ttar files.tar js\ -r
7za a -ttar files.tar global.php -r
7za a -ttar files.tar index.php -r
cd .. 
move files\files.tar files.tar
7za a -ttar %packagename%.tar *.tar 
7za a -ttar %packagename%.tar language\*.*
7za a -ttar %packagename%.tar *.xml
7za a -ttar %packagename%.tar *.sql
7za a -tgzip %packagename%.tar.gz %packagename%.tar
cd ..
REM del plugin\%packagename%.tar
move plugin\%packagename%.tar.gz %packagename%.tar.gz


