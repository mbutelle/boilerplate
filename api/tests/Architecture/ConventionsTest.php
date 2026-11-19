<?php

declare(strict_types=1);

use App\Shared\Application\Command\AsCommandHandler;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\ApiPlatform\Resource\Payload\CommandableInterface;

/**
 * Conventions propres au socle. Chacune protège une règle qui, sans test,
 * finit contournée sans mauvaise foi et sans que personne ne le voie.
 */

/**
 * @return list<class-string>
 */
function classesUnder(string $directory, string $suffix): array
{
    $found = [];
    $base = \dirname(__DIR__, 2).'/src';

    /** @var iterable<SplFileInfo> $files */
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base.$directory));

    foreach ($files as $file) {
        if (!$file->isFile() || !str_ends_with($file->getFilename(), $suffix.'.php')) {
            continue;
        }

        $relative = substr($file->getPathname(), strlen($base) + 1, -4);
        /** @var class-string $class */
        $class = 'App\\'.str_replace('/', '\\', $relative);

        if (class_exists($class)) {
            $found[] = $class;
        }
    }

    return $found;
}

it('taggue tous les handlers de commande avec AsCommandHandler', function (): void {
    // Sans l'attribut, la classe n'est pas taguée sur le bus : la commande part
    // et personne ne la traite. Messenger ne dit rien, l'effet attendu n'arrive
    // simplement jamais.
    // Filtré sur Application\Command : sans ça, la classe d'attribut
    // AsCommandHandler est elle-même capturée par le suffixe.
    $handlers = array_filter(
        classesUnder('', 'CommandHandler'),
        static fn (string $class): bool => str_contains($class, '\\Application\\Command\\')
            && !str_starts_with($class, 'App\\Shared\\'),
    );

    expect($handlers)->not->toBeEmpty();

    foreach ($handlers as $handler) {
        expect((new ReflectionClass($handler))->getAttributes(AsCommandHandler::class))
            ->not->toBeEmpty("{$handler} n'a pas l'attribut AsCommandHandler");
    }
});

it('fait implémenter CommandInterface à toutes les commandes', function (): void {
    // Le bus n'accepte que des CommandInterface : une commande qui ne l'implémente
    // pas échoue au dispatch, à l'exécution et non à la compilation.
    $commands = array_filter(
        classesUnder('', 'Command'),
        static fn (string $class): bool => str_contains($class, '\Application\Command\\'),
    );

    expect($commands)->not->toBeEmpty();

    foreach ($commands as $command) {
        expect(is_a($command, CommandInterface::class, true))
            ->toBeTrue("{$command} n'implémente pas CommandInterface");
    }
});

it('fait implémenter CommandableInterface à tous les payloads', function (): void {
    // Un payload qui ne l'implémente pas ne peut pas être traduit en commande par
    // le processor générique, qui lève alors « Unsupported data type ».
    $payloads = classesUnder('', 'Payload');

    expect($payloads)->not->toBeEmpty();

    foreach ($payloads as $payload) {
        expect(is_a($payload, CommandableInterface::class, true))
            ->toBeTrue("{$payload} n'implémente pas CommandableInterface");
    }
});

arch('les ressources API Platform vivent en Infrastructure')
    // Poser #[ApiResource] sur un modèle de domaine ramène le framework dans le
    // domaine — c'est exactement la forme dont ce socle a été sorti.
    // Une cible par expect(), enchaînées par and() : Pest ne vérifie que la
    // première d'une liste passée à toBeUsedIn(), et rien du tout d'une liste
    // passée à expect(). Constaté dans Holoworkflow en posant de vraies violations.
    ->expect('App\Catalog\Domain')->not->toUse('ApiPlatform\Metadata\ApiResource')
    ->and('App\IAM\Domain')->not->toUse('ApiPlatform\Metadata\ApiResource')
    ->and('App\Shared\Domain')->not->toUse('ApiPlatform\Metadata\ApiResource');

arch('les dépôts concrets restent en Infrastructure')
    // Une cible par expect(), enchaînées par and() : Pest ne vérifie que la
    // première d'une liste passée à toBeUsedIn(), et rien du tout d'une liste
    // passée à expect(). Constaté dans Holoworkflow en posant de vraies violations.
    ->expect('App\Catalog\Domain')->not->toUse('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository')
    ->and('App\IAM\Domain')->not->toUse('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository')
    ->and('App\Catalog\Application')->not->toUse('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository')
    ->and('App\IAM\Application')->not->toUse('Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository');

arch('RequestStack ne sert pas à lire des paramètres de collection')
    // Les paramètres d'une collection se lisent dans $context['filters'], jamais
    // dans la Request : un paramètre lu ailleurs n'entre pas dans l'OpenAPI,
    // donc pas dans le SDK, et le front demande autre chose que ce qu'on lit.
    ->expect('App\Catalog\Infrastructure\ApiPlatform\State\Provider')->not->toUse('Symfony\Component\HttpFoundation\RequestStack')
    ->and('App\IAM\Infrastructure\ApiPlatform\State\Provider')->not->toUse('Symfony\Component\HttpFoundation\RequestStack');
