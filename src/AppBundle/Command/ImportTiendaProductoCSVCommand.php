<?php
/**
 * User: inrumi
 * Date: 7/23/18
 * Time: 15:21
 */

namespace AppBundle\Command;


use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use AppBundle\Entity\Tienda\Producto;
use AppBundle\Entity\Tienda\Producto\Categoria;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportTiendaProductoCSVCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('csv:import:tienda-producto')
            ->setDescription('Importa un archivo CSV para productos de una tienda')
            ->addOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED,
                'Ubicación del archivo'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $file = $input->getOption('file');

        if (!$file) {
            throw new \RuntimeException('Debes introducir la ubicación del csv.');
        }

        $io->title('Intentando importar el archivo...');

        $reader = Reader::createFromPath($file, 'r');
        $reader->setHeaderOffset(0);

        $head = $reader->getHeader();
        $records = $reader->getRecords();

        $io->progressStart(iterator_count($records));

        foreach ($records as $record) {
            $claveProdServ = $this->em->getRepository(ClaveProdServ::class)
                ->findOneBy([
                    'claveProdServ' => $record['CLAVE UNIDAD'],
                ]);
            $claveUnidad = $this->em->getRepository(ClaveUnidad::class)
                ->findOneBy([
                    'claveUnidad' => $record['CLAVE PRODUCTO'],
                ]);
//            $categoria = $this->em->getRepository(Categoria::class)
//                ->findOneBy([
//                    'nombre' => $record['categoria']
//                ]);

            $producto = new Producto();

            $producto->setCodigoBarras($record['CODIGO']);
            $producto->setNombre($record['NOMBRE']);

            $producto->setPrecio($record['PRECIO']);
            $producto->setPreciocolaborador($record['PRECIO COLABORADOR']);
            $producto->setClaveProdServ($claveProdServ);
            $producto->setClaveUnidad($claveUnidad);
            $producto->setExistencia($record['EXISTENCIAS']);
            $producto->setIESPS(0);
            $producto->setIVA(0);

            $this->em->persist($producto);

            $io->progressAdvance();
        }

        $io->progressFinish();

        $this->em->flush();

        $io->success('El archivo ha sido importado.');
    }
}
