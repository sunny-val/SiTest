<?php
namespace SearchInform\TestApp;

use SearchInform\DataBase\DbMySql;

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
        $this->db = new DbMySql();
        // $err = $db->getErrorStr();
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
    public function saveNewTask($task)
    {
        $err = $this->db->getErrorStr();
        if (empty($err)) {
            $arr = array(
                
                "uuid" => $task->uuid,
                "name" => $task->name,
                "tags" => $this->db->escapeString($task->tags),
                "priority" => $task->priority,
                "status" => $task->status
            );
            return $this->db->insertData($this->db->task_table, $arr);
        }
        return false;
    }

    /**
     * Редактировать в БД задачу
     *
     * @param Task $task
     * @return boolean
     */
    public function editTask($task)
    {
        $err = $this->db->getErrorStr();
        if (empty($err)) {
            $arr = array(
                
                "name" => $task->name,
                "tags" => $this->db->escapeString($task->tags),
                "priority" => $task->priority,
                "status" => $task->status
            );
            $where = array(
                "`uuid` = '$task->uuid'"
            ); // "`login` = $login",'and',"`password` = $pass")
            return $this->db->updateData($this->db->task_table, $where, $arr);
        }
        return false;
    }

    /**
     * Удалить в БД задачу
     *
     * @param Task $task
     * @return boolean
     */
    public function delTask($task)
    {
        $err = $this->db->getErrorStr();
        if (empty($err)) {
            $where = array(
                "`uuid` = '$task->uuid'"
            );
            return $this->db->deleteData($this->db->task_table, $where);
        }
        return false;
    }

    /**
     * Прочитать все задачи из БД
     *
     * @return array(Task)
     */
    function readAllTasks()
    {
        $err = $this->db->getErrorStr();
        if (empty($err)) {
            // получение списка задач, сначала сортируем по статусу, потом по приоритету
            return $this->db->selectData($this->db->task_table, '*', null, array(
                'status',
                'ASC',
                'priority',
                'DESC'
            
            ), null);
        }
        return false;
    }
}
