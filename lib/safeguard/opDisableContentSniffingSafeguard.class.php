<?php

class opDisableContentSniffingSafeguard extends opSafeguard
{
  public function configure($configurations = array())
  {
  }

  public function apply(sfEvent $event)
  {
    $this->context->getResponse()->setHttpHeader('X-Content-Type-Options', 'nosniff', true);
  }
}
