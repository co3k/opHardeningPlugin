<?php

class opForceEncodingToUTF8Safeguard extends opSafeguard
{
  public function isAvailable()
  {
    return (sfConfig::get('sf_app') !== 'mobile_frontend');
  }

  public function apply(sfEvent $event, $value = null)
  {
    $request = $event->getSubject()->getRequest();

    if ($request instanceof opWebRequest)
    {
      $request->convertEncodingForInput('UTF-8');
    }
  }
}
