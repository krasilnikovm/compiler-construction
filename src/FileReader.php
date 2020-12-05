<?php declare(strict_types=1);

namespace Krasilnikovm\Compiler;

/**
 * Class FileReader
 *
 * @author Mihail Krasilnikov <mihail.krasilnikov.j@gmail.com>
 */
final class FileReader
{
    private $resource = null;
    private int $currentLine = 1;
    private int $currentPosition = 1;

    public function __construct(string $path)
    {
        $this->resource = fopen($path, 'rb');

        if ($this->resource === false) {
            throw new \RuntimeException('Resource not opened');
        }
    }

    public function getChar(): ?string
    {
        $char = \fgetc($this->resource);

        if (\strpos($char === false ? '' : $char , "\n") !== false) {
            $this->currentLine++;
            $this->currentPosition = 1;
        } else {
            $this->currentPosition++;
        }

        if ($char === false) {
            $char = null;
        }

        return $char;
    }

    public function closeFile(): bool
    {
        return fclose($this->resource);
    }

    public function getCurrentLine(): int
    {
        return $this->currentLine;
    }

    public function getCurrentPosition(): int
    {
        return $this->currentPosition;
    }
}