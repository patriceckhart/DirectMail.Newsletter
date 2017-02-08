<?php
namespace DirectMail\Newsletter\Command;

/*
 * This file is part of the DirectMail.Newsletter package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * @Flow\Scope("singleton")
 */
class CronCommandController extends CommandController
{

    /**
     * @Flow\Inject
     * @var \DirectMail\Newsletter\Domain\Repository\QueueRepository
     */
    protected $queueRepository;

    /**
     * @return void
     */
    public function workoffCommand() {
        $this->queueRepository->workoffAll();
        //In your crontab 1      *      *       *       *       /var/www/html/./flow cron:workoff
    }

}
