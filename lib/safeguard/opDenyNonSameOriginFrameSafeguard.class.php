<?php

class opDenyNonSameOriginFrameSafeguard extends opSafeguard
{
  public function configure($configurations = array())
  {
  }

  public function apply(sfEvent $event)
  {
    $this->context->getResponse()->setHttpHeader('X-Frame-Options', 'SAMEORIGIN', true);
  }
}
