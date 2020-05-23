<?php

/**
 * Service that can be used to send email using basic swift mailer transport
 */

namespace App\Mybase\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Mailer
{
    /**
    * @var array $emailConfig
    */
    private $emailConfig;

    /**
     * Mailer constructor
     * 
     * @param ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params)
    {
        $this->emailConfig = $params->get('mailer');
    }

    public function sendMail(array $mailInfo, string $html, array $files = [])
    {
        $emailConfig = $this->emailConfig;

        $smtpHost   = $emailConfig['smtp_host'];
        $smtpPort   = $emailConfig['smtp_port'];
        $smtpCert   = $emailConfig['smtp_cert'];
        $smtpUsername   = $emailConfig['smtp_username'];
        $smtpPassword   = $emailConfig['smtp_password'];

        $transport = (new \Swift_SmtpTransport($smtpHost, $smtpPort, $smtpCert))
            ->setUsername($smtpUsername)
            ->setPassword($smtpPassword)
        ;

        $swiftMailer = new \Swift_Mailer($transport);

        $message = (new \Swift_Message($mailInfo['title']))
            ->setFrom([$smtpUsername => $mailInfo['senderName']])
            ->setTo($mailInfo['sendTo'])
            ->setBody($html, 'text/html');

        foreach ($files as $file) {
            $message->attach(\Swift_Attachment::fromPath($file));
        }

        $swiftMailer->send($message);
    }
}
