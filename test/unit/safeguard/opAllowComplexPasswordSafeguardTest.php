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

sfConfig::set('openpne_member_config', array('password' => array(
  'Info' => '********** THIS VALUE MUST BE REPLACED **********',
  'FormType' => 'password',
  'ValueType' => 'password',
  'IsRegist' => true,
  'IsConfig' => true,
)));

$safeguard = new opAllowComplexPasswordSafeguard($context);
$safeguard->apply(new sfEvent(null, 'whatever.whatever'));

$config = sfConfig::get('openpne_member_config');
$t->isnt($config['password']['Info'], '********** THIS VALUE MUST BE REPLACED **********', 'Help message of the passwrd form field is now replaced');

$form = new MemberConfigPasswordForm();

$validator = $form->getValidator('password');

$t->isnt($validator->getOption('pattern'), '/^[[:graph:]]+$/i', 'Make sure an instance of the MemberConfigPasswordForm has old pattern option');
$t->isnt($validator->getOption('max_length'), null, 'Make sure an instance of the MemberConfigPasswordForm has old max_length option');

$form->bind(array());

$validator = $form->getValidator('password');

$t->is($validator->getOption('pattern'), '/^[[:graph:]]+$/i', 'pattern option has been changed');
$t->is($validator->getOption('max_length'), null, 'max_length option has been changed');
