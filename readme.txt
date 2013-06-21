=== Plugin Name ===
Contributors: verysimple
Donate link: http://verysimple.com/products/simplesecure/
Tags: contact form, secure, pgp, gpg, secure form, form processor
Requires at least: 2.9
Tested up to: 3.6
Stable tag: trunk

SimpleSecure is a secure contact form plugin that uses GPG to encrypt messages.

== Description ==

SimpleSecure is a plugin that allows you to put a secure contact form on your WordPress site.
The information submitted by the visitor will be encrypted using GPG and sent to your
email address where you can decrypt it using your private key and password.

GPG is a secure public-key encryption system for sending secure data.

This plugin is dedicated to the NSA and the PRISM system.

= Features =

* Easily add a secure contact form using a WordPress shortcode on any page
* Manage your public GPG keys via the settings panel
* Pure PHP implementation of GPG means no GPG binaries or shell access are required on the server

== Installation ==

Automatic Installation:

1. Go to Admin - Plugins - Add New and search for "simplesecure"
2. Click the Install Button
3. Click 'Activate'

Manual Installation:

1. Download simplesecure.zip
2. Unzip and upload the 'simplesecure' folder to your '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Example SimpleSecure Form
2. Upload a Public Key
2. Example Encrypted Message

== Frequently Asked Questions ==

= 1. What is SimpleSecure? =

SimpleSecure is a plugin that adds a secure contact form using GPG to encrypt messages

= 2. What is GPG? =

GPG is an open source implementation of PGP public key encryption.  PGP is a secure
encryption technology that allows people to send secure messages without having to
first share any secret information.  More info is at http://www.gnupg.org/

= 3. Where do I get GPG? =

GPG can be downloaded from http://www.gnupg.org/, however there are user
interfaces for Windows and Mac users at http://www.gpg4win.org/ and https://gpgtools.org/

= 4. How do I get started? =

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

= 0.0.1 =
* initial checkin

== Changelog ==

= 0.0.1 =
* initial checkin