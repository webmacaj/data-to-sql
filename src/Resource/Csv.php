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
        'text/csv',
        'text/plain'
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
     *
     * @return bool
     */
    public function setHeaderOffset(int $offset = 0): bool
    {
        if (!isset($this->data[$offset]) || $this->data[$offset]) {
            return false;
        }

        $this->header = $this->data[$offset];

        return (bool) count($this->header);
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

        while ($data = \fgetcsv($this->resource)) {
            $this->data[] = \array_fill_keys($this->header, $data);
        }
        \fclose($this->resource);

        return $this->data;
    }

    public function getCount(): int
    {

    }

    public function getRow(int $row): array
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