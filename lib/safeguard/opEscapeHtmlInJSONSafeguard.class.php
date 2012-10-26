<?php

/**
 * This file is part of the opHardeningPlugin package.
 * (c) Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class opEscapeHtmlInJSONSafeguard extends opSafeguard
{
  public function apply(sfEvent $event, $value = null)
  {
    $response = $event->getSubject();
    $content = $value;

    if (false === strpos($response->getContentType(), 'application/json'))
    {
      return $content;
    }

    return $this->escape($content);
  }

  protected function escape($string)
  {
    $search = array('/</u', '/>/u');
    $replace = array('\u003c', '\u003e');

    return preg_replace($search, $replace, $string);
  }
}
