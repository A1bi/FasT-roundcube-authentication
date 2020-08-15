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
  public $task = 'login|logout';
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
    if ($token = $this->get_token()) {
      if ($credentials = $this->fetch_credentials($token)) {
        $params['user'] = $credentials['email'];
        $params['pass'] = $credentials['password'];
      } else {
        $params['abort'] = true;
      }
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

  private function fetch_credentials($token)
  {
    $ch = curl_init();

    $api_token = $this->rc->config->get('fast_authentication_credentials_api_token');
    $api_url = $this->rc->config->get('fast_authentication_credentials_api_url');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "{$api_url}/{$token}");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Authorization: Token ' . $api_token
    ]);

    $content = curl_exec($ch);
    $okay = !curl_errno($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200;
    curl_close($ch);

    if ($okay) {
      return json_decode($content, true);
    } else {
      return false;
    }
  }

  private function get_token()
  {
    return rcube_utils::get_input_value('shared_email_account_token', rcube_utils::INPUT_GET);
  }
}
