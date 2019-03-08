<?php

namespace bdk\plug\mail;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

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
     * @var bool
     */
    private $isSsl = false;
    /**
     * @var array
     */
    private const SSL_PORT_ARR = [465, 587];

    /**
     * Driver constructor.
     *
     * @param array $conf
     */
    public function __construct(array $conf)
    {
        $this->host  = $conf['host'];
        $this->port  = $conf['port'];
        $this->uname = $conf['uname'];
        $this->pwd   = $conf['pwd'];
    }

    /**
     * @return Swift_Mailer|null
     */
    public function getMailer(): ?Swift_Mailer
    {
        if ( is_null($this->mailer) ) {
            $transport = new Swift_SmtpTransport($this->host, $this->port,
                in_array($this->port, self::SSL_PORT_ARR) ? 'ssl' : null);
            $transport->setUsername($this->uname)
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
     *
     * @return int
     */
    public function send(string $subject, array $from, array $to, string $body)
    {
        $message = (new Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body);
        $mailer  = $this->getMailer();
        $result  = $mailer->send($message);
        return $result;
    }
}
