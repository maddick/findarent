<?php
class Communication_Model_Message
{
    protected $_results = array();

    protected $_body;

    protected $_subject;

    protected $_recipientAddress;

    protected $_senderName;

    protected $_recipientName;

    protected $_emailType;

    protected $_listingTitle;

    protected $_listingURL;

    protected $_listingNumber;

    const TYPE_TO_FRIEND = 1;

    public function sendEmail( $type )
    {
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

        $reasons = array();

        if ( $type === Communication_Model_Message::TYPE_TO_FRIEND ) {
            if ( !isset($this->_senderName) ) {
                $this->_results['result'] = 'error';
                $reasons[] = 'No sender name was provided';
            }

            if ( !isset($this->_recipientName ) ) {
                $this->_results['result'] = 'error';
                $reasons[] = 'No recipient name was provided';
            }

            if ( !isset( $this->_recipientAddress ) ) {
                $this->_results['result'] = 'error';
                $reasons[] = 'No recipient address was provided.';
            }

            if ( !isset( $this->_listingTitle ) ) {
                $this->_results['result'] = 'error';
                $reasons[] = 'No listing title was provided.';
            }

            if ( !isset( $this->_listingURL ) ) {
                $this->_results['result'] = 'error';
                $reasons[] = 'No listing URL was provided.';
            }

            if ( !isset( $this->_listingNumber ) ) {
                $this->_results['result'] = 'error';
                $reasons[] = 'No listing number was provided.';
            }

            if ( !empty( $reasons ) ) {
                $this->_results['reasons'] = $reasons;
                return $this->_results;
            }

            $this->_subject = "FindARent.Net: Listing #" . $this->_listingNumber . " From " . $this->_senderName;

            $this->_body = "Dear " . $this->_recipientName . ",<br><br>" .
                           $this->_senderName . " has sent you this link in reference to a FindARent.net rental listing " .
                           "titled <b>" . $this->_listingTitle . "</b>. To view the listing, click the following link: <br><br>" .
                           "<a href=\"" . $this->_listingURL . "\">" . $this->_listingTitle . "</a>";
        }

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

        return $this->_results;
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

    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    public function setSenderName($senderName)
    {
        $this->_senderName = $senderName;
        return $this;
    }

    public function setRecipientName($recipientName)
    {
        $this->_recipientName = $recipientName;
        return $this;
    }

    public function setListingTitle($listingTitle)
    {
        $this->_listingTitle = $listingTitle;
        return $this;
    }

    public function setListingURL($listingURL)
    {
        $this->_listingURL = $listingURL;
        return $this;
    }

    public function setListingNumber($listingNumber)
    {
        $this->_listingNumber = $listingNumber;
        return $this;
    }
}