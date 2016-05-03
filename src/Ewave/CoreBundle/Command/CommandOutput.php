<?php

namespace Ewave\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CommandOutput extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var bool
     */
    private $firstRun = true;


    /**
     * @var EntityManager
     */
    private $entityManager = null;


    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function load(OutputInterface $output)
    {
        if ($this->firstRun === true) {
            $styles = array(
                'error' => new OutputFormatterStyle('red', null, array()),
                'critical' => new OutputFormatterStyle('black', 'red', array()),
                'list' => new OutputFormatterStyle('yellow', null, array()),
                'success' => new OutputFormatterStyle('green', null, array('bold')),
                'bold' => new OutputFormatterStyle(null, null, array('bold')),
            );

            $this->connect($output);

            foreach ($styles as $key => $style) {
                $output->getFormatter()->setStyle($key, $style);
            }
            $output->writeln('');
            $output->writeln('<success>                          Parser Console                          </success>');
            $output->writeln('');

            $this->firstRun = false;
        }
    }

    /**
     * Simply connects the output to this sub module
     *
     * @param OutputInterface $output
     */
    protected function connect(OutputInterface &$output)
    {
        $this->output = $output;
    }

    /**
     * @param $response
     */
    protected function returnResponse($response)
    {
        $output = array();

        foreach ($response as $key => $var) {
            $output[] = urlencode($key) . '=' . urlencode($var);
        }

        return $this->output->writeln(implode('&', $output));
    }

    protected function writeLine($text)
    {
        $this->output->writeln($text);
    }

    protected function writeErrorLine($text)
    {
        $this->output->writeln('<error>  ' . $text . '  </error>');
    }

    protected function writeSuccessLine($text)
    {
        $this->output->writeln('<success>  ' . $text . '  </success>');
    }

    protected function writeListLine($text)
    {
        $this->output->writeln('<list>  ' . $text . '  </list>');
    }

    /**
     * @param string $id
     * @return object
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getParam($key)
    {
        return $this->getContainer()->getParameter($key);
    }

    /**
     * @return EntityManager
     */
    protected function getManager()
    {
        if (is_null($this->entityManager)) {
            $this->entityManager = $this->get('doctrine')->getManager();
        }

        return $this->entityManager;
    }

    /**
     * @param string $entity
     * @param string $prefix
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($entity, $prefix = 'EwaveCoreBundle')
    {
        return $this->getManager()->getRepository($prefix.':'.$entity);
    }
}
