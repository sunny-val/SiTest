<?php
namespace SearchInform\TestApp;

use InvalidArgumentException;

/**
 * Приоритет задачи
 *
 * @author Aleksandr
 *        
 */
class TaskPriority
{

    private $priority_code;

    // коды приоритетов
    const PRIORITY_LOW = - 1;
    
    const PRIORITY_MEDIUM = 0;
    
    const PRIORITY_HIGH = 1;

    /**
     *
     * @param number $status_code
     * @throws InvalidArgumentException
     */
    public function __construct($priority_code)
    {
        if (! filter_var($priority_code, FILTER_VALIDATE_INT, PRIORITY_LOW, PRIORITY_HIGH)) {
            throw new InvalidArgumentException(sprintf('"%d" is not a valid status', $priority_code));
        }
        
        $this->$priority_code = $priority_code;
    }

    public function __toString()
    {
        switch ($this->priority_code) {
            case PRIORITY_LOW:
                return 'низкий';
            case PRIORITY_MEDIUM:
                return 'средний';
            case PRIORITY_HIGH:
                return 'высокий';
        }
        return '';
    }

    public function equals(TaskPriority $priority)
    {
        return strtolower((string) $this) === strtolower((string) $priority);
    }
}
