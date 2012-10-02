<?php

class opHardeningPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->setDefaultToHttpOnlySessionCookie();

    $this->dispatcher->connect('op_action.post_execute', array($this, 'disableContentSniffing'));
    $this->dispatcher->connect('op_action.post_execute', array($this, 'allowSameOriginFrame'));

    if (sfConfig::get('sf_app') !== 'pc_backend')
    {
      $this->dispatcher->connect('op_action.post_execute', array($this, 'enableXSSFilterWithBlock'));
    }

    if (sfConfig::get('sf_app') !== 'mobile_frontend')
    {
      $this->dispatcher->connect('context.load_factories', array($this, 'forceEncodingToUTF8'));
    }

    $this->dispatcher->connect('response.filter_content', array($this, 'addJSONHijackingProtection'));
  }

  protected function getResponse()
  {
    return sfContext::getInstance()->getResponse();
  }

  public function setDefaultToHttpOnlySessionCookie()
  {
    $current = session_get_cookie_params();
    if (empty($current['httponly']))
    {
      session_set_cookie_params($current['lifetime'], $current['path'], $current['domain'], $current['secure'], true /* HttpOnly */);
    }
  }

  public function disableContentSniffing($event)
  {
    $this->getResponse()->setHttpHeader('X-Content-Type-Options', 'nosniff', true);
  }

  public function allowSameOriginFrame($event)
  {
    $this->getResponse()->setHttpHeader('X-Frame-Options', 'SAMEORIGIN', true);
  }

  public function enableXSSFilterWithBlock($event)
  {
    $this->getResponse()->setHttpHeader('X-XSS-Protection', '1; mode=block', true);
  }

  public function forceEncodingToUTF8($event)
  {
    $request = $event->getSubject()->getRequest();

    if ($request instanceof opWebRequest)
    {
      $request->convertEncodingForInput('UTF-8');
    }
  }

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
  public function addJSONHijackingProtection($event, $content)
  {
    $response = $event->getSubject();

    if (false === strpos($response->getContentType(), 'application/json'))
    {
      return $content;
    }

    if (!sfContext::hasInstance())
    {
      return $content;
    }

    if ($this->needToProtectFromJSONHijack(sfContext::getInstance()->getRequest()))
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
