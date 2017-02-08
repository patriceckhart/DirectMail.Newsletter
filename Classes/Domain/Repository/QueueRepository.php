<?php
namespace DirectMail\Newsletter\Domain\Repository;

/*
 * This file is part of the DirectMail.Newsletter package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class QueueRepository extends Repository
{

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
    public function workoffAll() {

        $classname = '\DirectMail\Newsletter\Domain\Model\Queue';
        $query = $this->persistenceManager->createQueryForType($classname);
        $results = $query->matching($query->equals('send', "0"))->execute()->getFirst();

        $category = $results->getCategory();
        $title = $results->getTitle();
        $pageurl = $results->getPageurl();

        $results->setSend("1");
        $this->queueRepository->update($results);

        $this->persistenceManager->persistAll();

        $classname2 = '\DirectMail\Newsletter\Domain\Model\Recipient';
        $query2 = $this->persistenceManager->createQueryForType($classname2);
        $results2 = $query2->matching($query2->equals('category', $category))->execute();


        while ($result2 = $results2->current()) {
            $email = $result2->getEmail();
            $user = $result2->getDeleted();

            if($user=="0") {
                $file = file_get_contents($pageurl);
                //$body1 = preg_replace("/.*<body[^>]*>|<\/body>.*/si", "", $file);
                $body = $file;

                $subject = $title;

                $mail = new \Neos\SwiftMailer\Message();
                $mail->setFrom(array($this->settings['senderMail'] => $this->settings['senderName']))
                    ->setTo(array($email))
                    ->setReplyTo(array($this->settings['replyToMail'] => $this->settings['replyToName']))
                    ->setSubject($subject)
                    ->setBody($body, 'text/html')
                    ->send();

                $posted = $results->getPosted();
                $results->setPosted($posted+1);
                $this->queueRepository->update($results);
                $this->persistenceManager->persistAll();
            }

            $results2->next();
        }

    }

}
