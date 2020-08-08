<?php

/**
 * FasT Authentication
 *
 * Authenticates users through a FasT user account and fetches corresponding email credentials
 *
 * @version 0.1
 * @author Albrecht Oster
 */

class fast_authentication extends rcube_plugin
{
  public $task = 'login';

  function init()
  {
    $this->load_config();

    $rcmail = rcmail::get_instance();
    $config = $rcmail->config;

    $this->add_hook('authenticate', array($this, 'authenticate'));
  }

  function authenticate($params)
  {
    if ($this->get_auth_code() == 'foo') {
      $params['user'] = 'foobar@a0s.de';
      $params['pass'] = 'foobar';
    }

    return $params;
  }

  private function get_auth_code()
  {
    return rcube_utils::get_input_value('fast_auth_code', rcube_utils::INPUT_GET);
  }
}
