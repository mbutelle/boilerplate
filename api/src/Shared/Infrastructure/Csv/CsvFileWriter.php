<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Csv;

class CsvFileWriter
{
    private string $file;

    /**
     * @var resource|null
     */
    private $fileDescriptor;

    private string $delimiter;

    /**
     * @var list<string>
     */
    private array $headers;

    /**
     * @param list<string> $headers
     */
    public function __construct(?string $file = null, array $headers = [], string $delimiter = ';')
    {
        $this->delimiter = $delimiter;
        $this->file = $file ?? 'php://stdout';
        $this->headers = $headers;

        $this->fileDescriptor = $this->createFileDescriptor($this->file);

        if ($this->headers) {
            fputcsv($this->fileDescriptor, $this->headers, $this->delimiter, '"', '\\');
        }
    }

    /**
     * @param array<string, scalar|null> $line
     *
     * @throws \InvalidArgumentException
     */
    public function write(array $line): void
    {
        if (null === $this->fileDescriptor) {
            throw new \LogicException('Cannot write, the writer is closed.');
        }

        $this->checkLine($line);
        $this->checkLineWithHeader($line);

        fputcsv($this->fileDescriptor, $line, $this->delimiter, '"', '\\');
    }

    /**
     * @throws \LogicException
     */
    public function close(): void
    {
        if (null === $this->fileDescriptor) {
            throw new \LogicException('Cannot close the writer, it was not opened.');
        }

        fclose($this->fileDescriptor);
        $this->fileDescriptor = null;
    }

    /**
     * @return resource
     */
    private function createFileDescriptor(string $file)
    {
        if (!is_dir(\dirname($file))) {
            mkdir(\dirname($file), 0775, true);
        }

        $fp = @fopen($file, 'w');
        if (false === $fp) {
            throw new \RuntimeException(sprintf('Unable to write to file "%s".', $file));
        }

        return $fp;
    }

    /**
     * @param array<string, mixed> $line
     */
    private function checkLine(array $line): void
    {
        foreach ($line as $value) {
            if (\is_array($value) || \is_object($value)) {
                throw new \InvalidArgumentException(sprintf('Expected a scalar, got a "%s".', \is_object($value) ? $value::class : strtolower(\gettype($value))));
            }
        }
    }

    /**
     * @param array<string, mixed> $line
     */
    private function checkLineWithHeader(array $line): void
    {
        if (!$this->headers) {
            return;
        }

        if (array_diff($this->headers, array_keys($line))) {
            throw new \InvalidArgumentException(sprintf('The line to insert in the file is incorrect.'."\n".'Please respect the format of the header "[\'%s\']".'."\n".'Current line: "[\'%s\']".'."\n", implode('\', \'', $this->headers), implode('\', \'', array_keys($line))));
        }
    }
}
