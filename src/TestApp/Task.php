<?php
namespace SearchInform\TestApp;

/**
 *
 * @author Aleksandr
 *        
 */
class Task
{

    // коды приоритетов
    const PRIORITY_LOW = - 1;

    const PRIORITY_MEDIUM = 0;

    const PRIORITY_HIGHT = 2;

    // коды статусов
    const STATUS_WORK = 0;

    const STATUS_COMPLЕTED = 1;

    /**
     */
    public function __construct()
    {}

    /**
     */
    function __destruct()
    {
        
        // TODO - Insert your code here
    }

    /**
     * заполнить текущую задачу
     *
     * @param string $name
     * @param number $priority
     * @param number $status
     * @param string $tags
     * @param string $uuid
     */
    function fillTask($name, $priority = Task::PRIORITY_MEDIUM, $status = self::STATUS_WORK, $tags = '', $uuid = null)
    {
        $this->name = $name;
        $this->priority = $priority;
        $this->status = $status;
        $this->tags = $tags;
        $this->uuid == $uuid;
        if (! $this->uuid)
            $this->uuid = UUID::guuidv4();
    }
}

