<?php
namespace SearchInform\TestApp;

class UUID
{

    public function guuidv4()
    {
        if (function_exists('random_bytes'))
            $data = random_bytes(16); // since PHP 7
        else
            $data = openssl_random_pseudo_bytes(16);
        
        assert(strlen($data) == 16);
        
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
