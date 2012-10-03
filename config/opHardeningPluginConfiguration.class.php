<?php

class opHardeningPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->setDefaultToHttpOnlySessionCookie();

    $this->appendSafeguard('op_action.post_execute', 'disable_content_sniffing');
    $this->appendSafeguard('op_action.post_execute', 'deny_non_same_origin_frame');
    $this->appendSafeguard('op_action.post_execute', 'enable_XSS_filter_with_block');
    $this->appendSafeguard('context.load_factories', 'force_encoding_to_UTF8');
    $this->appendSafeguard('response.filter_content', 'JSON_hijacking_protection');
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
      $this->dispatcher->connect($eventName, array($safeguard, 'apply'));
    }
  }

  protected function getResponse()
  {
    return sfContext::getInstance()->getResponse();
  }

  public function setDefaultToHttpOnlySessionCookie()
  {
    $current = session_get_cookie_params();
    if (empty($current['httponly']))
    {
      session_set_cookie_params($current['lifetime'], $current['path'], $current['domain'], $current['secure'], true /* HttpOnly */);
    }
  }

  public function disableContentSniffing($event)
  {
  }
}
