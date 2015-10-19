<?php
namespace SkelletonApplication\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use SkelletonApplication\Options\SiteRegistrationOptions;

class IndexController extends AbstractActionController
{
	public function indexAction(){
		return new ViewModel();
	}
}
