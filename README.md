# xythobuzCMS

xythobuzCMS is a PHP CMS, dependent on MySQL, which is used on [this website](http://xythobuz.org).
It supports a piwik installation in /piwik/ and can display the Usercount for today. Just add the tracking code as code in the administration menu.

## Installation

1. Put files on webserver
2. Open setup.php with your browser, enter credentials
3. Delete setup.php

## License

xythobuzCMS is in the Public Domain!
If you like it, or are using it, you could drop a [mail](mailto:taucher.bodensee@gmail.com) (taucher.bodensee@gmail.com).
The flag icons are licensed as CC-BY-SA and come from [flags.blogpotato.de](http://flags.blogpotato.de)

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
    $xythobuzCMS_com = 'comments visible?';
	// TRUE or FALSE. Sets comment visibility after they are written.
	// if FALSE, you get an email.

    // These are optional:
    $xythobuzCMS_birth = 759193200; // Birthdate as unix timestamp
    $xythobuzCMS_flattr = 'flattr link';
    $xythobuzCMS_piwiktoken = 'piwik api token';
    $xythobuzCMS_adcode = "your ad code (html, javascript...)";
    $xythobuzCMS_customFeed = "custom feed url, if you want to use feedburner etc.";
	$xythobuzCMS_twitterNick = "Adds a follow button in sidebar, adds tweet button to posts.";
    $xythobuzCMS_onload = "js code here"; // Put in custom body onload handler.
	$xythobuzCMS_captcha_priv = "private reCaptcha Key.";
	$xythobuzCMS_captcha_pub = "public reCaptcha Key.";
	// recaptchalib.php in this folder needed. Get here:
	// http://code.google.com/p/recaptcha/downloads/list?q=label:phplib-Latest

xythobuzCMS creates an rss feed for your blog entries. It is available in your root folder as "rss.xml". You can change the link to the rss feed if you enter an alternative url in your config / setup.
If you use languages other than 'de' and 'en', put images of their flags into img/flags/ as code.png, so e.g. put ch.png in there, if one of your languages is swiss(ch)...
