<?php

/**
 * This file is part of the opHardeningPlugin package.
 * (c) Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * Add protection from JSON Hijacking attack
 *
 * The original logic of this method is based on Amon2::Plugin::Web::JSON
 * implementation by Tokuhiro Matsuno.
 *
 * You can get Amon2 from https://github.com/tokuhirom/Amon/
 *
 * Amon2 is licensed by http://dev.perl.org/licenses/ but any
 * code of this method are not from Amon2.
 */
class opJSONHijackingProtectionSafeguard extends opSafeguard
{
  public function apply(sfEvent $event, $value = null)
  {
    $response = $event->getSubject();
    $content = $value;

    if (false === strpos($response->getContentType(), 'application/json'))
    {
      return $content;
    }

    if (!$this->context)
    {
      return $content;
    }

    if ($this->needToProtectFromJSONHijack($this->context->getRequest()))
    {
      $content = json_encode('Your request is denied. Please retry to request with "X-Requested-With" header.');
    }

    return $this->escapeToAvoidJSONHijackWithUTF7($content);
  }

  /**
   * Check whether the browser environment needs protection for JSON Hijacking attack or not
   *
   * If ALL OF THE FOLLOWING CONDITIONS are true, this protection will be enabled
   *
   * - The request does NOT contain "X-Requested-With: XMLHttpRequest" header
   * - The request is GET method
   * - The user-agent value contains "android", "trident/5." or "trident 6." (case-insensitive)
   *
   * The followings are reasons why target user-agents are limited:
   *
   * - [1] Old versions of Android default browser allows a type of JSON Hijacking attack (using __defineSetter__)
   * - [2] Microsoft Internet Explorer 9 and 10 allows a type of JSON Hijacking attack (using VB Script Error Handler)
   *
   *    See also:
   *    - [1] : http://www.thespanner.co.uk/2011/05/30/json-hijacking/
   *    - [2] : http://d.hatena.ne.jp/hasegawayosuke/20130517/p1 [in Japanese]
   */
  public function needToProtectFromJSONHijack($request)
  {
    if ($request->isXmlHttpRequest())
    {
      return false;
    }

    if ($request->getMethod() !== sfRequest::GET)
    {
      return false;
    }

    return (bool)preg_match('#(?:android|trident/[56]\.)#i', $request->getHttpHeader('User-Agent'));
  }

  /**
   * Escape "+" to avoid JSON Hijacking Attack with UTF-7
   *
   * See: https://www.blackhat.com/presentations/bh-jp-08/bh-jp-08-Hasegawa/BlackHat-japan-08-Hasegawa-Char-Encoding.pdf
   */
  protected function escapeToAvoidJSONHijackWithUTF7($string)
  {
    return str_replace('+', '\u002b', $string);
  }
}
