<?php

abstract class opSafeguard implements opSafeguardInterface
{
  protected $context;

  public function __construct(sfContext $context = null, $configuraitons = array())
  {
    $this->context = $context;

    $this->configure($configuraitons);
  }

  public function getInstance()
  {
    return $this->context;
  }
}
