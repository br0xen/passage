# PasSage - The Password Sage #

### What is this repository for? ###
A Storage-less Password Locker compatible with pastor from appnician: https://github.com/appnician/pastor

### Usage Instructions ###
* Enter your 'Pin' (this is 'pass-phrase' from pastor)
* You will get a checksum back, this is to verify that you typed your pin correctly. Remember it if you want. :)
* Enter a 'Door ID'
  * This can be anything, just something you will remember to recall the password that is being generated.
  * Examples: 'email' or 'google account'
  * It's basically an identifier for what you are logging in to.
* The password is generated by combining the ping and the door id to create a new hash that then roughly becomes the password.

### How do I get set up? ###

Basically, just set it up the way you would any other PHP application.
Make sure that your web server is looking at the "public" directory as the root.
mod_rewrite in apache with these rules makes things better:

```
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-f
    RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /index.php/$1 [L]
```


SSL is definitely recommended.

### Questions? Comments? ###
Feel free to contact me however you can. :D
