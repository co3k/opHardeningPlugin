<?php

$app = 'pc_frontend';

include(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(null, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('pc_frontend', 'test', true);
$context = sfContext::createInstance($configuration);

// --

$response = $context->getResponse();

$t->is($response->getHttpHeader('X-Content-Type-Options'), '', 'No "X-Content-Type-Options" HTTP headers are specified');

$safeguard = new opDisableContentSniffingSafeguard($context);
$safeguard->apply(new sfEvent(new StdClass(), 'whatever.event_name'), array());

$t->is($response->getHttpHeader('X-Content-Type-Options'), 'nosniff', 'A "X-Content-Type-Options" HTTP header is specified and it is set as "nosniff"');

$response->setHttpHeader('X-Content-Type-Options', 'DUMMY', true);

$t->is($response->getHttpHeader('X-Content-Type-Options'), 'DUMMY', 'A custom "X-Content-Type-Options" HTTP header is specified');

$safeguard->apply(new sfEvent(new StdClass(), 'whatever.event_name'), array());

$t->is($response->getHttpHeader('X-Content-Type-Options'), 'nosniff', 'A custom "X-Content-Type-Options" HTTP header was overwritten');
