<?php
namespace CertificadoSat;

class Key
{
    private $file;
    private $password;

    public function __construct(string $file = null, string $password = null)
    {
        $this->file      = $file;
        $this->password  = $password;
    }

    public function convertToPEM(){
        $pem = shell_exec("openssl pkcs8 -inform DER -in {$this->file} -passin pass:{$this->password}");

        return $pem;
    }
}