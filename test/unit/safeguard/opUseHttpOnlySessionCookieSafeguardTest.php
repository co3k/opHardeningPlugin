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

session_set_cookie_params(1988, '/hardening-plugin', 'hardening.example.com', true, false);

$safeguard = new opUseHttpOnlySessionCookieSafeguard($context);
$safeguard->apply(new sfEvent(null, 'whatever.event_name'));

$params = session_get_cookie_params();

$t->is($params['lifetime'], 1988, '"lifetime" is not overwritten by the safeguard');
$t->is($params['path'], '/hardening-plugin', '"path" is not overwritten by the safeguard');
$t->is($params['domain'], 'hardening.example.com', '"domain" is not overwritten by the safeguard');
$t->is($params['secure'], true, '"secure" is not overwritten by the safeguard');
$t->is($params['httponly'], true, '"httponly" is overwritten by the safeguard. Now session cookies are published with HttpOnly field');
