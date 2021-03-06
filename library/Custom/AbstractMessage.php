<?php

abstract class Custom_AbstractMessage
{
    protected $_results = array();

    protected $_body;

    protected $_BCC;

    protected $_CC;

    protected $_subject;

    /**
     * @var Custom_EmailCriteria
     */
    protected $_recipientAddress;

    /**
     * @var Custom_EmailCriteria
     */
    protected $_replyToAddress;

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
                'ssl' => $config->messaging->ssl,
                'port' => $config->messaging->port
            );

            $mailTransport = new Zend_Mail_Transport_Smtp($config->messaging->smtpServer,$mailConfig);

            $mail = new Zend_Mail();

            $mail
                ->addTo($this->_recipientAddress->getCriteriaValue())
                ->setSubject($this->_subject)
                ->setBodyHtml($this->_body)
                ->setFrom($config->messaging->email, 'notifications');

            if ( isset($this->_CC) ) {
                $mail->addCc($this->_CC);
            }

            if ( isset( $this->_BCC) ) {
                $mail->addBcc($this->_BCC);
            }

            if ( isset( $this->_replyToAddress ) ) {
                $mail->setReplyTo($this->_replyToAddress->getCriteriaValue());
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

    public function setCC($CC)
    {
        $this->_CC = $CC;
    }

    public function setReplyToAddress($replyToAddress)
    {
        if ( $replyToAddress instanceof Custom_EmailCriteria ) {
            $this->_replyToAddress = $replyToAddress;
        } else {
            throw new Exception('$replyToAddress must be an instance of Custom_EmailCriteria');
        }
        return $this;
    }
}