<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class MailerService
{
    private $mailer;
    private $no_replyAddress;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, ParameterBagInterface $parameterBag, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->no_replyAddress = $parameterBag->get('no-reply_address');
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
            ->setFrom($this->no_replyAddress)
            ->setTo($to)
            ->setSubject("Bevestiging document upload IQSupport BV.")
            ->setBody($this->twig->render(
                'email/upload_success.html.twig',
                ['documentName' => $documentName]
            ),
                'text/html'
            );

        $this->mailer->send($mail);
    }

    /**
     * Send monthly user report files.
     * @param string $to
     * @param string $reportFilePath
     * @throws \Exception
     */
    public function sendUserReportMail(string $to, string $reportFilePath)
    {
        $today = (new \DateTime("now"))->format("d-m-Y");
        $mail = (new \Swift_Message())
            ->setFrom($this->no_replyAddress)
            ->setTo($to)
            ->setSubject("Klantrapportage Tekeningen Portaal | $today")
            ->attach(\Swift_Attachment::fromPath($reportFilePath));

        $this->mailer->send($mail);
    }
}