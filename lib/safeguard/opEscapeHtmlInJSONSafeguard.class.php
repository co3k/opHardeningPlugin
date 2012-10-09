<?php

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
