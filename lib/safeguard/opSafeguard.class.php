<?php

abstract class opSafeguard implements opSafeguardInterface
{
  protected $context, $config;

  public function __construct(sfContext $context = null, $configuraitons = array())
  {
    $this->context = $context;

    $this->config = new sfParameterHolder();
    $this->config->add($configuraitons);

    $this->configure($configuraitons);
  }

  public function configure($configurations = array())
  {
    // You can add your additional configuration handlings
  }

  public function isAvailable()
  {
    return true;
  }

  public function getInstance()
  {
    return $this->context;
  }

  public function getConfig()
  {
    return $this->config;
  }
}
