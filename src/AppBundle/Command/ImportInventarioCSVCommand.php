<?php
/**
 * User: inrumi
 * Date: 8/23/18
 * Time: 13:52
 */

namespace AppBundle\Command;


use AppBundle\Entity\Tienda\Inventario\Registro;
use AppBundle\Entity\Tienda\Producto;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportInventarioCSVCommand extends Command
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
            ->setName('csv:import:tienda-inventario')
            ->setDescription('Importa un archivo CSV para el primer inventario de tienda')
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

        $io->title('Importando el archivo...');

        if (!ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        $reader = Reader::createFromPath($file, 'r');

        $head = $reader->getHeader();
        $records = $reader->getRecords();

        $registro = new Registro();
        $registro->setFecha(new \DateTime());
        $registro->setReferencia('Primera carga de inventario');
        $registro->setTipo(Registro::TIPO_ENTRADA);
        $registro->setTotal(0);

        $io->progressStart(iterator_count($records));

        foreach ($records as $record) {
            $producto = $this->em
                ->getRepository(Producto::class)
                ->findOneBy([
                    'nombre' => $record[1]
                ]);

            if ($producto) {
                $entrada = new Registro\Entrada();
                $entrada->setCantidad($record[3]);
                $entrada->setImporte(0);
                $entrada->setRegistro($registro);
                $entrada->setProducto($producto);

                $this->em->persist($entrada);
            }

            $io->progressAdvance();
        }

        $io->progressFinish();

        $this->em->flush();

        $io->success('El archivo ha sido importado.');
    }
}
