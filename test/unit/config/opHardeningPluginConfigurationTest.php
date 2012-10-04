<?php

$app = 'pc_frontend';

include(dirname(__FILE__).'/../../bootstrap/functional.php');
include(dirname(__FILE__).'/../../bootstrap/unit.php');

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

// --

sfConfig::set('sf_app', 'pc_frontend');
$configuration = new ProjectConfiguration(__DIR__);
$dispatcher = $configuration->getEventDispatcher();

$pluginConfig = new opHardeningPluginConfiguration($configuration);
$numberOfListeners = getNumberOfListeners($dispatcher);

$t->is($numberOfListeners['op_action.post_execute'], 3, 'In pc_frontend, opHardeningPluginConfiguration adds "3" listeners for "op_action.post_execute" event.');
$t->is($numberOfListeners['context.load_factories'], 1, 'In pc_frontend, opHardeningPluginConfiguration adds "1" listeners for "context.load_factories" event.');
$t->is($numberOfListeners['response.filter_content'], 2, 'In pc_frontend, opHardeningPluginConfiguration adds "2" listeners for "response.filter_content" event.');

// --

sfConfig::set('sf_app', 'pc_backend');
$configuration = new ProjectConfiguration(__DIR__);
$dispatcher = $configuration->getEventDispatcher();

$pluginConfig = new opHardeningPluginConfiguration($configuration);
$numberOfListeners = getNumberOfListeners($dispatcher);

$t->is($numberOfListeners['op_action.post_execute'], 2, 'In pc_backend, opHardeningPluginConfiguration adds "2" listeners for "op_action.post_execute" event.');
$t->is($numberOfListeners['context.load_factories'], 1, 'In pc_backend, opHardeningPluginConfiguration adds "1" listeners for "context.load_factories" event.');
$t->is($numberOfListeners['response.filter_content'], 2, 'In pc_backend, opHardeningPluginConfiguration adds "2" listeners for "response.filter_content" event.');

// --

sfConfig::set('sf_app', 'mobile_frontend');
$configuration = new ProjectConfiguration(__DIR__);
$dispatcher = $configuration->getEventDispatcher();

$pluginConfig = new opHardeningPluginConfiguration($configuration);
$numberOfListeners = getNumberOfListeners($dispatcher);

$t->is($numberOfListeners['op_action.post_execute'], 3, 'In mobile_frontend, opHardeningPluginConfiguration adds "3" listeners for "op_action.post_execute" event.');
$t->is($numberOfListeners['context.load_factories'], 0, 'In mobile_frontend, opHardeningPluginConfiguration adds "0" listeners for "context.load_factories" event.');
$t->is($numberOfListeners['response.filter_content'], 2, 'In mobile_frontend, opHardeningPluginConfiguration adds "2" listeners for "response.filter_content" event.');
