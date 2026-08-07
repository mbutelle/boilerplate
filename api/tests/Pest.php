<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

// Les tests d'intégration ont besoin du noyau ; les tests unitaires et
// d'architecture n'en veulent pas, pour rester rapides et diagnostiquables.
pest()->extend(KernelTestCase::class)->in('Integration');
