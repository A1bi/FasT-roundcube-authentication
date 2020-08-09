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
  private $rc;

  function init()
  {
    $this->rc = rcube::get_instance();

    $this->load_config();

    $this->add_texts('i18n/');

    $this->add_hook('authenticate', array($this, 'authenticate'));
    $this->add_hook('template_object_loginform', array($this, 'append_fast_auth_link'));
  }

  function authenticate($params)
  {
    if ($this->get_auth_code() == 'foo') {
      $params['user'] = 'foobar@a0s.de';
      $params['pass'] = 'foobar';
    }

    return $params;
  }

  function append_fast_auth_link($params) {
    $url = $this->rc->config->get('fast_authentication_authcode_url');

    if (isset($url)) {
      $params['content'] .=
        html::p('my-5',
          html::a(
            array(
              'href' => $url,
              'class' => 'btn btn-lg btn-primary w-100'
            ),
            rcube::Q($this->gettext('auth_link_text'))
          )
        );
    }

    return $params;
  }

  private function get_auth_code()
  {
    return rcube_utils::get_input_value('fast_auth_code', rcube_utils::INPUT_GET);
  }
}
