<?php
declare(strict_types=1);
namespace MRBS\Form;

class ElementInputSearch extends ElementInput
{

  public function __construct()
  {
    parent::__construct();
    $this->setAttribute('type', 'search');
  }

}
