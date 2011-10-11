# xythobuzCMS

xythobuzCMS is a PHP CMS, dependent on MySQL, which is used on [this website](http://xythobuz.org).

## Installation

1. Put files on webserver
2. Open setup.php with your browser, enter credentials
3. Delete setup.php

## License

xythobuzCMS is in the Public Domain!
If you like it, or are using it, you could drop a [mail](mailto:taucher.bodensee@gmail.com) (taucher.bodensee@gmail.com).

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
    
    // These are optional:
    $xythobuzCMS_birth = 759193200; // Birthdate as unix timestamp
    $xythobuzCMS_flattr = 'flattr link';
    $xythobuzCMS_piwiktoken = 'piwik api token';
    $xythobuzCMS_adcode = "your ad code (html, javascript...)";
    $xythobuzCMS_customFeed = "custom feed url, if you want to use feedburner etc.";
	$xythobuzCMS_twitterNick = "Adds a follow button in sidebar, adds tweet button to posts."

xythobuzCMS creates an rss feed for your blog entries. It is available in your root folder as "rss.xml". You can change the link to the rss feed if you enter an alternative url in your config / setup.
