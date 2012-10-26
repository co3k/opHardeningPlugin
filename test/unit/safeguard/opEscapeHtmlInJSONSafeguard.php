<?php

/**
 * This file is part of the opHardeningPlugin package.
 * (c) Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

$app = 'pc_frontend';

include(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(null, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('pc_frontend', 'test', true);
$context = sfContext::createInstance($configuration);

// ---

$dispatcher = new sfEventDispatcher();

$safeguard = new opEscapeHtmlInJSONSafeguard($context);

$response = new sfWebResponse($dispatcher);
$response->setContentType('application/json');

$result = $safeguard->apply(new sfEvent($response, 'whatever.event_name'), '<script>alert(\"XSS\");</script>');

$t->is($result, '\u003cscript\u003ealert(\"XSS\");\u003c/script\u003e', 'Returns escaped response');
