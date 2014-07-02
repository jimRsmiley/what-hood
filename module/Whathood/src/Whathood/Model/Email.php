<?php
namespace Application\Model;

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
    
    public function __construct( $data ) {
        $hydrator = new \Zend\Stdlib\Hydrator\ClassMethods(false);
        $hydrator->hydrate($data,$this);
    }
    
    public function send($subject,$messageBody) {
        
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
        $transport->send($message);
    }
    
    public function getTransport() {
        $transport = new SmtpTransport();
        $options   = new SmtpOptions(array(
            'name'              => 'localhost.localdomain',
            'host'              => $this->smtpHost,
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => $this->smtpUser,
                'password' => $this->smtpPass,
            ),
        ));
        $transport->setOptions($options);
        return $transport;
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