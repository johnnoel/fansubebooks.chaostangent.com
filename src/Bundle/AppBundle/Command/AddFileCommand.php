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
        $filePaths = $input->getArgument('files');

        if (count($filePaths) === 0) {
            $output->writeln('<error>Missing files argument</error>');
            return;
        }

        $seriesId = $input->getArgument('series');

        $om = $this->getContainer()->get('doctrine')->getManager();
        $seriesRepo = $om->getRepository('Entity:Series');

        $series = $seriesRepo->findOneByAlias($seriesId);
        if ($series === null && intval($seriesId) > 0) {
            $series = $seriesRepo->find($seriesId);
        }

        if ($series === null) {
            $output->writeln('<error>Could not find series with alias/ID: '.$seriesId.'</error>');
            return;
        }

        $fileRepo = $om->getRepository('Entity:File');
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

            $timeStart = microtime(true);

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
            // keep a rolling buffer of lines, ensures no repeats due to
            // style overrides and whatnot
            $buffer = [];

            // gather all of the Dialogue lines from Events block
            // todo check $events is time ordered
            foreach ($events as $line) {
                if (!($line instanceof Dialogue)) {
                    continue;
                }

                $text = trim($line->getVisibleText());
                if (empty($text)) {
                    continue;
                }

                // if we've seen this line in the last x unique lines, ignore it
                if (in_array($text, $buffer)) {
                    continue;
                }

                // keep up to 10 lines
                $buffer[] = $text;
                if (count($buffer) > 10) {
                    array_shift($buffer);
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

            $timeTaken = microtime(true) - $timeStart;

            $output->writeln(sprintf('Successfully read <info>%d</info> lines from <comment>%s</comment> in %fs',
                count($lines),
                $file->getFilename(),
                $timeTaken
            ));
        }
    }
}
