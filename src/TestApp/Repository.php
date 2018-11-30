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
        // $err = $db->getErrorStr();
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
     * @return boolean
     */
    public function saveTask($task)
    {
        $err = $this->db->getErrorStr();
        if (empty($err)) {
            $arr = array(
                
                "uuid" => $task->uuid,
                "name" => $task->name,
                "tags" => $task->tags,
                "priority" => $task->priority,
                "status" => $task->status
            );
            return $this->db->insert_data($this->db->task_table, $arr);
        }
        return false;
    }

    /**
     * Получение описания ошибки, если null - нет ошибки
     *
     * @return string|NULL
     */
    public function getLastError()
    {
        return $this->db->getErrorStr();
    }

    /**
     * Прочитать все задачи из БД
     *
     * @return array(Task)
     */
    function readAllTasks()
    {
        
        // TODO - Insert your code here
    }
}

