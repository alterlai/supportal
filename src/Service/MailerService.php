<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class MailerService
{
    private $mailer;
    private $fromAddress;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, ParameterBagInterface $parameterBag, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->fromAddress = $parameterBag->get('no-replyAddress');
        $this->twig = $twig;
    }

    /**
     * @param string $to
     * @param string $documentName
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendUploadSuccessMail(string $to, string $documentName)
    {
        $mail = (new \Swift_Message())
            ->setFrom($this->fromAddress)
            ->setTo($to)
            ->setSubject("Bevestiging document upload IQSupport BV.")
            ->setBody($this->twig->render('email/upload_success.html.twig',
                ['documentName' => $documentName])
            );

        $this->mailer->send($mail);
    }
}