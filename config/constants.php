<?php

define('PRODUCTION', false);

define('PUBLIC_KEY', ''); #Public key of your discord application (you might not rly care if that is leaked)
define('BOT_TOKEN', null); #The token of your bot if you plan to use discord api (you should care if that is leacked :D)
define('CHANNEL_ID', ''); #Channel id where you want your app to post updates

//For logging error with sentry
define('SENTRY_DSN', null);

//To make sentry catch all error
define('SENTRY_ALL', false);

//Ignore request timestamp
//WARNING: DO NOT ENABLE IN PRODUCTION
define('IGNORE_TIME', false);

//Set this to an url to send a copy of the request to a request catcher
//ex: `https://discord-slash.requestcatcher.com/`
//WARNING: THIS MAY IMPACT PERFORMANCES A LOT
//WARNING: DO NOT ENABLE IN PRODUCTION
define('REQUEST_CATCHER_URL', null);
