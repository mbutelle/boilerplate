<?php

declare(strict_types=1);

/**
 * Frontières entre couches.
 *
 * Ces règles ne décrivent pas un idéal : elles verrouillent ce qui rend le
 * domaine testable sans base et sans framework. Chacune a été vue rouge sur une
 * vraie violation avant d'être posée.
 */
it('garde le mapping Doctrine hors de Domain et Application', function (): void {
    // Le mapping vit en XML, hors des classes : c'est ce qui permet de lire et
    // de tester un modèle métier sans ORM. Un seul #[ORM\Column] posé « pour
    // aller plus vite » ramène le framework dans le domaine.
    //
    // Écrit en balayage de fichiers, et non en règle arch()->not->toUse() :
    // cette dernière a été vérifiée sur une violation réelle (un #[ORM\Column]
    // ajouté à Product) et elle est restée VERTE. Une règle qu'on n'a pas vue
    // rougir ne teste pas ce qu'on croit.
    $offenders = [];
    $base = \dirname(__DIR__, 2).'/src';

    /** @var iterable<SplFileInfo> $files */
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));

    foreach ($files as $file) {
        if (!$file->isFile() || 'php' !== $file->getExtension()) {
            continue;
        }

        $path = $file->getPathname();
        if (!preg_match('#/(Domain|Application)/#', $path)) {
            continue;
        }

        if (str_contains((string) file_get_contents($path), 'Doctrine\ORM\Mapping')) {
            $offenders[] = substr($path, strlen($base) + 1);
        }
    }

    expect($offenders)->toBe([]);
});

arch('Domain n\'utilise aucune Infrastructure')
    ->expect('App\Catalog\Domain')
    ->not->toUse('App\Catalog\Infrastructure')
    ->and('App\IAM\Domain')->not->toUse('App\IAM\Infrastructure')
    ->and('App\Shared\Domain')->not->toUse('App\Shared\Infrastructure');

arch('Application n\'utilise pas l\'Infrastructure de son contexte')
    // Application orchestre le métier ; elle ne connaît que les ports déclarés
    // dans Domain\Contract. Le jour où un handler importe un dépôt Doctrine
    // concret, il devient impossible de le tester sans base.
    ->expect('App\Catalog\Application')
    ->not->toUse('App\Catalog\Infrastructure')
    ->and('App\IAM\Application')->not->toUse('App\IAM\Infrastructure');

arch('le domaine n\'a pas d\'EntityManager ni de QueryBuilder')
    // Incident constaté ailleurs : la règle « le domaine ne requête pas la base »
    // était connue de tous, et Doctrine\ORM\QueryBuilder est quand même entré
    // dans des contrats de Domain/. Rien ne la testait.
    ->expect(['Doctrine\ORM\EntityManagerInterface', 'Doctrine\ORM\QueryBuilder'])
    ->not->toBeUsedIn(['App\Catalog\Domain', 'App\IAM\Domain', 'App\Shared\Domain']);

arch('les contextes métier restent étanches entre eux')
    // Un contexte qui en importe un autre directement crée un couplage que rien
    // ne rattrape ensuite. La communication passe par les événements ou par une
    // interface explicitement partagée.
    ->expect('App\Catalog')
    ->not->toUse('App\IAM')
    ->and('App\IAM')->not->toUse('App\Catalog');
