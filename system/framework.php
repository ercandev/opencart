<?php
// Autoloader
$autoloader = new \Opencart\System\Engine\Autoloader();
$autoloader->register('Opencart\\' . ucwords($application_config), DIR_APPLICATION);
$autoloader->register('Opencart\System', DIR_SYSTEM);

// Registry
$registry = new \Opencart\System\Engine\Registry();
$registry->set('autoloader', $autoloader);

// Config
$config = new \Opencart\System\Engine\Config();
$config->load('default');
$config->load($application_config);
$registry->set('config', $config);

$config->set('application', ucwords($application_config));

// Event
$event = new \Opencart\System\Engine\Event($registry);
$registry->set('event', $event);

// Event Register
if ($config->has('action_event')) {
	foreach ($config->get('action_event') as $key => $values) {
	  foreach ($values as $value) {
	   $event->register($key, new \Opencart\System\Engine\Action($value));
	  }
	}
}

// Loader
$loader = new \Opencart\System\Engine\Loader($registry);
$registry->set('load', $loader);

// Request
$request = new \Opencart\System\Library\Request();
$registry->set('request', $request);

// Response
$response = new \Opencart\System\Library\Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$registry->set('response', $response);

// Database
if ($config->get('db_autostart')) {
  $db = new \Opencart\System\Library\DB($config->get('db_type'), $config->get('db_hostname'), $config->get('db_username'), $config->get('db_password'), $config->get('db_database'), $config->get('db_port'));
  $registry->set('db', $db);
}

// Session
$session = new \Opencart\System\Library\Session($config->get('session_engine'), $registry);

if ($config->get('session_autostart')) {
  if (isset($request->cookie[$config->get('session_name')])) {
    $session_id = $request->cookie[$config->get('session_name')];
  } else {
    $session_id = '';
  }
  
  
	$session->start('default', $session_id);
	
	// Require higher security for session cookies
	$option = [
	    'expires'  => 0,
	    'path'     => $config->get('session_path'),
	    'domain'   => $config->get('session_domain'),
	    'secure'   => $request->server['HTTPS'],
	    'httponly' => false,
	    'SameSite' => $config->get('session_samesite')
	];
	
	setcookie($config->get('session_name'), $session->getId(), $option);
}

$registry->set('session', $session);

// Cache
$registry->set('cache', new \Opencart\System\Library\Cache($config->get('cache_type'), $config->get('cache_expire')));


// Url
if ($config->get('url_autostart')) {
  $registry->set('url', new \Opencart\System\Library\Url($config->get('site_base'), $config->get('site_ssl')));
}

// Language
$language = new \Opencart\System\Library\Language($config->get('language_default'));
$language->load($config->get('language_default'));
$registry->set('language', $language);

// Document
$registry->set('document', new \Opencart\System\Library\Document());

// Config Autoload
if ($config->has('config_autoload')) {
	foreach ($config->get('config_autoload') as $value) {
		$loader->config($value);
	}
}

// Language Autoload
if ($config->has('language_autoload')) {
	foreach ($config->get('language_autoload') as $value) {
		$loader->language($value);
	}
}

// Library Autoload
if ($config->has('library_autoload')) {
	foreach ($config->get('library_autoload') as $value) {
		$loader->library($value);
	}
}

// Model Autoload
if ($config->has('model_autoload')) {
	foreach ($config->get('model_autoload') as $value) {
		$loader->model($value);
	}
}

// Front Controller
$controller = new \Opencart\System\Engine\Front($registry);

// Pre Actions
if ($config->has('action_pre_action')) {
	foreach ($config->get('action_pre_action') as $value) {
	  $controller->addPreAction(new \Opencart\System\Engine\Action($value));
	}
}

// Dispatch
$controller->dispatch(new \Opencart\System\Engine\Action($config->get('action_router')), new \Opencart\System\Engine\Action($config->get('action_error')));

// Output
$response->setCompression($config->get('config_compression'));
$response->output();
