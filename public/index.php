<?php
// use SearchInform\DataBase as Db;
// use SearchInform\Test as Test;

function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    require $fileName;
}

spl_autoload_register('autoload'); // 
//require '../SearchInform/DataBase/DbMySql.php';

// $asd = 'вап';
//$task = new Test\Task();

include 'templates/base.html';
