<?php
namespace DirectMail\Newsletter\Controller;

/*
 * This file is part of the DirectMail.Newsletter package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

class RecipientController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \DirectMail\Newsletter\Domain\Repository\RecipientRepository
     */
    protected $recipientRepository;

    /**
     * @Flow\Inject
     * @var \DirectMail\Newsletter\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

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

    protected function initializeCreateAction()
    {
        $this->arguments['newRecipient']->getPropertyMappingConfiguration()->allowProperties('gender');
    }

    /**
     * @return void
     */
    public function subscribeAction()
    {
        $category = $this->request->getInternalArgument('__category');
        $this->view->assign('category', $category);
    }

    /**
     * @return void
     */
    public function newAction()
    {
        $this->view->assign('categories', $this->categoryRepository->findAll());
    }

    /**
     * @return void
     */
    public function unsubscribeAction()
    {

    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Recipient $newRecipient
     * @return void
     */
    public function createAction($newRecipient) {

        $firstname = $newRecipient->getFirstname();
        $lastname = $newRecipient->getLastname();
        $email = $newRecipient->getEmail();

        $this->recipientRepository->add($newRecipient);

        $unsubscribeLink = $this->settings['unsubscribeLink'];
        $body = $this->settings['subscribeBody'];
        $unsubscribeButton = '<a href="'.$unsubscribeLink.'" target="_blank">'.$this->settings['unsubscribeButtonText'].'</a>';
        $footer = $this->settings['subscribeFooter'];
        $mailwrapper = '<div style="width:900px; margin-left:auto; margin-right:auto; margin-top:40px; margin-bottom:40px; font-family: Arial, Helvetica, sans-serif;">'.$body.$unsubscribeButton.$footer.'</div>';

        $mailcontent = $mailwrapper;

        $mail = new \Neos\SwiftMailer\Message();
        $mail->setFrom(array($this->settings['adminMail'] => $this->settings['adminName']))
            ->setTo(array($email => $firstname.' '.$lastname))
            ->setReplyTo(array($this->settings['replyToMail'] => $this->settings['replyToName']))
            ->setSubject($this->settings['subscribeSubject'])
            ->setBody($mailcontent, 'text/html')
            ->send();


        $this->redirect('subscribe');

    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Recipient $newRecipient
     * @return void
     */
    public function addAction($newRecipient) {
        $this->recipientRepository->add($newRecipient);

        $this->redirect('index');

    }

    /**
     * @param string $email
     * @return void
     */
    public function deleteAction($email) {

        $classname = '\DirectMail\Newsletter\Domain\Model\Recipient';
        $query = $this->persistenceManager->createQueryForType($classname);
        $results = $query->matching($query->equals('email', $email))->execute();
        $count = count($results);

        if ($count==0) {

            $this->redirect('unsubscribe');
        } else {
            while ($result = $results->current()) {
                $result->setDeleted('1');
                $this->recipientRepository->update($result);
                $results->next();
            }

        }

        $this->redirect('unsubscribe');
    }

    /**
     * @return void
     */
    public function indexAction() {
        $this->view->assign('recipients', $this->recipientRepository->findAll());
        $this->view->assign('categories', $this->categoryRepository->findAll());
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Recipient $recipient
     * @return void
     */
    public function editAction($recipient) {
        $this->view->assign('recipient', $recipient);
        $this->view->assign('categories', $this->categoryRepository->findAll());
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Recipient $recipient
     * @return void
     */
    public function updateAction($recipient) {
        $this->recipientRepository->update($recipient);

        $this->redirect('index');
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Recipient $recipient
     * @return void
     */
    public function hideAction($recipient) {
        $recipient->setDeleted('1');
        $this->recipientRepository->update($recipient);

        $this->redirect('index');
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Recipient $recipient
     * @return void
     */
    public function activateAction($recipient) {
        $recipient->setDeleted('0');
        $this->recipientRepository->update($recipient);
        $this->redirect('index');
    }

    /**
     * @return void
     */
    public function importAction()
    {
        $this->view->assign('categories', $this->categoryRepository->findAll());
    }

    /**
     * @return void
     * @param string $csvdata
     * @param string $category
     */
    public function importCSVAction($csvdata,$category)
    {
        $recipientlist = $csvdata;
        $recipientlist = explode("\n", $recipientlist);
        foreach ($recipientlist as $recipient)
        {
            list($firstname, $lastname, $email, $gender) = explode(';', $recipient);
            $recipientdat = new \DirectMail\Newsletter\Domain\Model\Recipient();
            $recipientdat->setFirstname($firstname);
            $recipientdat->setLastname($lastname);
            $recipientdat->setEmail($email);
            $recipientdat->setGender($gender);

            $recipientdat->setCategory($category);

            $recipientdat->setDeleted('0');

            $this->recipientRepository->add($recipientdat);
        }

        $this->redirect('index');
    }

}
