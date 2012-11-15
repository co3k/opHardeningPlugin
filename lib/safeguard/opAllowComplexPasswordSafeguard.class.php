<?php

/**
 * This file is part of the opHardeningPlugin package.
 * (c) Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

class opAllowComplexPasswordSafeguard extends opSafeguard
{
  public function apply(sfEvent $event, $value = null)
  {
    $this->changeHelpMessage();

    $this->context->getEventDispatcher()->connect('form.filter_values', array($this, 'changeValidatorOption'));
  }

  protected function changeHelpMessage()
  {
    $config = sfConfig::get('openpne_member_config');
    $config['password']['Info'] = 'Password must be at least 6 characters long.';

    sfConfig::set('openpne_member_config', $config);
  }

  public function changeValidatorOption($event, $value)
  {
    $form = $event->getSubject();
    if (!($form instanceof MemberConfigPasswordForm))
    {
      return $value;
    }

    $validator = $form->getValidator('password');
    $validator->setOption('pattern', '/^[[:graph:]]+$/i');
    $validator->setOption('max_length', null);

    return $value;
  }
}
