<?php

$app = 'pc_frontend';

include(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(null, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('pc_frontend', 'test', true);
$context = sfContext::createInstance($configuration);

// --

$response = $context->getResponse();

$t->is($response->getHttpHeader('X-Frame-Options'), '', 'No "X-Frame-Options" HTTP headers are specified');

$safeguard = new opDenyNonSameOriginFrameSafeguard($context);
$safeguard->apply(new sfEvent(new StdClass(), 'whatever.event_name'), array());

$t->is($response->getHttpHeader('X-Frame-Options'), 'SAMEORIGIN', 'A "X-Frame-Options" HTTP header is specified and it is set as "SAMEORIGIN"');

$response->setHttpHeader('X-Frame-Options', 'DUMMY', true);

$t->is($response->getHttpHeader('X-Frame-Options'), 'DUMMY', 'A custom "X-Frame-Options" HTTP header is specified');

$safeguard->apply(new sfEvent(new StdClass(), 'whatever.event_name'), array());

$t->is($response->getHttpHeader('X-Frame-Options'), 'SAMEORIGIN', 'A custom "X-Frame-Options" HTTP header was overwritten');
