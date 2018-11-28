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
        if (! preg_match('/^[A-Za-z0-9_-]*$/', $name)) {
            // if (! filter_var($name, FILTER_VALIDATE_STRING)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid name', $name));
        }
        
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->$name;
    }

    public function equals(TaskName $name)
    {
        return strtolower((string) $this) === strtolower((string) $name);
    }
}

class EmailAddress
{

    private $address;

    public function __construct($address)
    {
        if (! filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid email', $address));
        }
        
        $this->address = $address;
    }

    public function __toString()
    {
        return $this->address;
    }

    public function equals(EmailAddress $address)
    {
        return strtolower((string) $this) === strtolower((string) $address);
    }
}