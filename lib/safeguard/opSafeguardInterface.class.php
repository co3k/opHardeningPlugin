<?php

/**
 * This file is part of the opHardeningPlugin package.
 * (c) Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

interface opSafeguardInterface
{
  function configure($configurations = array());

  function isAvailable();

  function apply(sfEvent $event, $value = null);

  function getConfig();
}
