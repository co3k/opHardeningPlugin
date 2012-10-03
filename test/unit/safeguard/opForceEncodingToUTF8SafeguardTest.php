<?php

include(dirname(__FILE__).'/../../bootstrap/unit.php');

class MyClass
{
  public $request;

  public function __construct($request)
  {
    $this->request = $request;
  }

  public function getRequest()
  {
    return $this->request;
  }
}

$t = new lime_test(null, new lime_output_color());

$configuration = ProjectConfiguration::getApplicationConfiguration('pc_frontend', 'test', true);
$context = sfContext::createInstance($configuration);

$_POST['example'] = "e\xf0xampl\xe0e";
$request = new opWebRequest(new sfEventDispatcher());

// 

$params = $request->getPostParameters(false);
$t->is($params['example'], "e\xf0xampl\xe0e", 'The "example" POST parameter contains a character which is invalid as UTF-8');

$safeguard = new opForceEncodingToUTF8Safeguard($context);
$safeguard->apply(new sfEvent(new MyClass($request), 'whatever.event_name'), array());

$params = $request->getPostParameters(false);
$t->is($params['example'], 'example', 'The "example" POST parameter are constructed by only UTF-8 characters');
