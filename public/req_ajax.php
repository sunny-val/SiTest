<?php
use SearchInform\TestApp as TestApp;

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
    $base_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src';
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

// код запроса
$command = $_REQUEST['command']; // $command = $wh_dbase->escape_string($_REQUEST['command']);
                                 
// дополнительные параметры команды, в json формате (escape_string портит json формат)
$param = $_REQUEST['param'];
if ($param)
    $json_param = json_decode($param, JSON_UNESCAPED_UNICODE);

// формируем массив $json_arr с ответом
switch ($command) {
    // добавить новую задачу
    case add_task:
        if ($json_param) {
            $repo = new TestApp\Repository();
            // $repo . addTask();
        }
        $ret_arr = array(
            'err' => 0,
            'task' => '{"123", 34}'
        );
    default:
        $ret_arr = array(
            'err' => 1,
            'task' => '{}'
        );
}

header('Content-Type: application/json; charset=utf-8');

// $json_arr = json_encode($ret_arr);
// $ret_arr = "{'a', 'jk'}";
// $json_arr = json_encode($ret_arr);
// if (($json_arr = false) != false)
if (($json_arr = json_encode($ret_arr)) != false)
    echo $json_arr;
else
    echo '{"err":1, "task":"{}"}';

// $task = new TestApp\Task();
// $repo->saveTask($task);
