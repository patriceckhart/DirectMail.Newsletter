<?php
namespace DirectMail\Newsletter\Domain\Model;

/*
 * This file is part of the DirectMail.Newsletter package.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class Queue
{

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $pageurl;

    /**
     * @var \DateTime
     */
    protected $datetime;

    /**
     * @var string
     */
    protected $category;

    /**
     * @var string
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $posted;

    /**
     * @var string
     */
    protected $send;

    /**
     * @var string
     */
    protected $senddatetime;


    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPageurl() {
        return $this->pageurl;
    }

    /**
     * @param string $pageurl
     * @return void
     */
    public function setPageurl($pageurl) {
        $this->pageurl = $pageurl;
    }

    public function __construct() {
        $this->datetime = new \DateTime();
    }

    /**
     * @return string
     */
    public function getDatetime() {
        return $this->datetime;
    }

    /**
     * @return string
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * @param string $category
     * @return void
     */
    public function setCategory($category) {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     * @return void
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getSend() {
        return $this->send;
    }

    /**
     * @param string $send
     * @return void
     */
    public function setSend($send) {
        $this->send = $send;
    }

    /**
     * @return string
     */
    public function getPosted() {
        return $this->posted;
    }

    /**
     * @param string $posted
     * @return void
     */
    public function setPosted($posted) {
        $this->posted = $posted;
    }

    /**
     * @return string
     */
    public function getSenddatetime() {
        return $this->senddatetime;
    }

    /**
     * @param string $senddatetime
     * @return void
     */
    public function setSenddatetime($senddatetime) {
        $this->senddatetime = $senddatetime;
    }

}
