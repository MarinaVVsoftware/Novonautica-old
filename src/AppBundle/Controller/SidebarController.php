<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/15/17
 * Time: 11:48
 */

namespace AppBundle\Controller;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SidebarController extends Controller
{
    private $paths;
    private $currentPath;

    public function __construct()
    {
        $this->currentPath = $this->get('request_stack')->getMasterRequest()->getRequestUri();
        $this->paths = new ArrayCollection();
    }

    public function sidebarAction()
    {
        return $this->paths;
    }

    private function fullPaths()
    {

    }

    private function adminPaths()
    {

    }
}