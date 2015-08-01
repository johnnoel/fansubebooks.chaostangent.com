<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;
use ChaosTangent\ASS\Reader,
    ChaosTangent\ASS\Line\Dialogue;
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
        $this->setName('fansubebooks:files:add')
            ->setDescription('Add file(s) to Fansub Ebooks')
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

        $fileRepo = $om->getRepository('Entity:File');
        $filePaths = $input->getArgument('files');
        $files = [];

        foreach ($filePaths as $file) {
            $splFile = new \SplFileInfo($file);
            if (!$splFile->isFile() || !$splFile->isReadable()) {
                $output->writeln('<error>Unable to read file: '.$file.', omitting</error>');
            } else if ($fileRepo->fileWithNameExists($splFile->getFilename())) {
                $output->writeln('<info>File with name '.$file.' already exists, omitting</info>');
            } else {
                $files[] = $splFile;
            }
        }

        $reader = new Reader();

        foreach ($files as $file) {
            $f = new File();
            $f->setName($file->getFilename())
                ->setSeries($series);

            // parse file
            $script = $reader->fromFile($file->getPathname());

            // check script has the "Events" block
            if (!$script->hasBlock('Events')) {
                $output->writeln('<info>No events block in '.$file->getFilename().', skipping</info>');
                foreach ($script as $block) {
                    $output->writeln($block->getId());
                }
                continue;
            }

            $events = $script->getBlock('Events');
            // gather all of the Dialogue lines from Events block
            foreach ($events as $line) {
                if (!($line instanceof Dialogue)) {
                    continue;
                }

                $text = trim($line->getTextWithoutStyleOverrides());
                if (empty($text)) {
                    continue;
                }

                $l = new Line();
                $l->setLine($text)
                    ->setCharacterCount(mb_strlen($text));
                $f->addLine($l);
            }

            // generate a hash for the script based on sorted dialogue lines
            $lines = array_map(function($a) {
                return $a->getLine();
            }, $f->getLines()->toArray());

            sort($lines);
            $hash = hash('sha256', implode('', $lines));

            // last check for a file that already exists (same script,
            // different filename)
            if ($fileRepo->fileWithHashExists($hash)) {
                $output->writeln('<info>File '.$file->getFilename().' already exists (hash check), skipping</info>');
                continue;
            }

            $f->setHash($hash);

            // store!
            $om->persist($f);
            $om->flush();
        }
    }
}
