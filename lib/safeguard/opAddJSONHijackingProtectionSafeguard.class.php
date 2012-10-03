<?php

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
  public function configure($configurations = array())
  {
  }

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

    return $content;
  }

  public function needToProtectFromJSONHijack($request)
  {
    $pathArray = $request->getPathInfoArray();
    $userAgent = isset($pathArray['USER_AGENT']) ? $pathArray['USER_AGENT'] : '';

    return (!$request->isXmlHttpRequest() && stripos($userAgent, 'android') && $request->getMethod() === sfRequest::GET);
  }
}
