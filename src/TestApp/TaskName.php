<?php
namespace SearchInform\TestApp;

use InvalidArgumentException;

/**
 * Название задачи
 *
 * @author Aleksandr
 *        
 */
class TaskName
{

    private $name;

    // коды статусов
    const STATUS_WORK = 0;

    const STATUS_COMPLЕTED = 1;

    /**
     *
     * @param number $status_code
     * @throws InvalidArgumentException
     */
    public function __construct($name)
    {
        if (! filter_var($name, FILTER_DEFAULT)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid name', $name));
        }
        
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function equals(TaskName $name)
    {
        return strtolower((string) $this) === strtolower((string) $name);
    }
}
