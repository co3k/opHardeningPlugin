<?php

interface opSafeguardInterface
{
  function configure($configurations = array());

  function isAvailable();

  function apply(sfEvent $event, $value = null);
}
