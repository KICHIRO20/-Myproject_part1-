

Avactis Theme: Orange
====================

This Avactis theme is developed for use with Avactis Shopping Cart 1.9.1.

If the version of your Avactis Shopping Cart is older, you need to update
your Avactis up to the version 1.9.1.



Installing Avactis Theme
========================

1.  Download Avactis theme package on your local computer.
    The name of the archive will be avactis.1.9.1.theme.orange.zip

2.  Unpack this archive on your local computer using your favorite
    file archiver software (e.g. WinZip or WinRar).

    After unpacking you will see the "avactis.1.9.1.theme.orange"
    folder with several subdirectories and theme files.

3.  Execute your favorite FTP client (e.g. FileZilla) and connect with
    your server using FTP.

    If you do not remember your FTP access details, please contact
    your hosting provider support.

4.  Using your FTP client, open the directory with installed Avactis
    Cart, and open the "avactis-themes" directory.

5.  Upload the "avactis.1.9.1.theme.orange" folder from your local
    computer, to the "avactis-themes" folder on your server using FTP
    client.
	
    After uploading, you should find  the "avactis.1.9.1.theme.orange"
    directory in the "avactis-themes".
	
    It is recommended to make "/css" and "/js" folders writable by PHP
    in this directory.

6.  Using your FTP client, navigate to the "avactis-layouts" folder on
    your server and open the "storefront-layout.ini" file.
	
    If your FTP client is not able to modify files, download the
    "storefront-layout.ini" file on your local computer and open it using
    any simple text editor.

7.  Find this line of the code inside the "storefront-layout.ini":

    TemplateDirectory = "avactis-themes/system/"

    And replace it with the following line:

    TemplateDirectory = "avactis-themes/avactis.1.9.1.theme.orange/"

    Upload modified file back on your server, or save it in your FTP text editor.

8.  Avactis theme is successfully installed. 

    Open the storefront of your Avactis Shopping Cart and refresh the page.
    You will see new design of the storefront.


How to change a storefront logo
==============
Logotype of the theme is located in: "avactis.1.9.1.theme.orange/images/logo.png" file.
You can replace this logotype with your own.


How to change a Welcome Text
============
This text is located in the "avactis.1.9.1.theme.orange/welcome.html" file.
You can change this text anyway you like.


Support
============
If you will need any help with the theme installation, please contact our support team:
https://www.avactis.com/support.php

