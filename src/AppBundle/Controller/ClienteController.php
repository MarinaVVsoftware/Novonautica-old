<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Barco;
use AppBundle\Entity\Cliente;
use AppBundle\Repository\ClienteRepository;
use DataTables\DataTablesInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * @Security("has_role('ROLE_CLIENTE')")
     *
     * @return JsonResponse|Response
     */
    public function indexAction()
    {
        return $this->render('cliente/index.html.twig', ['title' => 'Clientes']);
    }

    /**
     * @Route("/clientes", name="cliente_index_data")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function getClientesDataAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'cliente');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Creates a new cliente entity.
     *
     * @Route("/nuevo", name="cliente_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $cliente = new Cliente();
        $barco = new Barco();

        $this->denyAccessUnlessGranted('CLIENTE_CREATE', $cliente);

        $cliente->setEstatus(true);
        $barco->setEstatus(true);

        $cliente->addBarco($barco);

        $form = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $razonesSociales = $cliente->getRazonesSociales();

            /** @var Cliente\RazonSocial $rs */
            foreach ($razonesSociales as $rs) {
                $rs->setCorreos(preg_replace('/;\s+/', ',', $rs->getCorreos()));
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
                        'password' => $randomString,
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
     *
     * @param Cliente $cliente
     *
     * @return Response
     */
    public function showAction(Cliente $cliente)
    {
        return $this->render('cliente/show.html.twig', [
            'title' => "Cliente: {$cliente->getNombre()}",
            'cliente' => $cliente,
        ]);
    }

    /**
     * @Route("/reportes/adeudos", name="cliente_show_data_adeudos")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function getShowDataAdeudosTableAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'clienteReporteAdeudos');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * @Route("/reportes/abonos", name="cliente_show_data_abonos")
     *
     * @param Request $request
     * @param DataTablesInterface $dataTables
     *
     * @return JsonResponse
     */
    public function getShowDataAbonosTableAction(Request $request, DataTablesInterface $dataTables)
    {
        try {
            $results = $dataTables->handle($request, 'clienteReporteAbonos');

            return $this->json($results);
        } catch (HttpException $e) {
            return $this->json($e->getMessage(), $e->getStatusCode());
        }
    }


    /**
     * Displays a form to edit an existing cliente entity.
     *
     * @Route("/{id}/editar", name="cliente_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Cliente $cliente
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Cliente $cliente)
    {
        $this->denyAccessUnlessGranted('CLIENTE_EDIT', $cliente);

        $barcos = $cliente->getBarcos();
        $barcomotores = [];

        $em = $this->getDoctrine()->getManager();

        foreach ($barcos as $barco) {
            $barco = $em->getRepository(Barco::class)->find($barco->getId());
            $cliente = $barco->getCliente();
            if (!$barco) {
                throw $this->createNotFoundException('No hay barcos encontrados para el id '.$barco->getId());
            }
            $originalMotores = new ArrayCollection();

            foreach ($barco->getMotores() as $motor) {
                $originalMotores->add($motor);
            }
            $barcomotores[$barco->getId()] = $originalMotores; //guardamos en el arreglo la coleccion de motores correspondiente a su id de barco
        }

        $clonedRazonesSociales = $cliente->getRazonesSociales()->toArray();
        $clonedCuentasBancarias = $cliente->getCuentasBancarias()->toArray();

        $deleteForm = $this->createDeleteForm($cliente);
        $editForm = $this->createForm('AppBundle\Form\ClienteType', $cliente);
        $editForm->handleRequest($request);
        try {
            if ($editForm->isSubmitted() && $editForm->isValid()) {
                foreach ($barcos as $barco) {
                    $om = $barcomotores[$barco->getId()]; //extraemos la coleccion de motores del barco correspondiente
                    foreach ($om as $motor) {
                        if (false === $barco->getMotores()->contains($motor)) {
                            $motor->getBarco()->removeMotore($motor);
                            $motor->setBarco(null);
                            $em->persist($motor);
                            $em->remove($motor);
                        }

                        $em->persist($barco);
                    }
                }

                foreach ($clonedRazonesSociales as $clonedRazonSocial) {
                    $clonedRazonSocial->setCorreos(
                        preg_replace('/;\s+/', ',', $clonedRazonSocial->getCorreos())
                    );

                    if (!$cliente->getRazonesSociales()->contains($clonedRazonSocial)) {
                        $em->remove($clonedRazonSocial);
                    }
                }

                foreach ($clonedCuentasBancarias as $clonedCuentasBancaria) {
                    if (!$cliente->getCuentasBancarias()->contains($clonedCuentasBancaria)) {
                        $em->remove($clonedCuentasBancaria);
                    }
                }

                $em->flush();

                return $this->redirectToRoute('cliente_show', ['id' => $cliente->getId()]);
            }
        } catch (\Exception $e) {
            $this->addFlash('notice', 'Error!');
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
     *
     * @param Request $request
     * @param Cliente $cliente
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Cliente $cliente)
    {
        $this->denyAccessUnlessGranted('CLIENTE_DELETE', $cliente);

        $form = $this->createDeleteForm($cliente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cliente);
            $em->flush();
        }

        return $this->redirectToRoute('cliente_index');
    }

    /**
     * Creates a form to delete a cliente entity.
     *
     * @param Cliente $cliente The cliente entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Cliente $cliente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cliente_delete', ['id' => $cliente->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }

}
