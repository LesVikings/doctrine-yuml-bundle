<?php
namespace OnurbTest\Bundle\YumlBundle\Command;

use Onurb\Bundle\YumlBundle\Command\YumlCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Tester\CommandTester;

class YumlCommandTest extends TestCase
{
    const YUML_LINK = 'https://yuml.me/15a98c92.png';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var YumlCommand
     */
    private $command;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CommandTester
     */
    private $commandTester;

    public function setUp()
    {
        parent::setUp();

        $yumlClient = $this->createMock('Onurb\\Bundle\\YumlBundle\\Yuml\\YumlClientInterface');

        $yumlClient->expects($this->once())
            ->method('makeDslText')
            ->will($this->returnValue('[Simple.Entity|+a;b;c]'));

        $yumlClient->expects($this->once())
            ->method('getGraphUrl')
            ->will($this->returnValue(self::YUML_LINK));

        $this->application = new Application();
        $this->application->add(new YumlCommand($yumlClient, [
            'show_fields_description' => false,
            'colors' => [],
            'notes' => [],
            'extension' => 'png',
            'style' => 'plain',
            'direction' => 'TB',
            'scale' => 'normal',
        ]));
        $this->command = $this->application->find('yuml:mappings');

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @covers \Onurb\Bundle\YumlBundle\Command\YumlCommand
     */
    public function testExecute()
    {
        $this->commandTester->execute([
            'command'   => $this->command->getName()
        ]);

        $this->assertRegExp('/.../', $this->commandTester->getDisplay());
        $this->assertSame('Downloaded', explode(' ', $this->commandTester->getDisplay())[0]);
    }
}
