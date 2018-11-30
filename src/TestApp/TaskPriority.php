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
    const PRIORITY_LOW = 1;

    const PRIORITY_MEDIUM = 2;

    const PRIORITY_HIGH = 3;

    /**
     *
     * @param number $status_code
     * @throws InvalidArgumentException
     */
    public function __construct($priority_code)
    {
        if (! filter_var($priority_code, FILTER_VALIDATE_INT, array(
            'options' => array(
                'min_range' => self::PRIORITY_LOW,
                'max_range' => self::PRIORITY_HIGH
            )
        ))) {
            throw new InvalidArgumentException(sprintf('"%d" is not a valid status', $priority_code));
        }
        $this->priority_code = $priority_code;
    }

    public function __toString()
    {
        return (string) $this->priority_code;
    }

    public function equals(TaskPriority $priority)
    {
        return strtolower((string) $this) === strtolower((string) $priority);
    }
}
