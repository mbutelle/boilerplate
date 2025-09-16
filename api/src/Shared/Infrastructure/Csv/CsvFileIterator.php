<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Csv;

class CsvFileIterator implements \Iterator
{
    private string $file;
    private array $headers;
    private mixed $fileDescriptor;
    private ?array $currentElement = null;
    private string $delimiter;
    private array $linesWithError = [];
    private int $lineCounter = 1;

    public function __construct(string $file, string $delimiter = ';')
    {
        $this->file = $file;
        $this->delimiter = $delimiter;
        $this->initializeFile();
        $this->readHeaders();
        $this->next();
    }

    public function getLinesWithError(): array
    {
        return $this->linesWithError;
    }

    public function getFileName(): string
    {
        preg_match('/([0-9A-Za-z_\-]+)\.csv/', $this->file, $matches);

        return $matches[1] ?? '';
    }

    public function current(): mixed
    {
        if (is_array($this->currentElement)) {
            return array_combine($this->headers, $this->currentElement);
        }

        return false;
    }

    public function next(): void
    {
        if (!$this->isFileValid()) {
            return;
        }

        do {
            $this->readNextLine();

            if (!is_array($this->currentElement)) {
                continue;
            }

            if ($this->isInvalidLine($this->currentElement)) {
                $this->recordErrorLine($this->currentElement);
                continue;
            }

            break;
        } while ($this->isFileValid());
    }

    public function key(): int
    {
        return $this->lineCounter;
    }

    public function valid(): bool
    {
        $isResourceValid = is_resource($this->fileDescriptor);

        if ($isResourceValid && feof($this->fileDescriptor)) {
            fclose($this->fileDescriptor);

            return false;
        }

        return $isResourceValid;
    }

    public function rewind(): void
    {
        if (!is_resource($this->fileDescriptor)) {
            $this->fileDescriptor = $this->openFile();
        }

        $this->lineCounter = 1;
        rewind($this->fileDescriptor);
        $this->readHeaders();
        $this->next();
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    private function initializeFile(): void
    {
        $this->fileDescriptor = $this->openFile();

        if (false === $this->fileDescriptor) {
            throw new \RuntimeException(sprintf('Unable to read file "%s".', $this->file));
        }
    }

    private function readHeaders(): void
    {
        $headers = fgetcsv($this->fileDescriptor, 0, $this->delimiter);

        if (false === $headers || $this->isNullArray($headers)) {
            throw new \RuntimeException(sprintf('Unable to read header from the file "%s".', $this->file));
        }

        $this->headers = $headers;
    }

    private function readNextLine(): void
    {
        $line = fgetcsv($this->fileDescriptor, 0, $this->delimiter);

        if (false === $line) {
            return;
        }

        $this->currentElement = $line;
        ++$this->lineCounter;
    }

    private function isFileValid(): bool
    {
        return is_resource($this->fileDescriptor) && !feof($this->fileDescriptor);
    }

    private function isInvalidLine(array $line): bool
    {
        return $this->isNullArray($line) || $this->hasColumnCountMismatch($line);
    }

    private function hasColumnCountMismatch(array $line): bool
    {
        return count($line) !== count($this->headers);
    }

    private function recordErrorLine(array $line): void
    {
        $this->linesWithError[$this->lineCounter] = $line;
    }

    private function isNullArray(array $data): bool
    {
        return 1 === count($data) && !isset($data[0]);
    }

    private function openFile()
    {
        return @fopen($this->file, 'r');
    }
}
