<?php

declare(strict_types=1);

/**
 * Règles de style prélevées une par une dans les préréglages de Pest.
 *
 * Les préréglages complets (`php`, `strict`, `security`) ne sont volontairement
 * PAS activés : ils apportent des dizaines de règles non choisies, dont
 * certaines cassent le socle. Écartées explicitement pour ne pas les
 * rediscuter :
 *   - `toBeFinal` : les entités Doctrine ne peuvent pas être finales (proxies) ;
 *   - `not->toHaveProtectedMethods` : ne protège rien ici, et le trait
 *     Timestampable de Gedmo expose des propriétés protégées ;
 *   - `toUseStrictEquality` en bloc : la base est saine, mais la règle rougit
 *     sur du code tiers importé et n'apporte rien de plus que php-cs-fixer.
 */
arch('aucune fonction de débogage ne subsiste')
    ->expect(['dd', 'dump', 'var_dump', 'ray', 'print_r', 'die', 'exit'])
    ->not->toBeUsed();

arch('aucune fonction dangereuse')
    ->expect(['eval', 'shell_exec', 'exec', 'passthru', 'system', 'extract', 'compact'])
    ->not->toBeUsed();

arch('les fichiers déclarent les types stricts')
    ->expect('App')
    ->toUseStrictTypes();

arch('les énumérations de domaine ont un type de backing')
    // Une enum sans backing ne peut pas être persistée : la contrainte se
    // découvre au premier mapping Doctrine, pas à l'écriture de l'enum.
    ->expect('App\Catalog\Domain\ValueObject\ProductStatus')
    ->toBeStringBackedEnum();

arch('rien ne dépend du conteneur de services directement')
    // Sortir un service du conteneur à la main masque ses dépendances et rend le
    // test impossible sans booter Symfony.
    ->expect('Symfony\Component\DependencyInjection\ContainerInterface')
    ->not->toBeUsedIn('App');
