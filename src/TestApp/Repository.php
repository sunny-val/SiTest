<?php
namespace SearchInform\TestApp;

use SearchInform\DataBase as Db;
use Exception;

/**
 *
 * @author Aleksandr
 *        
 */
class Repository
{

    public $db;
    /**
     */
    public function __construct()
    {
        $this->db = new Db\DbMySql();
//         $err = $db->getErrorStr();
    }

    /**
     */
    function __destruct()
    {
        
        // TODO - Insert your code here
    }

    /**
     * Сохранить в БД задачу
     *
     * @param Task $task
     */
    public function saveTask($task)
    {
        $task->fillTask('павлин');
        $db = new Db\DbMySql();
        $err = $db->getErrorStr();
    }

    /**
     * Прочитать все задачи из БД
     * * @return array(Task)
     */
    function readAllTasks()
    {
        
        // TODO - Insert your code here
    }
}

