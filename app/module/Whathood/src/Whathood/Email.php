<?php
namespace Whathood;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class Email {

    protected $toAddress;
    protected $fromAddress;
    protected $fromName;
    protected $smtpUser;
    protected $smtpPass;
    protected $smtpHost;
    protected $smtpPort;

    protected $_logger;

    public function setLogger($logger) {
        $this->_logger = $logger;
    }

    public function logger() {
        return $this->_logger;
    }

    protected function __construct(array $data) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(false);
        $hydrator->hydrate($data,$this);
    }

    public static function build(array $data) {
        $emailer = new static($data);

        if (!$emailer->getSmtpHost())
            throw new \InvalidArgumentException("smtpHost must be defined");
        if (!$emailer->getSmtpUser())
            throw new \InvalidArgumentException("smtpUser must be defined");
        if (!$emailer->getSmtpPass())
            throw new \InvalidArgumentException("smtpPass must be defined");
        if (!$emailer->getFromAddress())
            throw new \InvalidArgumentException("fromAddress must be defined");
        if (!$emailer->getSmtpPort())
            throw new \InvalidArgumentException("smtpPort must be defined");
        if (!$emailer->getToAddress())
            throw new \InvalidArgumentException("toAddress must be defined");
        if (!array_key_exists('logger',$data))
            throw new \InvalidArgumentException("logger must be defined");

        return $emailer;
    }

    public function send($subject,$messageBody) {

        if (empty($subject))
            throw new \InvalidArgumentException("subject may not be empty");

        $subject = sprintf("[%s] %s",
            substr(strtoupper(\Whathood\Util::environment()), 0, 4),
            $subject);

        $html = new \Zend\Mime\Part(nl2br($messageBody));
        $html->type = 'text/html';
        $body = new \Zend\Mime\Message;
        $body->setParts(array($html));

        $message = new Message();
        $message->addFrom($this->fromAddress, $this->fromName)
            ->addTo( $this->toAddress )
            ->setSubject($subject);
        $message->setBody($body);

        $transport = $this->getTransport();

        if (\Whathood\Util::is_production())
            $transport->send($message);
    }

    public function getTransport() {
        $transport = new SmtpTransport();
        $transport->setOptions($this->getTransportOptions());
        return $transport;
    }

    public function getTransportOptions() {

        $connection_config =  array(
            'username' => $this->smtpUser,
            'password' => $this->smtpPass
        );

        if (465 == $this->getSmtpPort())
            $connection_config['ssl'] = 'ssl';
        else if (587 == $this->getSmtpPort())
            $connection_config['ssl'] = 'tls';

        $options   = new SmtpOptions(array(
            'name'              => 'localhost.localdomain',
            'host'              => $this->getSmtpHost(),
            'port'              => $this->getSmtpPort(),
            'connection_class'  => 'login',
            'connection_config' => $connection_config
        ));

        return $options;
    }
    public function getToAddress() {
        return $this->toAddress;
    }

    public function setToAddress( $toAddress ) {
        $this->toAddress = $toAddress;
    }

    public function getSmtpUser() {
        return $this->smtpUser;
    }

    public function setSmtpUser( $smtpUser ) {
        $this->smtpUser = $smtpUser;
    }

    public function getSmtpPass() {
        return $this->smtpPass;
    }

    public function setSmtpPass( $smtpPass ) {
        $this->smtpPass = $smtpPass;
    }

    public function getSmtpHost() {
        return $this->smtpHost;
    }

    public function setSmtpHost( $smtpHost ) {
        $this->smtpHost = $smtpHost;
    }

    public function getSmtpPort() {
        return $this->smtpPort;
    }

    public function setSmtpPort( $smtpPort ) {
        $this->smtpPort = $smtpPort;
    }

    public function getFromAddress() {
        return $this->fromAddress;
    }

    public function setFromAddress($fromAddress) {
        $this->fromAddress = $fromAddress;
    }

    public function getFromName() {
        return $this->fromName;
    }

    public function setFromName( $fromName ) {
        $this->fromName = $fromName;
    }
}
?>
