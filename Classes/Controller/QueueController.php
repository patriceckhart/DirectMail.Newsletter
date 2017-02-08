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
        /*$classname = '\DirectMail\Newsletter\Domain\Model\Queue';
        $query = $this->persistenceManager->createQueryForType($classname);
        $results = $query->matching($query->equals('done', "0"))->execute();
        $this->view->assign('results', $results);*/
        $this->view->assign('queues', $this->queueRepository->findAll());
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Queue $queue
     * @return void
     */
    public function deleteAction($queue) {
        $this->queueRepository->remove($queue);
        $this->addFlashMessage('Newsletter wurde vom Versand ausgenommen');
        $this->redirect('index','status');
    }




}
