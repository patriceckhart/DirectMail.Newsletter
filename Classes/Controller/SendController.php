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
     * @var array
     */
    protected $settings;

    /**
     * Inject settings
     *
     * @param array $settings
     * @return void
     */
    public function injectSettings(array $settings) {
        $this->settings = $settings;
    }


    /**
     * @return void
     */
    public function indexAction()
    {
        $newsletterSite = $this->settings['newsletterSite'].'/';
        $this->view->assign('nodes', $this->nodeDataRepository->findAll());
        $this->view->assign('hostname', "http://".$this->request->getHttpRequest()->getBaseUri()->getHost()."/");
        $this->view->assign('newslettersite', $newsletterSite);
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Queue $newSend
     * @return void
     */
    public function sendAction($newSend) {
        $this->view->assign('categories', $this->categoryRepository->findAll());

        $pageUrl = $newSend->getPageurl();
        $title = $newSend->getTitle();
        $newsletterSite = $this->settings['newsletterSite'].'/';
        $senddatetime = $newSend->getSenddatetime();

        $hostname = "http://".$this->request->getHttpRequest()->getBaseUri()->getHost()."/";
        $fullUrl = $hostname.$newsletterSite.$pageUrl.".html";

        $this->view->assign('pageUrl', $pageUrl.'.html');
        $this->view->assign('title', $title);
        $this->view->assign('hostname', "http://".$this->request->getHttpRequest()->getBaseUri()->getHost()."/");
        $this->view->assign('fullUrl', $fullUrl);
        $this->view->assign('senddatetime', $senddatetime);
        $this->view->assign('newslettersite', $newsletterSite);
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Queue $newSend
     * @return void
     */
    public function finishAction($newSend) {

        $this->persistenceManager->persistAll();

        $category = $newSend->getCategory();

        $senddatetime = $newSend->getSenddatetime();

        $filterArr = array('category' => $category, 'deleted' => "0");

        $classname = '\DirectMail\Newsletter\Domain\Model\Recipient';
        $query = $this->persistenceManager->createQueryForType($classname);

        foreach($filterArr as $property => $filter) {
            if(isset($filter)) {
                $constraintArr[] = $query->equals($property, $filter);
            }
        }

        $results = $query->matching($query->logicalAnd($constraintArr))->execute();

        $recipients = count($results);

        $newSend->setQuantity($recipients);
        $newSend->setSend("0");
        $newSend->setPosted("0");

        $newSend->setSenddatetime($senddatetime);

        $this->queueRepository->add($newSend);

        $this->redirect('index');
    }



}
