<?php
use SearchInform\TestApp\Repository;
use SearchInform\TestApp\Task;
use SearchInform\TestApp\TaskPriority;
use SearchInform\TestApp\TaskStatus;

// define('DEBUG', true);
define('DEBUG', false);

/**
 * A project implementation.
 *
 * @param string $class
 *            The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    
    // project namespace prefix
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

if (! DEBUG)
    $buffering = true;

// включить буферизацию вывода чтобы сообщения об ошибках не появились в ответе
if ($buffering) {
    ob_start();
    ob_clean();
}

// код запроса
$command = $_REQUEST['command'];

// дополнительные параметры команды, в json формате (escape_string портит json формат)
$param = $_REQUEST['param'];
if ($param)
    $json_param = json_decode($param, JSON_UNESCAPED_UNICODE);

// формируем массив $json_arr с ответом
switch ($command) {
    // получить список всех задач
    case get_all_tasks:
        $repo = new Repository();
        $all_tasks = $repo->readAllTasks();
        if (! $all_tasks) {
            $err_txt = $repo->getLastError();
        }
        $ret_arr = array(
            'err' => $err_txt,
            'task' => $all_tasks
        );
        break;
    // добавить новую задачу
    case add_task:
        $err = null;
        if ($json_param) {
            $new_task = new Task();
            if ($new_task->fill($json_param['name'], $json_param['prio'], $json_param['status'], $json_param['tags'])) {
                $repo = new Repository();
                // если значения прошли фильтрацию, добавляем их в БД
                if (! $repo->saveNewTask($new_task)) {
                    $err = 1;
                    $err_txt = $repo->getLastError();
                }
            } else {
                $err_txt = _("Invalid task parameters");
            }
        } else {
            $err_txt = _("Invalid task parameters");
        }
        $ret_arr = array(
            'err' => $err_txt,
            'task' => array(
                'uuid' => (string) $new_task->uuid
            )
        );
        break;
    
    // редактировать существующую задачу
    case edit_task:
        $err = null;
        if ($json_param) {
            $present_task = new Task();
            if ($present_task->fill($json_param['name'], $json_param['prio'], $json_param['status'], $json_param['tags'], $json_param['uuid'])) {
                $repo = new Repository();
                // если значения прошли фильтрацию, изменяем их в БД
                if (! $repo->editTask($present_task)) {
                    $err = 1;
                    $err_txt = $repo->getLastError();
                }
            } else {
                $err_txt = _("Invalid task parameters");
            }
        } else {
            $err_txt = _("Invalid task parameters");
        }
        $ret_arr = array(
            'err' => $err_txt,
            'task' => array(
                'uuid' => (string) $new_task->uuid
            )
        );
        
        break;
    
    // удалить задачу
    case del_task:
        $err = null;
        if ($json_param) {
            $present_task = new Task();
            if ($present_task->fill('name', TaskPriority::PRIORITY_MEDIUM, TaskStatus::STATUS_WORK, '', $json_param['uuid'])) {
                $repo = new Repository();
                if (! $repo->delTask($present_task)) {
                    $err = 1;
                    $err_txt = $repo->getLastError();
                }
            } else {
                $err_txt = _("Invalid task parameters");
            }
        } else {
            $err_txt = _("Invalid task parameters");
        }
        $ret_arr = array(
            'err' => $err_txt,
            'task' => array(
                'uuid' => (string) $new_task->uuid
            )
        );
        
        break;
    
    default:
        $ret_arr = array(
            'err' => _('unknown command'),
            'task' => '{}'
        );
}

// очистка буфера вывода и отключение буферизации, чтобы всякие сообщения об ошибках не выводились
if ($buffering)
    ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

if (($json_arr = json_encode($ret_arr)) != false)
    echo $json_arr;
else
    echo '{"err":"unknown error", "task":"{}"}';

