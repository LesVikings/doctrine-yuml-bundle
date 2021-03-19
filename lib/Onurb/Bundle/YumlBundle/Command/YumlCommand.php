<?php
namespace Onurb\Bundle\YumlBundle\Command;

use Onurb\Bundle\YumlBundle\Yuml\YumlClientInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate and save Yuml images for metadata graphs.
 *
 * @license MIT
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class YumlCommand extends Command
{
    protected static $defaultName = 'yuml:mappings';

    /**
     * @var YumlClientInterface
     */
    private $client;

    /**
     * @var array
     */
    private $config;

    public function __construct(YumlClientInterface $client, array $config)
    {
        parent::__construct();

        $this->client = $client;
        $this->config = $config;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate an image from yuml.me of doctrine metadata')
            ->addOption(
                'filename',
                'f',
                InputOption::VALUE_REQUIRED,
                'Output filename',
                'yuml-mapping.png'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getOption('filename');

        $showDetailParam    = $this->config['show_fields_description'];
        $colorsParam        = $this->config['colors'];
        $notesParam         = $this->config['notes'];
        $styleParam         = $this->config['style'];
        $extensionParam     = $this->config['extension'];
        $direction          = $this->config['direction'];
        $scale              = $this->config['scale'];

        $graphUrl = $this->client->getGraphUrl(
            $this->client->makeDslText($showDetailParam, $colorsParam, $notesParam),
            $styleParam,
            $extensionParam,
            $direction,
            $scale
        );
        $this->client->downloadImage($graphUrl, $filename);

        $output->writeln(sprintf('Downloaded <info>%s</info> to <info>%s</info>', $graphUrl, $filename));

        return 0;
    }
}
