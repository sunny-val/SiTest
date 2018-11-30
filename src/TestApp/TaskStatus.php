<?php
namespace SearchInform\TestApp;

use InvalidArgumentException;

/**
 * Статус задачи
 *
 * @author Aleksandr
 *        
 */
class TaskStatus
{

    private $status_code;

    // коды статусов
    const STATUS_WORK = 1;

    const STATUS_COMPLЕTED = 2;

    /**
     *
     * @param number $status_code
     * @throws InvalidArgumentException
     */
    public function __construct($status_code)
    {
        if (! filter_var($status_code, FILTER_VALIDATE_INT, array(
            'options' => array(
                'min_range' => self::STATUS_WORK,
                'max_range' => self::STATUS_COMPLЕTED
            )
        ))) {
            throw new InvalidArgumentException(sprintf('"%d" is not a valid status', $status_code));
        }
        $this->status_code = $status_code;
    }

    public function __toString()
    {
        return (string) $this->status_code;
    }

    public function equals(TaskStatus $status)
    {
        return strtolower((string) $this) === strtolower((string) $status);
    }
}
