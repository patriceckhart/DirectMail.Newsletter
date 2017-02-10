<?php
namespace DirectMail\Newsletter\Controller;

/*
 * This file is part of the DirectMail.Newsletter package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

class QueueController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \DirectMail\Newsletter\Domain\Repository\QueueRepository
     */
    protected $queueRepository;

    /**
     * @return void
     */
    public function indexAction() {
        $this->view->assign('queues', $this->queueRepository->findAll());
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Queue $queue
     * @return void
     */
    public function deleteAction($queue) {
        $this->queueRepository->remove($queue);
        $this->redirect('index','status');
    }




}
