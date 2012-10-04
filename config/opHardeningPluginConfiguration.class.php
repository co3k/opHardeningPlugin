<?php

class opHardeningPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->appendSafeguard(null, 'use_http_only_session_cookie');
    $this->appendSafeguard('op_action.post_execute', 'disable_content_sniffing');
    $this->appendSafeguard('op_action.post_execute', 'deny_non_same_origin_frame');
    $this->appendSafeguard('op_action.post_execute', 'enable_XSS_filter_with_block');
    $this->appendSafeguard('context.load_factories', 'force_encoding_to_UTF8');
    $this->appendSafeguard('response.filter_content', 'JSON_hijacking_protection');
    $this->appendSafeguard('response.filter_content', 'escape_html_in_JSON');
  }

  protected function appendSafeguard($eventName, $safeguardName, sfContext $context = null)
  {
    if (!$context && sfContext::hasInstance())
    {
      $context = sfContext::getInstance();
    }

    $className = 'op'.sfInflector::camelize($safeguardName).'Safeguard';

    $safeguard = new $className($context, sfConfig::getAll());
    if ($safeguard->isAvailable())
    {
      if ($eventName)
      {
        $this->dispatcher->connect($eventName, array($safeguard, 'apply'));
      }
      else
      {
        $safeguard->apply(new sfEvent(null, ''));
      }
    }
  }
}
