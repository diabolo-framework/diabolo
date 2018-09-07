<?php
namespace X\Service\Mail\Handler;
use X\Service\Mail\Mail;
abstract class MailHandler {
    /**
     * @param array $option
     */
    public function __construct( array $option = array() ) {
        foreach ( $option as $key => $value ) {
            if ( property_exists($this, $key) ) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * @param array[] $addresses each address contains `name` and `address` key.
     * @return string
     */
    protected function getAddressListString( $addresses ) {
        $addressList = array();
        foreach ( $addresses as $address ) {
            if ( null === $address['name'] ) {
                $addressList[] = $address['address'];
            } else {
                $addressList[] = sprintf('%s <%s>', $address['name'], $address['address']);
            }
        }
        return implode(', ', $addressList);
    }
    
    /**
     * @param Mail $mail
     * @param string $contentSeparator
     * @return string
     */
    protected function getHeaders( Mail $mail, &$contentSeparator ) {
        $headers = array();
        $headers['MIME-Version'] = '1.0';
        $headers['Reply-To'] = $mail->replyTo;
        $headers['X-Mailer'] = 'DiaboloMail';
        $headers['Date'] = $mail->getDate();
        $headers['Message-ID'] = $mail->getMessageId();
        $headers['Cc'] = $this->getAddressListString($mail->getCCRecipients());
        $headers['Bcc'] = $this->getAddressListString($mail->getBCCRecipients());
        $headers['Subject'] = '=?UTF-8?B?'.base64_encode($mail->subject).'?=';
        $headers['From'] = $this->getAddressListString(array(array(
            'name'=>$mail->fromName,
            'address'=>$mail->from
        )));
        $headers['Sender'] = $headers['From'];
        $headers['To'] = $this->getAddressListString($mail->getRecipients());
        
        if ( !$mail->hasAttachments() && $mail->isHtml ) {
            $headers['Content-type'] = 'text/html; charset='.$mail->charset;
        }
        if ( $mail->hasAttachments() ) {
            $contentSeparator = md5(uniqid(time()));
            
            $headers['Content-Type'] = 'multipart/mixed; boundary="'.$contentSeparator.'"';
            $headers['Content-Transfer-Encoding'] = '7bit';
            $headers[] = 'This is a MIME encoded message.';
        }
        
        $headers = array_filter($headers);
        foreach ( $headers as $key => $value ) {
            if ( is_numeric($key) ) {
                $headers[$key] = $value;
            } else {
                $headers[$key] = $key.': '.$value;
            }
        }
        $headers = implode("\r\n", $headers)."\r\n";
        
        return $headers;
    }
    
    /**
     * @param Mail $mail
     * @param unknown $contentSeparator
     */
    protected function getBody( Mail $mail, $contentSeparator ) {
        if ( !$mail->hasAttachments() ) {
            return $mail->content;
        }
        
        $body = array();
        $body[] = '--'.$contentSeparator;
        if ( $mail->isHtml ) {
            $body[] = 'Content-Type: text/html; charset="'.$mail->charset.'"';
        } else {
            $body[] = 'Content-Type: text/plain; charset="'.$mail->charset.'"';
        }
        $body[] = 'Content-Transfer-Encoding: 8bit';
        $body[] = '';
        $body[] = $mail->content;
        
        foreach ( $mail->getAttachments() as $attachment ) {
            $body[] = '--'.$contentSeparator;
            $body[] = 'Content-Type: application/octet-stream; name="'.$attachment['name'].'"';
            $body[] = 'Content-Transfer-Encoding: base64';
            $body[] = 'Content-Disposition: attachment';
            $body[] = '';
            $body[] = base64_encode(file_get_contents($attachment['path']));
        }
        $body[] = '--'.$contentSeparator.'--';
        
        $body = implode("\r\n", $body);
        return $body;
    }
    
    abstract public function send( Mail $mail );
}