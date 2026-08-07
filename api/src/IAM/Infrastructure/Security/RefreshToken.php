<?php

declare(strict_types=1);

namespace App\IAM\Infrastructure\Security;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

/**
 * Entité du bundle gesdinet, pas un modèle de domaine : elle reste en
 * infrastructure et garde le mapping par attributs de sa classe parente.
 */
#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{
}
