<?php 
namespace Bbctop\Lib\Support\Facade; 
use think\Facade; 

class File extends Facade { 
  protected static function getFacadeClass() { 
    return 'Bbctop\Lib\Support\Helper\File'; 
  } 
}