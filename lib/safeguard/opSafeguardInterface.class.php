<?php

interface opSafeguardInterface
{
  function configure($configurations = array());

  function apply(sfEvent $event);
}
