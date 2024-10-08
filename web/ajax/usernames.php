<?php
declare(strict_types=1);
namespace MRBS;

// Returns an object containing all the usernames available for use by the Select2
// tool on the edit_entry page.

use MRBS\Form\Form;

require '../defaultincludes.inc';

// Check the CSRF token
Form::checkToken();

// Check the user is authorised for this page
checkAuthorised(this_page());

// Check that the user has a legitimate reason for accessing this page
if (!can_register_others() && !is_book_admin())
{
  exit;
}

$result = array();

if (method_exists(auth(), 'getUsernames'))
{
  try
  {
    $result = auth()->getUsernames();
  }
  catch (\Exception $e)
  {
    trigger_error($e->getMessage(), E_USER_WARNING);
  }
}

http_headers(array("Content-Type: application/json"));

echo json_encode($result);
