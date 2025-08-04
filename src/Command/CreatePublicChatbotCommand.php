<?php

namespace App\Command;

use App\Entity\PublicChatbot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-public_chatbot',
    description: 'Creates the default public chatbot if not exists',
)]
class CreatePublicChatbotCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $repo = $this->em->getRepository(PublicChatbot::class);
        $existing = $repo->findOneBy([]); // Assumes only one allowed

        if ($existing) {
            $io->warning("Public chatbot already exists.");
            return Command::SUCCESS;
        }

        $chatbot = new PublicChatbot();
        $chatbot->setName('VPilot');
        $chatbot->setapiKey('sk-...');
        $chatbot->setAssistantId('asst_...');
        $chatbot->setModel('gpt-4o-mini');
        $chatbot->setIconUrl('/assets/images/chatbot/chatbot.gif');
        $chatbot->setRenderEveryPages(true);
        $chatbot->setFontColor1('#ffffffff');
        $chatbot->setFontColor2('#ffffffff');
        $chatbot->setMainColor('#306285');
        $chatbot->setSecondaryColor('#616161ff');
        $chatbot->setWelcomeMessage('Bonjour! Je suis VPilot, comment puis-je vous aider sur la documentation de Visual Planning ?');
        $chatbot->setPromptMessage('Posez-moi vos question.');
        $chatbot->setPosition('right');
        $chatbot->setShowDesktop(true);
        $chatbot->setShowMobile(true);
        $chatbot->setShowTablet(true);

        $this->em->persist($chatbot);
        $this->em->flush();

        $io->success('Public chatbot created successfully.');

        return Command::SUCCESS;
    }
}