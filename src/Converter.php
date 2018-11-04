<?php
declare(strict_types=1);

namespace Webmacaj\DataToSql;

use Webmacaj\DataToSql\Resource\ResourceInterface;

class Converter
{
    /** @var ResourceInterface */
    protected $resource;

    /**
     * Converter constructor.
     *
     * @param string $type
     * @param string $input
     */
    public function __construct(string $type, string $input)
    {
        $type = 'Webmacaj\\DataToSql\\Resource\\' . \ucfirst(\strtolower($type));
        if (!\class_exists($type)) {
            throw new \Exception('Unable to determine resource type.');
        }

        $this->resource = new $type();

        if (\filter_var($input, FILTER_VALIDATE_URL)) {
            $this->resource->createFromUrl($input);
        } elseif (\file_exists($input)) {
            $this->resource->createFromFile($input);
        } else {
            $this->resource->createFromString($input);
        }
    }

}