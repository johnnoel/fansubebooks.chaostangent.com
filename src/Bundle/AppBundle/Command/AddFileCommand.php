<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;
use ChaosTangent\FansubEbooks\Entity\File,
    ChaosTangent\FansubEbooks\Entity\Line;

/**
 * Add file command
 *
 * Parse and add a script file
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class AddFileCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fansubebooks:scripts:add')
            ->setDescription('Add script(s) to Fansub Ebooks')
            ->addArgument('series', InputArgument::REQUIRED, 'Series alias / ID')
            ->addArgument('files', InputArgument::IS_ARRAY, 'Script file(s)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $seriesId = $input->getArgument('series');

        $om = $this->getContainer()->get('doctrine')->getManager();
        $seriesRepo = $om->getRepository('Entity:Series');

        $series = $seriesRepo->findOneByAlias($seriesId);
        if ($series === null) {
            $series = $seriesRepo->find($seriesId);

            if ($series === null) {
                $output->writeln('<error>Could not find series with alias/ID: '.$seriesId.'</error>');
                return;
            }
        }

        $filePaths = $input->getArgument('files');
        $files = [];

        foreach ($filePaths as $file) {
            $splFile = new \SplFileInfo($file);
            if (!$splFile->isFile() || !$splFile->isReadable()) {
                $output->writeln('<error>Unable to read file: '.$file.'</error>');
            } else {
                $files[] = $splFile;
            }
        }

        $assReader = $this->getContainer()->get('fansubebooks.ass_reader');

        foreach ($files as $file) {
            $f = new File();
            $f->setName($file->getFilename())
                ->setSeries($series);

            //try {
            $assFile = $assReader->parse(file_get_contents($file->getPathname()));
            //} catch (InvalidAssFileException $e) {
            //}

            foreach ($assFile->lines as $line) {
            }
        }
    }
}
