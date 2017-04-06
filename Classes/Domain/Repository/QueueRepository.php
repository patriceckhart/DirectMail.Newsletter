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

        $senddatetime = $results->getSenddatetime();

        $classname2 = '\DirectMail\Newsletter\Domain\Model\Recipient';
        $query2 = $this->persistenceManager->createQueryForType($classname2);
        $results2 = $query2->matching($query2->equals('category', $category))->execute();

        $sdttimestamp = strtotime($senddatetime);
        $now = time();
        $now = intval($now);
        $sdttimestamp = intval($sdttimestamp);

        if ($now>=$sdttimestamp) {

            $results->setSend("1");
            $this->queueRepository->update($results);
            $this->persistenceManager->persistAll();


            while ($result2 = $results2->current()) {
                $email = $result2->getEmail();
                $user = $result2->getDeleted();

                $firstname = $result2->getFirstname();
                $lastname = $result2->getLastname();
                $gender = $result2->getGender();

                $salutation1 = $this->settings['salutation1'];
                $salutation2 = $this->settings['salutation2'];
                $salutation3 = $this->settings['salutation3'];

                $presalutation1 = $this->settings['presalutation1'];
                $presalutation2 = $this->settings['presalutation2'];
                $presalutation3 = $this->settings['presalutation3'];

                if($gender==1) {
                    $salutationgender = $salutation1;
                    $presalutationgender = $presalutation1;
                } else if ($gender == 2) {
                    $salutationgender = $salutation2;
                    $presalutationgender = $presalutation2;
                } else {
                    $salutationgender = $salutation3;
                    $presalutationgender = $presalutation3;
                }

                if($user=="0") {
                    $file = file_get_contents($pageurl);
                    //$body1 = preg_replace("/.*<body[^>]*>|<\/body>.*/si", "", $file);
                    //$body = $file;

                    $salutation = $presalutationgender.' '.$salutationgender.' '.$firstname.' '.$lastname;

                    if($firstname == "" || $lastname == "") {
                        $body = str_replace("{salutation}","",$file);
                        $body = str_replace("{firstname}","",$body);
                        $body = str_replace("{lastname}","",$body);
                        $body = str_replace("{gender}","",$body);
                    } else {
                        $body = str_replace("{salutation}",$salutation,$file);
                        $body = str_replace("{firstname}",$firstname,$body);
                        $body = str_replace("{lastname}",$lastname,$body);
                        $body = str_replace("{gender}",$salutationgender,$body);
                    }

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
    
}
