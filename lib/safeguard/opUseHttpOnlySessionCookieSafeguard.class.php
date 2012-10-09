<?php

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
