<?php

abstract class Custom_AbstractMessage
{
    protected $_results = array();

    protected $_body;

    protected $_subject;

    protected $_recipientAddress;

    protected abstract function _createMessage();

    public function sendMessage()
    {
        if ( $this->_createMessage() ) {

            $config = new Zend_config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');

            //setup configuration array
            $mailConfig = array(
                'auth' => 'login',
                'username' => $config->messaging->email,
                'password' => $config->messaging->password,
                'ssl' => 'tls',
                'port' => $config->messaging->port
            );

            $mailTransport = new Zend_Mail_Transport_Smtp($config->messaging->smtpServer,$mailConfig);

            $mail = new Zend_Mail();

            $mail
                ->addTo($this->_recipientAddress)
                ->setSubject($this->_subject)
                ->setBodyHtml($this->_body)
                ->setFrom($config->messaging->email, 'Mike Matovic');

            try {
                $mail->send($mailTransport);
                $this->_results['result'] = 'success';
                $this->_results['message'] = 'email successfully sent to ' . $this->_recipientAddress;
            } catch( Exception $e ){
                $this->_results['result'] = 'server error';
                $this->_results['reasons'] = $e->getMessage();
            }
        }

        return $this->_results;
    }

    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    public function setSubject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    public function setRecipientAddress($toAddress)
    {
        $this->_recipientAddress = $toAddress;
        return $this;
    }
}