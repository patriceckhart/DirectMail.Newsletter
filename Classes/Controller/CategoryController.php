<?php
namespace DirectMail\Newsletter\Controller;

/*
 * This file is part of the DirectMail.Newsletter package.
 */

use DirectMail\Newsletter\Domain\Model\Category;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

class CategoryController extends ActionController
{

    /**
     * @Flow\Inject
     * @var \DirectMail\Newsletter\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @Flow\Inject
     * @var \DirectMail\Newsletter\Domain\Repository\RecipientRepository
     */
    protected $recipientRepository;

    /**
     * @return void
     */
    public function indexAction() {
        $this->view->assign('categories', $this->categoryRepository->findAll());
    }

    /**
     * @return void
     */
    public function newAction() {

    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Category $newCategory
     * @return void
     */
    public function createAction($newCategory) {
        $this->categoryRepository->add($newCategory);
        $this->redirect('index');
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Category $category
     * @return void
     */
    public function editAction($category) {
        $this->view->assign('category', $category);
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Category $category
     * @return void
     */
    public function updateAction(Category $category) {
        $this->categoryRepository->update($category);
        $this->redirect('index');
    }

    /**
     * @param \DirectMail\Newsletter\Domain\Model\Category $category
     * @return void
     */
    public function deleteAction(Category $category) {
        $category->setDeleted('1');
        $this->categoryRepository->update($category);
        $this->redirect('index');
    }

}
