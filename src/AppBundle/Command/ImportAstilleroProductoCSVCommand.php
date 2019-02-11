<?php
/**
 * User: inrumi
 * Date: 7/23/18
 * Time: 15:21
 */

namespace AppBundle\Command;


use AppBundle\Entity\Astillero\Producto;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveProdServ;
use AppBundle\Entity\Contabilidad\Facturacion\Concepto\ClaveUnidad;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportAstilleroProductoCSVCommand extends Command
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
            ->setName('csv:import:astillero-producto')
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
            $claveUnidad = $this->em->getRepository(ClaveUnidad::class)
                ->findOneBy([
                    'claveUnidad' => $record['CLAVE UNIDAD'],
                ]);
            $claveProdServ = $this->em->getRepository(ClaveProdServ::class)
                ->findOneBy([
                    'claveProdServ' => $record['CLAVE PRODUCTO'],
                ]);

            $producto = new Producto();

            $producto->setIdentificador($record['CODIGO']);
            $producto->setClaveUnidad($claveUnidad);
            $producto->setClaveProdServ($claveProdServ);
            $producto->setNombre($record['NOMBRE']);
            $producto->setUnidad($record['UNIDAD']);
            $producto->setPrecio($record['PRECIO'] * 100);
            $producto->setExistencia($record['EXISTENCIAS']);

            $this->em->persist($producto);

            $io->progressAdvance();
        }

        $io->progressFinish();

        $this->em->flush();

        $io->success('El archivo ha sido importado.');
    }
}
