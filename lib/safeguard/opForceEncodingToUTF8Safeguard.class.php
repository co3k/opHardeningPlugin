<?php

/**
 * This file is part of the opHardeningPlugin package.
 * (c) Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class opForceEncodingToUTF8Safeguard extends opSafeguard
{
  public function isAvailable()
  {
    return (sfConfig::get('sf_app') !== 'mobile_frontend');
  }

  public function apply(sfEvent $event, $value = null)
  {
    $request = $this->context->getRequest();

    if ($request instanceof opWebRequest)
    {
      $request->convertEncodingForInput('UTF-8');
    }
  }
}
