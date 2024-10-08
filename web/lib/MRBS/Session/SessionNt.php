<?php
declare(strict_types=1);
namespace MRBS\Session;

use MRBS\User;
use function MRBS\auth;

/*
 * Session management scheme that uses Windows NT domain users and Internet
 * Information Server as the source for user authentication.
 *
 * To use this authentication scheme set the following
 * things in config.inc.php:
 *
 *      $auth['type']    = 'none';
 *      $auth['session'] = 'nt';
 *
 * Then, you may configure admin users:
 *
 * $auth['admin'][] = 'nt_username1';
 * $auth['admin'][] = 'nt_username2';
 *
 * See AUTHENTICATION  for more information.
 */


class SessionNt extends Session
{

  // For this scheme no need to prompt for a name - NT User always there.
  public function getCurrentUser() : ?User
  {
    return auth()->getUser(get_current_user()) ?? parent::getCurrentUser();
  }

}
