<?php
/**
 * User: inrumi
 * Date: 8/9/18
 * Time: 16:42
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Pincode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PincodeController extends Controller
{
    /**
     * @var ServiceEntityRepositoryInterface
     */
    private $entityRepository;

    public function __construct(
        EntityManagerInterface $entityRepository
    ) {
        $this->entityRepository = $entityRepository;
    }

    /**
     * @Route("/generate-pincode", name="generate-pincode")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return JsonResponse
     */
    public function getPinAction(Request $request)
    {
        $desciption= $request->query->get('description');
        $pinCode = new Pincode($this->generatePin());
        $pinCode->setCreatedBy($this->getUser());
        $pinCode->setDescription($desciption);
        $this->entityRepository->persist($pinCode);
        $this->entityRepository->flush();

        return new JsonResponse(
            [
                'status' => 'success',
                'data' => [
                    'pincode' => $pinCode
                ],
            ],
            JsonResponse::HTTP_OK
        );
    }

    private function generatePin()
    {
        $pin = strtoupper(bin2hex(random_bytes(4)));

        $duped = $this->entityRepository
            ->getRepository(Pincode::class)
            ->findOneBy(['pin' => $pin]);

        if ($duped) {
            return self::generatePin();
        }

        return $pin;
    }

}
