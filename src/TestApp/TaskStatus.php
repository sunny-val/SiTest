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

    private $status;

    // коды статусов
    const STATUS_WORK = 0;

    const STATUS_COMPLЕTED = 1;

    /**
     *
     * @param number $status_code
     * @throws InvalidArgumentException
     */
    public function __construct($status_code)
    {
        if (! filter_var($status_code, FILTER_VALIDATE_INT, STATUS_WORK, STATUS_COMPLЕTED)) {
            throw new InvalidArgumentException(sprintf('"%d" is not a valid status', $status_code));
        }
        
        $this->status = $status_code;
    }

    public function __toString()
    {
        switch ($this->status) {
            case STATUS_WORK:
                return 'в работе';
            case STATUS_COMPLЕTED:
                return 'завершена';
        }
        return '';
    }

    public function equals(TaskStatus $status)
    {
        return strtolower((string) $this) === strtolower((string) $status);
    }
}
