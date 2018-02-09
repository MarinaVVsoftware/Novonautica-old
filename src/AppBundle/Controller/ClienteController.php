<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Barco;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\Motor;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Cliente controller.
 *
 * @Route("cliente")
 */
class ClienteController extends Controller
{
    /**
     * Lists all cliente entities.
     *
     * @Route("/", name="cliente_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $datatables = $this->get('datatables');
                $results = $datatables->handle($request, 'cliente');
                return $this->json($results);
            } catch (HttpException $e) {
                return $this->json($e->getMessage(), $e->getCode());
            }
        }

        return $this->render('cliente/index.html.twig', ['title' => 'Clientes']);
    }

    /**
     * @Route("/empresas.{_format}", defaults={"_format" = "json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getEmpresasAction(Request $request)
    {
        $empresas = $this->getDoctrine()->getRepository('AppBundle:Cliente')->findEmpresas();
        return new Response($this->serializeEntities($empresas, $request->getRequestFormat(), [
            'barcos',
            'monederomovimientos',
            'mhcotizaciones',
            'mhcotizacionesadicionales',
            'astillerocotizaciones'
        ]));
    }

    /**
     * El campo factura de clientes utilizara este metodo para conseguir la informacion
     * de un cliente a traves de su RFC
     *
     * @Route("/listado.{_format}", defaults={"_format"="json"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getClientesLike(Request $request)
    {
        $q = $request->query->get('rfc');
        $rfcs = $this->getDoctrine()->getRepository('AppBundle:Cliente\RazonSocial')->findLikeRfc($q);

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);

        $obj = $serializer->serialize($rfcs, $request->getRequestFormat(), [
            'attributes' => [
                'rfc',
                'razonSocial',
                'direccion',
                'correos',
                'cliente' => [
                    'nombre',
                    'telefono'
                ]
            ]
        ]);

        return new Response($obj);
    }

    /**
     * Creates a new cliente entity.
     *
     * @Route("/nuevo", name="cliente_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $cliente = new Cliente();
        $barco = new Barco();
        $motor = new Motor();

        $cliente->setEstatus(true);
        $barco->setEstatus(true);

        $cliente->addBarco($barco);
        $barco->addMotore($motor);

        $form = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $razonesSociales = $cliente->getRazonesSociales();

            /** @var Cliente\RazonSocial $rs */
            foreach ($razonesSociales as $rs) {
                $rs->setCorreos(preg_replace('/;/', ',', $rs->getCorreos()));
            }

            $fechaHoraActual = new \DateTime('now');

            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 8; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            $barco->setFecharegistro($fechaHoraActual);
            $cliente->setFecharegistro($fechaHoraActual)
                ->setPassword($randomString);

            $em->persist($cliente);
            $em->flush();


            $message = (new \Swift_Message('¡Has sido dado de alta en NovoNautica!'))
                ->setFrom('noresponder@novonautica.com')
                ->setTo($cliente->getCorreo())
                ->setBcc('admin@novonautica.com')
                ->setBody(
                    $this->renderView(':cliente:correo.alta.twig', [
                        'correo' => $cliente->getCorreo(),
                        'password' => $randomString
                    ]),
                    'text/html'
                );
            $mailer->send($message);

            return $this->redirectToRoute('cliente_show', ['id' => $cliente->getId()]);

        }

        return $this->render('cliente/new.html.twig', [
            'title' => 'Nuevo cliente',
            'cliente' => $cliente,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a cliente entity.
     *
     * @Route("/{id}", name="cliente_show")
     * @Method("GET")
     */
    public function showAction(Cliente $cliente)
    {
        $deleteForm = $this->createDeleteForm($cliente);
        //$barcos = $cliente->getBarcos();

        return $this->render('cliente/show.html.twig', [
            'title' => 'Cliente',
            'cliente' => $cliente,
            'delete_form' => $deleteForm->createView()
        ]);
    }

    /**
     * Displays a form to edit an existing cliente entity.
     *
     * @Route("/{id}/editar", name="cliente_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Cliente $cliente)
    {
        $barcos = $cliente->getBarcos();
        $barcomotores = [];

        $em = $this->getDoctrine()->getManager();

        foreach ($barcos as $barco) {
            $barco = $em->getRepository(Barco::class)->find($barco->getId());
            $cliente = $barco->getCliente();
            if (!$barco) {
                throw $this->createNotFoundException('No hay barcos encontrados para el id ' . $barco->getId());
            }
            $originalMotores = new ArrayCollection();

            foreach ($barco->getMotores() as $motor) {
                $originalMotores->add($motor);
            }
            $barcomotores[$barco->getId()] = $originalMotores; //guardamos en el arreglo la coleccion de motores correspondiente a su id de barco
        }

        $originalRs = new ArrayCollection();

        foreach ($cliente->getRazonesSociales() as $rs) {
            $originalRs->add($rs);
        }

        $deleteForm = $this->createDeleteForm($cliente);
        $editForm = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($barcos as $barco) {
                $om = $barcomotores[$barco->getId()]; //extraemos la coleccion de motores del barco correspondiente
                foreach ($om as $motor) {
                    if (false === $barco->getMotores()->contains($motor)) {
                        // remove the Task from the Tag
                        $motor->getBarco()->removeMotore($motor);
                        // if it was a many-to-one relationship, remove the relationship like this
                        $motor->setBarco(null);
                        $em->persist($motor);
                        // if you wanted to delete the Tag entirely, you can also do that
                        $em->remove($motor);
                    }
                    $em->persist($barco);
                }
            }

            foreach ($originalRs as $ors) {
                if ($cliente->getRazonesSociales()->contains($ors) === false) {
                    $ors->setCliente(null);
                    $em->remove($ors);
                }
            }

            $em->flush();

            // redirect back to some edit page
            return $this->redirectToRoute('cliente_show', ['id' => $cliente->getId()]);
        }

        return $this->render('cliente/edit.html.twig', [
            'title' => 'Editar cliente',
            'cliente' => $cliente,
            'barcos' => $barcos,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a cliente entity.
     *
     * @Route("/{id}", name="cliente_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Cliente $cliente)
    {
        $form = $this->createDeleteForm($cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cliente);
            $em->flush();
        }

        return $this->redirectToRoute('cliente_index');
    }

    private function serializeEntities($entity, $format, $ignoredAttributes = []): string
    {
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [new JsonEncoder(), new XmlEncoder()]);
        $normalizer->setIgnoredAttributes($ignoredAttributes);

        return $serializer->serialize($entity, $format);
    }

    /**
     * Creates a form to delete a cliente entity.
     *
     * @param Cliente $cliente The cliente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cliente $cliente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cliente_delete', array('id' => $cliente->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

}
