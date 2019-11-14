<?php

/**
 * @author  Baha2r
 * @license MIT
 * Date: 12/Nov/2019 23:14 PM
 *
 * SetMasterKeyCommand class provides a system to generate master key
 **/

namespace PHPGuard\Console;


use Redis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use PHPGuard\Console\EventHandler\InputListener;
use PHPGuard\Console\EventHandler\InputEvent;
use PHPGuard\Hash\Hash;

class SetMasterKeyCommand extends Command
{

    /**
     * @var string|null Master key
     */
    private $master;

    /**
     * @var Redis Store master key in memory
     */
    private $redis;

    /**
     * @param  string|null  $name  The name of the command. The default name is null, it means it must be set in configure()
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->redis = new Redis();
        $this->redis->connect("127.0.0.1");
    }


    /**
     * Configures requirements for this command
     */
    protected function configure(): void
    {
        $this->setName("setAdminKey")
                ->setDescription("Set admin key for using Guard cryptography system")
                ->setHelp("<comment>\nSet admin key for using Guard cryptography system. It uses admin key to generate master key due to protect your data.\n</comment>");
        parent::configure();
    }


    /**
     * Executes this command
     *
     * @param  InputInterface   $input
     * @param  OutputInterface  $output
     *
     * @throws RuntimeException Throws runtime exception if it fails to set master key
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln("");
        $output->writeln("<question>>>> Welcome to Guard cryptography system</question><comment>  v2.0.0</comment>");
        $output->writeln("");
        $output->writeln("<question>>>> Please enter your admin key:</question>");
        $output->writeln("");
        $dispatcher = new EventDispatcher();
        $listener = new InputListener();
        $dispatcher->addListener(InputEvent::EVENT_NAME, array($listener, "onInputEvent"));
        $dispatcher->dispatch(new InputEvent(), InputEvent::EVENT_NAME);
        $this->master = Hash::sha512("ژ‍‍‍‍‍`گ ث".InputListener::$adminKey."ءو ؛");
        if (is_null($this->master)) {
            throw new RuntimeException("[RuntimeException]:\n\nFailed to set master key!\n");
        }
        $isInserted = $this->redis->set(Hash::sha256(["This", "Is", "?", "!"], true), $this->master);
        if (!$isInserted) {
            throw new RuntimeException("Master key generated successfully, but we can not insert it in memory heap!");
        }
        $output->writeln("");
        $output->writeln("<info>>>> Master key generated successfully!\n</info>");
    }
}