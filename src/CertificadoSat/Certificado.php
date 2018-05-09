<?php

namespace CertificadoSat;

class Certificado
{
    private $file;
    private $password;
    private $type;

    public function __construct($file = null, $password = null, $type = null)
    {
        $this->file     = $file;
        $this->password = $password;
        $this->type     = $type;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call(string $name, array $arguments)
    {
        $strategy = $this->getCertificateType();

        if (method_exists($strategy, $name)) {
            return $strategy->{$name}($arguments);
        } else {
            throw new Exception("This method doesn't exist");
        }
    }

    public function getFileName()
    {
        $nameFile = pathinfo($this->file);
        return $nameFile['filename'];
    }

    public function getFileExtension()
    {
        $extension = strchr($this->file, ".");
        $extension = substr($extension , 1);

        return $extension;
    }

    public function getCertificateType(){
        $strategy = null;

        switch ($this->getFileExtension()){
            case "cer":
                $strategy = new Cer($this->file);
                break;

            case "key":
                $strategy = new Key($this->file, $this->password);
                break;
        }

        return $strategy;
    }

    public function convertToPEM(){
        $certificate = $this->getCertificateType();
        $pem         = $certificate->convertToPEM();

        return $pem;
    }

    public function save(string $directory, string $filename = null)
    {
        $filename  = $filename ?? $this->getFileName();
        $extension = $this->getFileExtension();

        $directory = rtrim($directory, '/').'/';
        $directory = "{$directory}{$filename}.{$extension}.pem";

        return file_put_contents($directory, $this->convertToPEM());
    }

}