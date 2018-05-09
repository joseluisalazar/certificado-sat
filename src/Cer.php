<?php
namespace JLS\Certificado;


class Cer
{
    private $file;
    private $chunkLength = 64;

    public function __construct(string $file = null)
    {
        $this->file = file_get_contents($file);
    }

    public function convertToPem()
    {
        $prefix = "-----BEGIN CERTIFICATE-----\n";
        $suffix = "-----END CERTIFICATE-----\n";

        $pem = base64_encode($this->file);
        $pem = chunk_split($pem, $this->chunkLength, "\n") ;
        $pem = $prefix.$pem.$suffix;

        return $pem;
    }

    public function getNoCertificate()
    {
        $data = $this->parseCertificate();
        $data = str_split($data['serialNumberHex'], 2);

        $serialNumber = null;

        for ($i = 0; $i < sizeof($data); $i++) {
            $serialNumber .= substr($data[$i], 1);
        }

        return $serialNumber;
    }

    public function getExpirationDate()
    {
        $data = $this->parseCertificate();

        return $this->dateFormat($data['validTo_time_t']);
    }

    public function getInitialDate()
    {
        $data = $this->parseCertificate();

        return $this->dateFormat($data['validFrom_time_t']);
    }

    protected function parseCertificate()
    {
        return openssl_x509_parse($this->convertToPem());
    }

    protected function dateFormat(string $date)
    {
        return date('Y-m-d H:i:s', $date);
    }
}