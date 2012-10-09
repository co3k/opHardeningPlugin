<?php

class opDisableContentSniffingSafeguard extends opSafeguard
{
  public function apply(sfEvent $event, $value = null)
  {
    $this->context->getResponse()->setHttpHeader('X-Content-Type-Options', 'nosniff', true);
  }
}
