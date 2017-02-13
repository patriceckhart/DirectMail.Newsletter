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

    /**
     * @return void
     */
    public function cleanupAction() {

        $classname = '\DirectMail\Newsletter\Domain\Model\Queue';
        $query = $this->persistenceManager->createQueryForType($classname);
        $results = $query->matching($query->equals('send', "1"))->execute();


        while ($result = $results->current()) {

            $quantity = $result->getQuantity();
            $posted = $result->getPosted();

            if($quantity==$posted) {
                $this->queueRepository->remove($result);
                $this->persistenceManager->persistAll();
            }

            $results->next();

        }


        $this->redirect('index','status');
    }




}
