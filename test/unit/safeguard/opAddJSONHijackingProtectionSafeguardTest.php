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

$androidUserAgent = 'Mozilla/5.0 (Linux; U; Android 2.2.1; ja-jp; IS03 Build/S3251) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1';
$ieUserAgent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)';

$_SERVER['USER_AGENT'] = $androidUserAgent;
$context->getRequest()->setMethod(sfWebRequest::GET);

// --

$safeguard = new opJSONHijackingProtectionSafeguard($context);

$response = new sfWebResponse($dispatcher);
$response->setContentType('text/plain');
$response->setContent('DUMMY');

$result = $safeguard->apply(new sfEvent($response, 'whatever.event_name'), $response->getContent());

$t->is($result, 'DUMMY', 'Returns raw response if the content-type is "text/plain" in non-XHR and GET request with Android');

// --

$response->setContentType('application/json');
$result = $safeguard->apply(new sfEvent($response, 'whatever.event_name'), $response->getContent());

$t->is($result, '"Your request is denied. Please retry to request with \"X-Requested-With\" header."', 'Returns error message if the content-type is "application/json" in non-XHR and GET request with Android');

// --

$context->getRequest()->setMethod(sfWebRequest::POST);
$result = $safeguard->apply(new sfEvent($response, 'whatever.event_name'), $response->getContent());

$t->is($result, 'DUMMY', 'Returns raw response if the request method is POST');

// --

$context->getRequest()->setMethod(sfWebRequest::GET);
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$result = $safeguard->apply(new sfEvent($response, 'whatever.event_name'), $response->getContent());

$t->is($result, 'DUMMY', 'Returns raw response if the request with XHR header');

// --

$context->getRequest()->setMethod(sfWebRequest::GET);
$_SERVER['USER_AGENT'] = $ieUserAgent;
$_SERVER['HTTP_X_REQUESTED_WITH'] = '';
$result = $safeguard->apply(new sfEvent($response, 'whatever.event_name'), $response->getContent());

$t->is($result, 'DUMMY', 'Returns raw response if the request with no Android default browser');

// --

$vector = '+ACIAfQA7-var a+AD0AewAi-'; // '"} var a={"' in UTF-7

$response->setContent($vector);
$result = $safeguard->apply(new sfEvent($response, 'whatever.event_name'), $response->getContent());

$t->is($result, '\u002bACIAfQA7-var a\u002bAD0AewAi-', '"+" are escaped to unicode-escape-sequence ("\u002b")');
