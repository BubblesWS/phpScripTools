# phpScripTools
php script for easy web dev.

The resize.php file is a easy way to resize one or more image uploaded in your website:

Required parameter:
- Only one: the file (to resize one file), or "T" for all picture in the source folder.

Optional parameter:
- the source folder : by default the source is the same where the script run;
- the destination folder : by default make a folder "resized" into the same folder where the scipt run;
- the min and max dimension of resized image : by default if the image is more then 1000px, the resized img is the 33% (1/3), else 50%;
- the new file name : by default it's the same name;
- the overwrite : by default is false, do not overwrite the file if exist in the destination folder, to change set it on true

The allowed type ar PNG JPG JPEG GIF.

For example you can include resize.php in your gallery page, and automatize the resizing of all new image in a specific folder.
If overwrite is false, the script is very quickly because resize only the new files.

try it, test it, enjoy it!

Riccardo
License GNU
