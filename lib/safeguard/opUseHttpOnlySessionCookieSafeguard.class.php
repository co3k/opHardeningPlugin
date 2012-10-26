<?php

/**
 * This file is part of the opHardeningPlugin package.
 * (c) Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class opUseHttpOnlySessionCookieSafeguard extends opSafeguard
{
  public function apply(sfEvent $event, $value = null)
  {
    $current = session_get_cookie_params();
    if (empty($current['httponly']))
    {
      session_set_cookie_params($current['lifetime'], $current['path'], $current['domain'], $current['secure'], true /* HttpOnly */);
    }
  }

}
