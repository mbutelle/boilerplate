<?php

declare(strict_types=1);

use App\IAM\Domain\Model\User;

it('donne toujours ROLE_USER, même à un administrateur', function (): void {
    expect(User::createAdmin('a@example.test')->getRoles())
        ->toContain(User::ROLE_USER)
        ->toContain(User::ROLE_ADMIN);
});

it('ne duplique pas ROLE_USER pour un utilisateur ordinaire', function (): void {
    expect(User::create('a@example.test')->getRoles())->toBe([User::ROLE_USER]);
});

it('identifie l\'utilisateur par son e-mail', function (): void {
    expect(User::create('a@example.test')->getUserIdentifier())->toBe('a@example.test');
});

it('émet un jeton de réinitialisation à chaque demande', function (): void {
    $user = User::create('a@example.test');
    expect($user->getResetToken())->toBeNull();

    $user->requestReset();
    $first = $user->getResetToken();

    $user->requestReset();

    expect($first)->not->toBeNull()
        ->and($user->getResetToken())->not->toBe($first);
});

it('efface le jeton une fois le mot de passe redéfini', function (): void {
    $user = User::create('a@example.test');
    $user->requestReset();

    $user->resetPassword('hash');

    expect($user->getResetToken())->toBeNull()
        ->and($user->getPassword())->toBe('hash')
        ->and($user->getResetAt())->not->toBeNull();
});

it('ne considère pas un utilisateur ordinaire comme administrateur', function (): void {
    expect(User::create('a@example.test')->isAdmin())->toBeFalse()
        ->and(User::createAdmin('b@example.test')->isAdmin())->toBeTrue();
});
