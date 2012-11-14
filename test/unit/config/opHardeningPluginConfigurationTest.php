<?php

/**
 * This file is part of the opHardeningPlugin package.
 * (c) Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

$app = 'pc_frontend';

include(dirname(__FILE__).'/../../bootstrap/functional.php');
include(dirname(__FILE__).'/../../bootstrap/unit.php');

sfConfig::set('op_hardening', array());

$t = new lime_test(null, new lime_output_color());

function getNumberOfListeners($dispatcher)
{
  $results = array();

  $targetListeners = array(
    'op_action.post_execute', 'context.load_factories', 'response.filter_content',
  );

  foreach ($targetListeners as $listener)
  {
    $results[$listener] = count($dispatcher->getListeners($listener));
  }

  return $results;
}

// ------------------------------------
$t->diag('Test for adding safeguards');
// ------------------------------------

sfConfig::set('sf_app', 'pc_frontend');
$configuration = new ProjectConfiguration(__DIR__);
$dispatcher = $configuration->getEventDispatcher();

$pluginConfig = new opHardeningPluginConfiguration($configuration);
$dispatcher->notify(new sfEvent(null, 'context.load_factories'));

$numberOfListeners = getNumberOfListeners($dispatcher);

$t->is($numberOfListeners['op_action.post_execute'], 3, 'In pc_frontend, opHardeningPluginConfiguration adds "3" listeners for "op_action.post_execute" event.');
$t->is($numberOfListeners['response.filter_content'], 2, 'In pc_frontend, opHardeningPluginConfiguration adds "2" listeners for "response.filter_content" event.');

// --

sfConfig::set('sf_app', 'pc_backend');
$configuration = new ProjectConfiguration(__DIR__);
$dispatcher = $configuration->getEventDispatcher();

$pluginConfig = new opHardeningPluginConfiguration($configuration);
$dispatcher->notify(new sfEvent(null, 'context.load_factories'));
$numberOfListeners = getNumberOfListeners($dispatcher);

$t->is($numberOfListeners['op_action.post_execute'], 2, 'In pc_backend, opHardeningPluginConfiguration adds "2" listeners for "op_action.post_execute" event.');
$t->is($numberOfListeners['response.filter_content'], 2, 'In pc_backend, opHardeningPluginConfiguration adds "2" listeners for "response.filter_content" event.');

// --

sfConfig::set('sf_app', 'mobile_frontend');
$configuration = new ProjectConfiguration(__DIR__);
$dispatcher = $configuration->getEventDispatcher();

$pluginConfig = new opHardeningPluginConfiguration($configuration);
$dispatcher->notify(new sfEvent(null, 'context.load_factories'));
$numberOfListeners = getNumberOfListeners($dispatcher);

$t->is($numberOfListeners['op_action.post_execute'], 3, 'In mobile_frontend, opHardeningPluginConfiguration adds "3" listeners for "op_action.post_execute" event.');
$t->is($numberOfListeners['response.filter_content'], 2, 'In mobile_frontend, opHardeningPluginConfiguration adds "2" listeners for "response.filter_content" event.');

// ----------------------------------------------
$t->diag('Test for configuration of safeguards');
// ----------------------------------------------

sfConfig::set('op_hardening_test_config_safeguards', false);

class Test_Config_Safeguards extends opHardeningPluginConfiguration
{
  public function initialize()
  {
  }

  public function callAppendSafeguard($eventName, $safeguardName, sfContext $context = null)
  {
    $this->appendSafeguard($eventName, $safeguardName, $context);
  }
}

class opDummySafeguard extends opSafeguard
{
  public function configure($configurations = array())
  {
  }

  public function apply(sfEvent $event, $value = null)
  {
    sfConfig::set('op_hardening_test_config_safeguards', true);
  }
}

$configuration = new ProjectConfiguration(__DIR__);
$dispatcher = $configuration->getEventDispatcher();
$pluginConfig = new Test_Config_Safeguards($configuration);

sfConfig::set('op_hardening', array(
  'dummy' => array(
    'enabled' => true,
  ),
));

$pluginConfig->callAppendSafeguard(null, 'dummy');

$t->is(sfConfig::get('op_hardening_test_config_safeguards'), true, 'opHardeningPluginConfiguration registers "dummy" as available safeguard');

sfConfig::set('op_hardening_test_config_safeguards', false);
sfConfig::set('op_hardening', array(
  'dummy' => array(
    'enabled' => false,
  ),
));

$pluginConfig->callAppendSafeguard(null, 'dummy');

$t->is(sfConfig::get('op_hardening_test_config_safeguards'), false, 'opHardeningPluginConfiguration does not register "dummy" as available safeguard');

sfConfig::set('op_hardening_test_config_safeguards', false);
sfConfig::set('op_hardening', array(
  'dummy' => array(
    'enabled' => false,
  ),
  'dummy2' => array(
    'enabled' => true,
  ),
));

$pluginConfig->callAppendSafeguard(null, 'dummy');

$t->is(sfConfig::get('op_hardening_test_config_safeguards'), false, 'opHardeningPluginConfiguration does not register "dummy" as available safeguard even if the dummy2 has some configurations');
