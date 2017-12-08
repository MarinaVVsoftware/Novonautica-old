<?php
/**
 * Created by PhpStorm.
 * User: inrumi
 * Date: 11/28/17
 * Time: 10:41
 */

namespace AppBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Oneup\UploaderBundle\Event\PostPersistEvent;

class UploadListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onUpload(PostPersistEvent $event)
    {
        $response = $event->getResponse();
        $response['baseFileName'] = $event->getFile()->getBaseName();

        return $response;
    }
}