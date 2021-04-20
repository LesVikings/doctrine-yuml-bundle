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
    /**
     * @var \Onurb\Bundle\YumlBundle\Yuml\YumlClientInterface
     */
    private $yumlClient;
    /**
     * @var bool
     */
    private $showFileDescription;
    /**
     * @var array
     */
    private $colors;
    /**
     * @var array
     */
    private $notes;
    /**
     * @var string
     */
    private $style;
    /**
     * @var string
     */
    private $direction;
    /**
     * @var string
     */
    private $scale;
    /**
     * @var string
     */
    private $extension;

    public function __construct (
        YumlClientInterface $yumlClient,
        bool $showFileDescription = true,
        array $colors = [],
        array $notes = [],
        string $style = 'plain',
        string $extension = 'png',
        string $direction = 'TB',
        string $scale = 'normal'
    )
    {
        parent::__construct();
        $this->yumlClient          = $yumlClient;
        $this->showFileDescription = $showFileDescription;
        $this->colors              = $colors;
        $this->notes               = $notes;
        $this->style               = $style;
        $this->direction           = $direction;
        $this->scale               = $scale;
        $this->extension = $extension;
    }

    protected function configure()
    {
        $this->setName('yuml:mappings')
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

        $graphUrl = $this->yumlClient->getGraphUrl(
            $this->yumlClient->makeDslText($this->showFileDescription, $this->colors, $this->notes),
            $this->style,
            $this->extension,
            $this->direction,
            $this->scale
        );
        $this->yumlClient->downloadImage($graphUrl, $filename);

        $output->writeln(sprintf('Downloaded <info>%s</info> to <info>%s</info>', $graphUrl, $filename));

        return 0;
    }
}
