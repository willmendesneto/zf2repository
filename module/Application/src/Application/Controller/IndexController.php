<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\ActionController;

/**
 * Controlador que gerencia os posts
 *
 * @category Application
 * @package Controller
 * @author  Elton Minetto <eminetto@coderockr.com>
 */
class IndexController extends ActionController
{
	/**
	 * Mostra os posts cadastrados
	 * @return void
	 */
	public function indexAction()
	{
		$post = $this->getTable('Application\Model\Post');
		$sql = $post->getSql();
		$select = $sql->select();

		$paginatorAdapter = new PaginatorDbSelectAdapter($select, $sql);
		$paginator = new Paginator($paginatorAdapter);
		$paginator->setCurrentPageNumber($this->params()->fromRoute('page'));

		return new ViewModel(array(
			'posts' => $paginator
			));
	}
}