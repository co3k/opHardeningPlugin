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
 * Logic of this method is based on Amon2::Plugin::Web::JSON
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

  public function needToProtectFromJSONHijack($request)
  {
    $userAgent = $request->getHttpHeader('User-Agent');

    return (!$request->isXmlHttpRequest() && stripos($userAgent, 'android') && $request->getMethod() === sfRequest::GET);
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
