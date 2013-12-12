=== Plugin Name ===
Contributors: verysimple
Donate link: http://verysimple.com/products/simplesecure/
Tags: contact form, secure, pgp, gpg, secure form, form processor
Requires at least: 2.9
Tested up to: 3.9
Stable tag: trunk

SimpleSecure is a secure contact form plugin that encrypts messages using a pure PHP implementation of GPG / PGP.

== Description ==

SimpleSecure is a plugin that allows you to insert a secure contact form on any page or post.
The email message submitted by your visitor is securely encrypted using GPG/PGP, however no binaries are
required nor are any shell calls necessary.  SimpleSecure includes a pure PHP port of the GPG encryption functions
which allows it to run on any server that supports PHP.  In other words, you do not need to install GPG or allow shell 
access to PHP on your server.

The reason for implementing PGP in pure PHP is to make installation and configuration of the plugin
simple and easy.  You do not need admin access nor do you need to modify file permissions on your server.
If you have struggled with encrypted form processors in the past you'll be surprised at how SimpleSecure
"just works" out of the box with almost zero configuration.

PGP is a data encryption and decryption computer program that provides cryptographic privacy and 
authentication for data communication.  PGP can be used to send messages confidentially. For this, PGP 
combines symmetric-key encryption and public-key encryption. The message is encrypted using a symmetric 
encryption algorithm.  GnuPG (GPG) stands for GNU Privacy Guard and is GNU's tool for secure communication 
and data storage. It can be used to encrypt data and to create digital signatures. It is compliant with the 
OpenPGP Internet standard as described in RFC-4880.

= Features =

* Easily add a secure contact form using a WordPress shortcode on any page
* Manage your public GPG keys via the settings panel
* Pure PHP implementation of GPG - no binaries or shell access required

== Installation ==

Automatic Installation:

1. Go to Admin - Plugins - Add New and search for "simplesecure"
2. Click the Install Button
3. Click 'Activate'

Manual Installation:

1. Download simplesecure.zip
2. Unzip and upload the 'simplesecure' folder to your '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress

After installation view the PGP setup tutorial at http://www.youtube.com/watch?v=FFxFevczuRg

== Screenshots ==

1. SimpleSecure Secure Form
2. Upload a Public Key
3. Example Encrypted Message

== Frequently Asked Questions ==

= 1. What is SimpleSecure? =

SimpleSecure is a plugin that adds a secure contact form using GPG to encrypt messages

= 2. What is GPG and PGP? =

PGP is a software utility that is used to encrypt data using "public key encryption."

GPG is an open source version of PGP that is free for personal and commercial use. 
 
Public key encryption is a method of encryption that does not require any of
the parties to share any common, secret information, such as a password, in 
order to communication securely.

More info is at http://www.gnupg.org/

= 3. Where do I get GPG? =

GPG can be downloaded from http://www.gnupg.org/, however there are user
interfaces for Windows and Mac users at http://www.gpg4win.org/ and https://gpgtools.org/

= 4. How do I get started with GPG? =

A tutorial video is available at http://www.youtube.com/watch?v=FFxFevczuRg

The first step is to install GPG, either the command line version or a graphical
interface on your computer.  Once installed you will generate a public/private
key pair.  You can publish or share your public key with anyone and they use this
to send you encrypted messages.  The private key is kept confidential and you use
this to decrypt messages sent to you.

Once you have generated your keypair, you export your public key as a text file.
In SimpleSecure settings you provide your public key.  SimpleSecure will use this
key to encrypt messages that are sent to you.

= 4. What key types are supported? =

SimpleSecure uses a pure PHP implementation of GPG which supports a subset of the full
GPG functionality.  Supported keys are RSA and DSS up to 4096 bits in length.

= 5. My contact form shows an alert message that the page is not secure.  How can I get rid of that? =

This message appears when SimpleSecure detects that the website is not being 
browsed in SSL mode (ie HTTPS).  Without SSL enabled the information entered
by your visitor is sent from their computer to their server as plain, unencrypted
text.  It is possible that their information could be viewed by others.  Since
SimpleSecure is meant to be a secure form processor, a warning is displayed
when the page is not being viewed with SSL.

To get rid of this you should make sure that you link to the correct SSL URL for 
your contact page (ie HTTPS instead of HTTP).  If you do not have SSL enabled 
then you can contact your web host for more information.

There is no legitimate way to disable this alert otherwise - it is there for a reason.
If you don't want your visitors to see it then you should use a non-secure form plugin
instead.

= 6. I lost my private key and/or password.  Can you decrypt a message for me? =

No.  The whole point of encryption is that it cannot be decrypted without the 
private key and password.  For this reason you should keep your key and password
backed up in a secure location where you will not lose them and others will
not have access to them.

== Upgrade Notice ==

= 0.0.4 =
* fixed "not a MPI" error on PHP 5.5.x
* updated GPG library for compatibility with PHP 5.5.x
* updated GPG library to handle comment lines in a public key
* update GPG library to better report problems with public key

== Changelog ==

= 0.0.3 =
* fixed bug where email was sent to sender instead of recipient

= 0.0.2 =
* add basic form validation
* add session token

= 0.0.1 =
* initial checkin