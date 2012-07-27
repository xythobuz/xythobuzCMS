# xythobuzCMS

xythobuzCMS is a PHP CMS, dependent on MySQL, which is used on [this website](http://xythobuz.org).
A special version is served if an iOS Device browses the site. This is using [iWebKit](http://snippetspace.com/).

## Installation

1. Put files on webserver
2. Open setup.php with your browser, enter credentials
3. Delete setup.php

## License

xythobuzCMS is now licensed as [Creative Commons 3.0 CC-BY](http://creativecommons.org/licenses/by/3.0).
If you like it, or are using it, you could drop a [mail](mailto:xythobuz@xythobuz.org) (xythobuz@xythobuz.org).
The flag icons are licensed as CC-BY-SA and come from [flags.blogpotato.de](http://flags.blogpotato.de). If you want flags for other countries, get them there :)

## Example Config

The setup.php script creates a config.php file and fills it with your credentials. It also creates a sample page. These are the values found in the config.php file:

    $sql_host = 'mysql server';
    $sql_username = 'mysql username';
    $sql_password = 'mysql password';
    $sql_database = 'mysql database name';
    
    $xythobuzCMS_root = 'http://example.com';
    $xythobuzCMS_author = 'Your Name';
    $xythobuzCMS_authormail = 'your@mail.com';
    $xythobuzCMS_lang = 'main language code (de, en...)';
    $xythobuzCMS_lang2 = 'alternative language code';
    $xythobuzCMS_title = 'page title';
    $xythobuzCMS_logo = 'logo url';
    $xythobuzCMS_com = 'FALSE';
	// TRUE or FALSE. Sets comment visibility after they are written.
	// if FALSE, you get an email.

    // These are optional:
    $xythobuzCMS_birth = 759193200; // Birthdate as unix timestamp
    $xythobuzCMS_flattr = 'flattr button link'; // DEPRECATED
    $xythobuzCMS_flattrusername = 'flattr username';
    $xythobuzCMS_adcode = "your ad code (html, javascript...)";
    $xythobuzCMS_customFeed = "custom feed url, if you want to use feedburner etc.";
	$xythobuzCMS_twitterNick = "Adds a follow button in sidebar, adds tweet button to posts.";
    $xythobuzCMS_onload = "js code here"; // Put in custom body onload handler.
	$xythobuzCMS_captcha_priv = "private reCaptcha Key.";
	$xythobuzCMS_captcha_pub = "public reCaptcha Key.";
	// recaptchalib.php in this folder needed. Get here:
	// http://code.google.com/p/recaptcha/downloads/list?q=label:phplib-Latest
    $xythobuzCMS_logoLink = "http://www.example.com"; // Link on logo. Root if unset

xythobuzCMS creates an rss feed for your blog entries. It is available in your root folder as "rss.xml". You can change the link to the rss feed if you enter an alternative url in your config / setup.
If you use languages other than 'de' and 'en', put images of their flags into img/flags/ as code.png, so e.g. put ch.png in there, if one of your languages is swiss(ch)...
