<?php
namespace SearchInform\TestApp;

use Exception;

/**
 *
 * @author Aleksandr
 *        
 */
class Task
{

    public $name;

    public $priority;

    public $status;

    public $uuid;

    /**
     */
    public function __construct()
    {}

    /**
     * заполнить текущую задачу
     *
     * @param string $name
     * @param number $priority
     * @param number $status
     * @param string $tags
     * @param string $uuid
     * @return boolean
     */
    function fill($name, $priority = TaskPriority::PRIORITY_MEDIUM, $status = TaskStatus::STATUS_WORK, $tags = '', $uuid = null)
    {
        // создание объектов-значений(Value Objects) и фильтрация/валидация
        try {
            $tuuid = new TaskUUID($uuid);
            $tname = new TaskName($name);
            $tstatus = new TaskStatus((integer) $status);
            $tpriority = new TaskPriority((integer) $priority);
        } catch (Exception $e) {
            // echo 'raise exception: ', $e->getMessage(), "\n";
            return false;
        }
        $this->name = $tname;
        $this->priority = $tpriority;
        $this->status = $tstatus;
        $this->uuid = $tuuid;
        // Внимание! tags не проверенная переменная, перед записью в БД сделать escapeString(tags)
        $this->tags = $tags;
        return true;
    }
}

