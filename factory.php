<?php
require_once 'interfases/Segin.php';

/**
 * Шаблон фабрика
 *
 */
/**
 * Class Google
 */
class Google implements Segin
{

    public function whoAmI()
    {
        echo __CLASS__ . '<br>';
    }

    public function login()
    {
        // TODO: Implement login() method.
    }
}

/**
 * Class FB
 */
class FB implements Segin
{

    public function whoAmI()
    {
        echo __CLASS__ . '<br>';
    }

    public function login()
    {
        // TODO: Implement login() method.
    }
}

/**
 * Class Fabric
 * производит выдор класса и создает объект
 */
class Fabric
{
    public static function getInstance($type)
    {
        if ($type === 'Google'){
            return new Google();
        }elseif ($type === 'FB'){
            return new FB();
        }
        return null;

    }
}

$type = (empty($_REQUEST['type']))?'Google':$_REQUEST['type'];

$auth = Fabric::getInstance($type);
if (!$auth){
    die('ERROR');
}
$auth->whoAmI();
$auth->login();


