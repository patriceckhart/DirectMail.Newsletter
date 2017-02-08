<?php
namespace DirectMail\Newsletter\Controller;

/*
 * This file is part of the DirectMail.Newsletter package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

class SendController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \Neos\ContentRepository\Domain\Repository\NodeDataRepository
     */
    protected $nodeDataRepository;

    /**
     * @Flow\Inject
     * @var \DirectMail\Newsletter\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @Flow\Inject
     * @var \DirectMail\Newsletter\Domain\Repository\QueueRepository
     */
    protected $queueRepository;

    /**
     * @return void
     */
    public function indexAction()
    {
        $this->view->assign('nodes', $this->nodeDataRepository->findAll());
        $this->view->assign('hostname', "http://".$this->request->getHttpRequest()->getBaseUri()->getHost()."/");
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Queue $newSend
     * @return void
     */
    public function sendAction($newSend) {
        $this->view->assign('categories', $this->categoryRepository->findAll());

        $pageUrl = $newSend->getPageurl();
        $title = $newSend->getTitle();
        $hostname = "http://".$this->request->getHttpRequest()->getBaseUri()->getHost()."/";
        $fullUrl = $hostname.$pageUrl.".html";

        $this->view->assign('pageUrl', $pageUrl.'.html');
        $this->view->assign('title', $title);
        $this->view->assign('hostname', "http://".$this->request->getHttpRequest()->getBaseUri()->getHost()."/");
        $this->view->assign('fullUrl', $fullUrl);

    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Queue $newSend
     * @return void
     */
    public function finishAction($newSend) {

        $this->persistenceManager->persistAll();

        $category = $newSend->getCategory();

        $classname = '\DirectMail\Newsletter\Domain\Model\Recipient';
        $query = $this->persistenceManager->createQueryForType($classname);
        $results = $query->matching($query->equals('category', $category))->execute();

        $recipients = count($results);

        $newSend->setQuantity($recipients);
        $newSend->setSend("0");
        $newSend->setPosted("0");

        $this->queueRepository->add($newSend);

        $this->addFlashMessage('Newsletter in der Warteschlange.');

        $this->redirect('index');
    }



}
