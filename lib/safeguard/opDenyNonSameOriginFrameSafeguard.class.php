<?php

class opDenyNonSameOriginFrameSafeguard extends opSafeguard
{
  public function apply(sfEvent $event, $value = null)
  {
    $this->context->getResponse()->setHttpHeader('X-Frame-Options', 'SAMEORIGIN', true);
  }
}
