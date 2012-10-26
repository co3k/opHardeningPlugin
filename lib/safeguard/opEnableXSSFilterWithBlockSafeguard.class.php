<?php

/**
 * This file is part of the opHardeningPlugin package.
 * (c) Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

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
