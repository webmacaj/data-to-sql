<?php
declare(strict_types=1);

namespace Webmacaj\DataToSql\Resource;

interface ResourceInterface
{
    /**
     * Create from file.
     *
     * @param string $path
     */
    public function createFromFile(string $path);

    /**
     * Create from raw data.
     *
     * @param string $data
     */
    public function createFromString(string $data);

    /**
     * Create from url.
     *
     * @param string $url
     */
    public function createFromUrl(string $url);

    /**
     * Return record count.
     *
     * @return int
     */
    public function getCount(): int;
}