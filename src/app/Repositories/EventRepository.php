<?php

namespace App\Repositories;

use App\Entities\Event;
use App\Helpers\MyPDO;
use DI\Attribute\Inject;
use DI\Attribute\Injectable;

#[Injectable]
class EventRepository extends BaseRepository
{
    public function __construct(
        #[Inject('MyPDO')]
        MyPDO $myPDO,
    )
    {
        parent::__construct($myPDO, Event::class);
    }
}
