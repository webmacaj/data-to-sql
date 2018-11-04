<?php
declare(strict_types=1);

namespace Webmacaj\DataToSql\Resource;

/**
 * Class Csv
 * @package Webmacaj\DataToSql\Resource
 */
class Csv implements ResourceInterface
{
    /** @var array */
    protected $data = [];

    /** @var array */
    protected $header = [];

    /** @var array */
    protected $allowedMimeTypes = [
        'text/csv'
    ];

    /** @var resource */
    protected $resource;

    /**
     * {@inheritdoc}
     */
    public function createFromFile(string $path)
    {
        if (!\file_exists($path)) {
            throw new \Exception('File does not exist.');
        }

        $f = fopen($path, 'r');

        if (!$f) {
            throw new \Exception('Unable to open file for reading.');
        }

        $this->header = fgetcsv($f);
        $this->resource = $f;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromString(string $data)
    {
        $this->validateMimeType($data);

        $f = fopen('php://memory', 'r+');
        fwrite($f, $data);

        $this->header = fgetcsv($f);
        $this->resource = $f;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromUrl(string $url)
    {
        throw new \Exception('Url not supported yet.');
    }

    /**
     * Set header from specific offset.
     *
     * @param int $offset
     */
    public function setHeaderOffset(int $offset = 0)
    {
        if (!isset($this->data[$offset])) {
            throw new \Exception('Data at offset position ' . $offset . ' is empty.');
        }

        $this->header = $this->data[$offset];
    }

    /**
     * Get data with specified header.
     *
     * @return array
     */
    public function getRows(): array
    {
        if (!$this->header) {
            $this->header = $this->setHeaderOffset(0);
        }

        while ($data = fgetcsv($this->resource)) {
            $this->data[] = array_fill_keys($this->header, $data);
        }

        return $this->data;
    }

    public function getCount(): int
    {

    }

    /**
     * Validate string data type.
     *
     * @param string $data
     * @return bool
     */
    protected function validateMimeType(string $data): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $data);

        if (!$mimeType || !in_array($mimeType, $this->allowedMimeTypes)) {
            throw new \Exception('Unable to validate string data.');
        }

        return true;
    }
}