<?php
namespace SearchInform\TestApp;

use InvalidArgumentException;

class TaskUUID
{

    private $uuid;

    /**
     *
     * @param string $uuid
     * @throws InvalidArgumentException
     */
    public function __construct($uuid)
    {
        // если не id, создаём его
        if (! $uuid)
            $uuid = $this->getUUIDV4();
        if (! filter_var($uuid, FILTER_DEFAULT) || ! $this->checkUUIDV4($uuid)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid UUID4', $uuid));
        }
        $this->uuid = $uuid;
    }

    public function __toString()
    {
        return $this->uuid;
    }

    public function equals(TaskName $uuid)
    {
        return strtolower((string) $this) === strtolower((string) $uuid);
    }

    public function checkUUIDV4($uuid)
    {
        preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $uuid, $matches);
        if ($uuid != $matches[0])
            return false;
        return true;
    }

    public function getUUIDV4()
    {
        if (function_exists('random_bytes')) // since PHP 7
            $data = random_bytes(16);
        else
            $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);
        // set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        // пример: fc5fc0a2-b058-49a2-a277-4206f45a7ebd
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
