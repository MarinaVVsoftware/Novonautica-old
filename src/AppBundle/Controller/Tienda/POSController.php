<?php
/**
 * User: inrumi
 * Date: 6/25/18
 * Time: 12:44
 */

namespace AppBundle\Controller\Tienda;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class POSController
 * @package AppBundle\Controller\Tienda
 * @Route("/tienda/pos")
 */
class POSController extends AbstractController
{
    /**
     * @Route("/", name="tienda_pos_index")
     */
    public function indexAction()
    {
        return $this->render(
            'tienda/pos/index.html.twig',
            [

            ]
        );
    }
}
