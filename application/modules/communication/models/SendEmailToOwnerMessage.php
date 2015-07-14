<?php
class Communication_Model_SendEmailToOwnerMessage extends Custom_AbstractMessage
{
    /**
     * @var Custom_ListingCriteria
     */
    protected $_listing;

    protected $_ownerName;

    protected $_senderEmail;

    protected $_senderMessage;

    protected $_senderName;

    protected $_senderPhone;

    protected $_farNotice = ""; //TODO: this is based on source being 2 denoting Craig's List

    protected function _createMessage()
    {
        //reasons array to hold failure messages
        $reasons = array();

        if ( !isset( $this->_listing ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'listing was not provided';
        } else {
            if ( !$this->_listing->isValid() ) {
                $this->_results['result'] = 'error';
                $reasons = array_merge( $reasons, $this->_listing->getValidationErrors() );
            }
        }

        if ( !isset( $this->_ownerName ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'ownerName was not provided';
        }
        if ( !isset( $this->_senderEmail ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'senderEmail was not provided';
        }
        if ( !isset( $this->_senderMessage ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'senderMessage was not provided';
        }
        if ( !isset( $this->_senderName ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'senderName was not provided';
        }
        if ( !isset( $this->_senderPhone ) ) {
            $this->_results['result'] = 'error';
            $reasons[] = 'senderPhone was not provided';
        }

        if ( !empty( $reasons ) ) {
            $this->_results['reasons'] = $reasons;
            return false;
        }

        $recipientAddress = new Custom_EmailCriteria('mike.matovic@gmail.com');
        $this->setRecipientAddress($recipientAddress);

        //generate a mini-listing using with a listing:
        // - listingID
        // - headline
        // - rent
        // - bedrooms
        // - state
        // - city
        // - description (MarketingMessage)
        // - link to the listing
        // - photo of the listing

        $listing = $this->_listing->getCriteriaValue();

        $miniListing =
            "<div style=\"color: #5a5a5a;\">" .
              "<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"12\" align=\"center\">" .
                "<tbody>" .
                  "<tr>" .
                    "<td align=\"center\" valign=\"top\">" .
                      "<table style=\"width: 850px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\" bgcolor=\"#F2C747\">" .
                        "<tbody>" .
                          "<tr>" .
                            "<td>" .
                              "<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\" bgcolor=\"#F9F9F9\">" .
                                "<tbody>" .
                                  "<tr>" .
                                    "<td align=\"center\" width=\"300\">" .
                                      "<a href=\"". $listing['URL'] ."\" rel=\"nofollow\" target=\"_blank\"><img src=\"". $listing['Image'] ."\" alt=\"\" width=\"250px\"></a>" .
                                      "<br><strong>Listing #" . $listing['ListingID'] . "</strong>".
                                    "</td>".
                                    "<td>".
                                      "<div style=\"color: #2f6d9d;\"><strong>" . $listing['ListingID'] . "</strong></div>".
                                        "<hr size=\"1\" noshade=\"\" style=\"border-top: 1px solid #BDBCAB;\">".
                                        "<table style=\"width: 98%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">".
                                          "<tbody>".
                                            "<tr>".
                                              "<td>".
                                                "<br />".
                                                "<div style=\"color: #f5472c;\">".
                                                "<strong>Rent: $" . $listing['Rent'] . "</strong>".
                                                "</div>Bedrooms: " . $listing['Bedrooms'] .
                                                "<br>".
                                                "Bathrooms: " . $listing['Bathrooms'] .
                                              "</td>".
                                              "<td>" .
                                                $listing['City'] .", " . $listing['State'] .
                                                "<br>" .
                                                "<a href=\"". $listing['URL'] ."\" rel=\"nofollow\" target=\"_blank\"><strong>Click for contact info &amp; details</strong></a>".
                                              "</td>".
                                            "</tr>".
                                            "<tr>".
                                              "<td colspan=\"2\">".
                                                "<div style=\"color: #000000;\">".
                                                $listing['MarketingMessage'] .
                                                "<a href=\"". $listing['URL'] ."\" rel=\"nofollow\" target=\"_blank\"> <strong>MORE »</strong></a>".
                                                "<br>".
                                                "<br>".
                                                "<hr size=\"1\" noshade=\"\" style=\"border-top: 1px solid #BDBCAB;\">".
                                                "<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"5\">".
                                                  "<tbody>".
                                                    "<tr>".
                                                      "<td>".
                                                        "<a href=\"http://www.findarent.net\"><img src=\"http://www.findarent.net/App_Themes/Default/Images/logo_cl.png\" alt=\"FindARent.net\" border=\"0\" style=\"margin: 5px 0;\"></a>".
                                                      "</td>
                                                    </tr>
                                                  </tbody>
                                                </table>
                                              </div>
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>";


        $this->_subject = "FindARent.Net : Listing #" . $listing['ListingID'] . " Inquiry";

        $this->_body = "Dear " . $this->_ownerName . ", <br />" .
                        "<br/>" .
                        $this->_farNotice .
                        "<p>The following email inquiry was sent in response to the following listing:</p>" .
                        "<br />" .
                        $miniListing .
                        "<br />" .
                        "<hr />" .
                        "<br />" .
                        "<b><u>Contact Information</u>:</b><br />" .
                        "<b>Name:</b>&nbsp;&nbsp;" . $this->_senderName . "<br />" .
                        "<b>Email:</b>&nbsp;&nbsp;" . $this->_senderEmail . "<br />" .
                        "<b>Phone:</b>&nbsp;&nbsp;" . $this->_senderPhone . "<br />" .
                        "<b>Message:</b><br />" .
                        $this->_senderMessage . "<br />" .
                        "<br />" .
                        "<hr />" .
                        "<a href=\"http://www.findarent.net\"><img src=\"http://www.findarent.net/App_Themes/Default/Images/mainlogo.png\" alt=\"FindARent.net\" border=\"0\" style=\"margin: 5px 0;\" /></a><br />" .
                        "<a href=\"http://www.findarent.net\">http://www.findarent.net/</a><br />" .
                        "<br />";
        return true;
    }

    public function setListing($listing)
    {
        if ( $listing instanceof Custom_ListingCriteria ) {
            $this->_listing = $listing;
        } else {
            throw new Exception('$listing must be an instance of Custom_ListingCriteria');
        }
        return $this;
    }

    public function setOwnerName($ownerName)
    {
        $this->_ownerName = $ownerName;
    }

    public function setSenderEmail($senderEmail)
    {
        $this->_senderEmail = $senderEmail;
    }

    public function setSenderMessage($senderMessage)
    {
        $this->_senderMessage = $senderMessage;
    }

    public function setSenderName($senderName)
    {
        $this->_senderName = $senderName;
    }

    public function setSenderPhone($senderPhone)
    {
        $this->_senderPhone = $senderPhone;
    }
}