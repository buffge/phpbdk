<?php

namespace bdk\plug\mail;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;

class Driver
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var int
     */
    private $port;
    /**
     * @var string
     */
    private $uname;
    /**
     * @var string
     */
    private $pwd;
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * Driver constructor.
     * @param array $conf
     */
    public function __construct(array $conf)
    {
        $this->host = $conf['host'];
        $this->port = $conf['port'];
        $this->uname = $conf['uname'];
        $this->pwd = $conf['pwd'];
    }

    /**
     * @return Swift_Mailer|null
     */
    public function getMailer(): ?Swift_Mailer
    {
        if (is_null($this->mailer)) {
            $transport = (new Swift_SmtpTransport($this->host, $this->port))
                ->setUsername($this->uname)
                ->setPassword($this->pwd);
            $this->mailer = new Swift_Mailer($transport);
        }
        return $this->mailer;
    }

    /**
     * @param string $subject
     * @param array $from
     * @param array $to
     * @param string $body
     * @return int
     */
    public function send(string $subject, array $from, array $to, string $body)
    {
        $message = (new Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body);
        $mailer = $this->getMailer();
        $result = $mailer->send($message);
        return $result;
    }

}
