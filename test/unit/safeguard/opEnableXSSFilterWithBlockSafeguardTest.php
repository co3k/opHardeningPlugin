<?php

$app = 'pc_frontend';

include(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(null, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('pc_frontend', 'test', true);
$context = sfContext::createInstance($configuration);

// --

$t->diag('->apply()');

$response = $context->getResponse();

$t->is($response->getHttpHeader('X-XSS-Protection'), '', 'No "X-XSS-Protection" HTTP headers are specified');

$safeguard = new opEnableXSSFilterWithBlockSafeguard($context);
$safeguard->apply(new sfEvent(new StdClass(), 'whatever.event_name'), array());

$t->is($response->getHttpHeader('X-XSS-Protection'), '1; mode=block', 'A "X-XSS-Protection" HTTP header is specified and it is set as "1; mode=block"');

$response->setHttpHeader('X-XSS-Protection', '0', true);

$t->is($response->getHttpHeader('X-XSS-Protection'), '0', 'A custom "X-Frame-Options" HTTP header is specified');

$safeguard->apply(new sfEvent(new StdClass(), 'whatever.event_name'), array());

$t->is($response->getHttpHeader('X-XSS-Protection'), '1; mode=block', 'A custom "X-XSS-Protection" HTTP header was overwritten');

$t->diag('->isAvailable()');

sfConfig::set('sf_app', 'pc_frontend');
$t->is($safeguard->isAvailable(), true, 'This safeguard is available in pc_frontend');
sfConfig::set('sf_app', 'pc_backend');
$t->is($safeguard->isAvailable(), false, 'This safeguard is not available in pc_backend');
