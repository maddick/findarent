<?php

abstract class Custom_AbstractMessage
{
    protected $_results = array();

    protected $_body;

    protected $_BCC;

    protected $_subject;

    /**
     * @var Custom_EmailCriteria
     */
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
                ->addTo($this->_recipientAddress->getCriteriaValue())
                ->setSubject($this->_subject)
                ->setBodyHtml($this->_body)
                ->setFrom($config->messaging->email, 'Mike Matovic');

            if ( isset( $this->_BCC) ) {
                $mail->addBcc($this->_BCC);
            }

            try {
                $mail->send($mailTransport);
                $this->_results['result'] = 'success';
                $this->_results['message'] = 'email successfully sent to ' . $this->_recipientAddress->getCriteriaValue();
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

    public function setRecipientAddress($recipientAddress)
    {
        if ( $recipientAddress instanceof Custom_EmailCriteria ) {
            $this->_recipientAddress = $recipientAddress;
        } else {
            throw new Exception('$recipientAddress must be an instance of Custom_EmailCriteria');
        }
        return $this;
    }

    public function setBCC($BCC)
    {
        $this->_BCC = $BCC;
    }
}