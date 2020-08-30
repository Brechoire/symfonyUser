<?php

namespace App\Service;

use Swift_Message;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class SendMail
 * @package App\Service
 */
class SendMailService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $renderer;

    /**
     * SendMail constructor.
     * @param \Swift_Mailer $mailer
     * @param Environment $renderer
     */
    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    /**
     * @param $subject|Email subject
     * @param $myEmail|Sender Email
     * @param $email|Recipient's email
     * @param $render|Template email
     * @param array $param|Param
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendMail($subject, $myEmail, $email, $render, $param = [])
    {
        $message = (new Swift_Message($subject))
            ->setFrom($myEmail)
            ->setTo($email)
            ->setBody(
                $this->renderer->render(
                    $render, $param
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

}