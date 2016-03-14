<?php

Config::set('site_name', 'Help4Car');

Config::set('languages', array('en', 'fr'));

Config::set('static_controller_head', 'head');
Config::set('static_controller_footer', 'footer');

Config::set('static_action_head','index');
Config::set('static_action_footer','index');

// Routes. Route name => method prefix
Config::set('routes', array(
    'default' => '',
    'admin'   => 'admin_',
));

Config::set('default_route', 'default');
Config::set('default_language', 'en');
Config::set('default_controller', 'main');
Config::set('default_action', 'index');

Config::set('db.host', 'localhost');
Config::set('db.user', 'root');
Config::set('db.password', '');
Config::set('db.db_name', 'sto_auto');

Config::set('salt', 'jd7sj3sdkd964he7e');