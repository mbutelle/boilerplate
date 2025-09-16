<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Message\Bidding;

use App\Shared\Domain\Contracts\DomainId;
use App\Shared\Domain\ValueObject\Money;
use App\Shared\Infrastructure\Message\MessageInterface;

class AuctionEnded implements MessageInterface
{
    public function __construct(
        public DomainId $sellerReference,
        public DomainId $auctionReference,
        public string $auctionUniqId,
        public DomainId $productReference,
        public ?DomainId $accountReference = null,
        public ?Money $amount = null,
    ) {
    }
}
