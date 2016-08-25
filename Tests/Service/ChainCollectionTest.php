<?php

namespace Borovets\ChainCommandBundle\Tests\Service;


use Borovets\ChainCommandBundle\Service\ChainCollection;
use Borovets\ChainCommandBundle\Tests\Fixtures\Commands\HelloCommand;
use Borovets\ChainCommandBundle\Tests\Fixtures\Commands\Hi2Command;
use Borovets\ChainCommandBundle\Tests\Fixtures\Commands\HiCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tests\Command\CommandTest;

class ChainCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ChainCollection */
    protected $chainCollection;

    public function setUp()
    {
        $this->chainCollection = new ChainCollection();

        $this->chainCollection->addCommand('foo:hello', 0, new HiCommand());
    }

    public function correctChain()
    {
        return [
            ['foo:hello', 0, new HelloCommand()]
        ];
    }

    /**
     * @dataProvider correctChain
     */
    public function testAddCommand($mainCommand, $priority, $command)
    {
        $this->assertNull($this->chainCollection->addCommand($mainCommand, $priority, $command));
    }

    public function testHasChain()
    {
        $this->assertTrue($this->chainCollection->hasChain('foo:hello'));
        $this->assertFalse($this->chainCollection->hasChain('bar:hi'));
    }

    public function testIsChainedCommand()
    {
        $this->assertFalse($this->chainCollection->isChainedCommand('foo:hello'));
        $this->assertTrue($this->chainCollection->isChainedCommand('bar:hi'));
    }

    public function testGetMainChainName()
    {
        $this->assertEquals('foo:hello', $this->chainCollection->getMainChainName('bar:hi'));
        $this->assertFalse($this->chainCollection->getMainChainName('foo:hello'));
    }

    public function testChainSubCommands()
    {
        $firstCommand = new HiCommand();
        $secondCommand = new Hi2Command();

        $chainCollection = new ChainCollection();
        $chainCollection->addCommand('foo:hello', 0, $firstCommand);
        $chainCollection->addCommand('foo:hello', 10, $secondCommand);

        $result = $chainCollection->getChainSubCommands('foo:hello');
        $mustBe = [
            ['commandName' => 'bar:hi', 'priority' => 0, 'command' => $firstCommand],
            ['commandName' => 'bar:hi2', 'priority' => 10, 'command' => $secondCommand],
        ];

        $this->assertEquals($mustBe, $result);
    }
}
