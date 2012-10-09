<?php

class opEnableXSSFilterWithBlockSafeguard extends opSafeguard
{
  public function isAvailable()
  {
    return (sfConfig::get('sf_app') !== 'pc_backend');
  }

  public function apply(sfEvent $event, $value = null)
  {
    $this->context->getResponse()->setHttpHeader('X-XSS-Protection', '1; mode=block', true);
  }
}
