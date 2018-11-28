<?php
use SearchInform\TestApp as TestApp;
use SearchInform\DataBase as Db;

// function autoload($className)
// {
// $className = ltrim($className, '\\');
// $fileName = '';
// $namespace = '';
// if ($lastNsPos = strrpos($className, '\\')) {
// $namespace = substr($className, 0, $lastNsPos);
// $className = substr($className, $lastNsPos + 1);
// $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
// }
// $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
// require $fileName;
// }

// function autoload($className)
// {

// // project-specific namespace prefix
// $prefix = 'SearchInform';

// // base directory for the namespace prefix
// $base_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src'; // . DIRECTORY_SEPARATOR;

// // does the class use the namespace prefix?
// $len = strlen($prefix);
// if (strncmp($prefix, $className, $len) !== 0) {
// // no, move to the next registered autoloader
// return;
// }

// // get the relative class name
// $relative_class = substr($className, $len);

// // replace the namespace prefix with the base directory, replace namespace
// // separators with directory separators in the relative class name, append
// // with .php
// $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';

// // if the file exists, require it
// if (file_exists($file)) {
// require $file;
// }
// };

/**
 * A project-specific implementation.
 *
 * @param string $class
 *            The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    
    // project-specific namespace prefix
    $prefix = 'SearchInform';
    
    // base directory for the namespace prefix
    $base_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src'; // . DIRECTORY_SEPARATOR;
                                                                                    
    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }
    
    // get the relative class name
    $relative_class = substr($class, $len);
    
    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', DIRECTORY_SEPARATOR, $relative_class) . '.php';
    
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// spl_autoload_register('autoload');

// код запроса
$command = $_REQUEST['command'];
// $command = $wh_dbase->escape_string($_REQUEST['command']);
// дополнительные параметры команды, в json формате (escape_string портит json формат)
$param = $_REQUEST['param'];
$json_param = json_decode($param);

$repo = new TestApp\Repository();

$task = new TestApp\Task();
$repo->saveTask($task);