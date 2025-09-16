<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Csv;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\String\u;

final class HttpCsvResponseFactory
{
    private TranslatorInterface $translator;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(TranslatorInterface $translator, PropertyAccessorInterface $propertyAccessor)
    {
        $this->translator = $translator;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function build(string $class, array $data): Response
    {
        $classShortName = $this->getClassShortName($class);
        $properties = $this->getProperties($class);

        ob_start();
        $csvWriter = new CsvFileWriter('php://output', array_map(function (string $field) use ($classShortName) {
            return $this->translator->trans(sprintf('export.%s.%s', $classShortName, $field));
        }, $properties));

        foreach ($data as $row) {
            $csvWriter->write($this->export($properties, $classShortName, $row));
        }

        $response = new Response(ob_get_clean());
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s.csv"', $this->translator->trans(sprintf('export.file_name.%s', $classShortName))));

        return $response;
    }

    private function export(array $properties, string $classShortName, $object): array
    {
        $data = [];
        foreach ($properties as $field) {
            $key = $this->translator->trans(sprintf('export.%s.%s', $classShortName, $field));
            $value = $this->propertyAccessor->getValue($object, u($field)->camel()->toString());

            if ($value instanceof \DateTime) {
                $value = $value->format('d/m/Y');
            } elseif (is_bool($value)) {
                $value = $this->translator->trans(sprintf('export.%s.values.%s', $classShortName, $value ? 'true' : 'false'));
            } elseif (is_bool($value)) {
                $value = $this->translator->trans(sprintf('export.%s.values.%s', $classShortName, $value));
            }

            $data[$key] = $value;
        }

        return $data;
    }

    private function getProperties(string $class): array
    {
        return array_map(function ($key) {
            return u($key)->snake()->toString();
        }, array_keys(get_class_vars($class)));
    }

    private function getClassShortName(string $class): string
    {
        return u((new \ReflectionClass($class))->getShortName())->snake()->toString();
    }
}
