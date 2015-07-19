<?php

class Communication_Model_SendEmailToFAR extends Custom_AbstractMessage
{
    protected $_senderName;

    protected $_senderEmail;

    protected $_senderMessage;

    protected $_senderCompany = 'Was Not Provided';

    protected $_senderPhone = 'Was Not Provided';

    protected function _createMessage()
    {
        $reasons = array();

        if ( !isset($this->_senderName ) ) {
            $reasons[] = 'Sender name was not provided';
        }

        if ( !isset( $this->_senderEmail ) ) {
            $reasons[] = 'Sender email was not provided';
        }

        if ( !isset( $this->_senderMessage ) ) {
            $reasons[] = 'Sender messages was not provided';
        }

        if ( !empty( $reasons ) ) {
            $this->_results['result'] = 'error';
            $this->_results['reasons'] = $reasons;
            return false;
        }

        $this->_subject = 'FindARent.net : Contact Request';

        $this->_body = '<h2>Contact Request</h2>
                        The following information was submitted via the Contact Request form:<br />
                        <br />
                        <hr />
                        <table>
                            <tr>
                                <td width="85px">Name:</td>
                                <td>
                                    <%Name%>
                                </td>
                            </tr>
                            <tr>
                                <td>Email:</td>
                                <td>
                                    <%Email%>
                                </td>
                            </tr>
                            <tr>
                                <td>Phone:</td>
                                <td>
                                    <%Phone%>
                                </td>
                            </tr>
                            <tr>
                                <td>Company:</td>
                                <td>
                                    <%Company%>
                                </td>
                            </tr>
                            <tr>
                                <td>Message:</td>
                                <td>
                                    <%Message%>
                                </td>
                            </tr>
                        </table>
                        <br />
                        <hr />
                        <a href="http://www.findarent.net"><img src="http://www.findarent.net/App_Themes/Default/Images/mainlogo.png" alt="FindARent.net" border="0" style="margin: 5px 0;" /></a><br />
                        <a href="http://www.findarent.net">http://www.findarent.net/</a><br />
                        <br />';

        $this->_body = str_replace('<%Name%>', $this->_senderName, $this->_body);
        $this->_body = str_replace('<%Email%>', $this->_senderEmail, $this->_body);
        $this->_body = str_replace('<%Phone%>', $this->_senderPhone, $this->_body);
        $this->_body = str_replace('<%Company%>', $this->_senderCompany, $this->_body);
        $this->_body = str_replace('<%Message%>', $this->_senderMessage, $this->_body);

        //$this->_recipientAddress = 'nick@findarent.net';
        $emailCriteria = new Custom_EmailCriteria('mike.matovic@gmail.com');
        $this->setRecipientAddress($emailCriteria);

        return true;
    }

    public function setSenderName($senderName)
    {
        $this->_senderName = $senderName;
        return $this;
    }

    public function setSenderEmail($senderEmail)
    {
        $this->_senderEmail = $senderEmail;
        return $this;
    }

    public function setSenderMessage($senderMessage)
    {
        $this->_senderMessage = $senderMessage;
        return $this;
    }

    public function setSenderCompany($senderCompany)
    {
        $this->_senderCompany = $senderCompany;
        return $this;
    }

    public function setSenderPhone($senderPhone)
    {
        $this->_senderPhone = $senderPhone;
        return $this;
    }
}