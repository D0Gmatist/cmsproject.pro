<?php

namespace Modules\Mail;

final class Mail {
	public $mail;
	public $send_error = false;
	public $smtp_msg = "";
	public $from = false;
	public $html_mail = false;
	public $bcc = array ();
	public $keepalive = false;

	/**
	 * Mail constructor.
	 * @param $config
	 * @param bool $is_html
	 */
	function __construct( $config, $is_html = false ) {
		/** @var  mail */
		$this->mail = new PhpMailer;
		$this->mail->CharSet = $config['charset'];
		$this->mail->Encoding = "base64";

		$config['mail_title'] = str_replace( '&amp;', '&', $config['mail_title'] );

		if( $config['mail_title'] ) {
			$this->mail->setFrom( $config['admin_mail'], $config['mail_title'] );

		} else {
			$this->mail->setFrom( $config['admin_mail'] );

		}

		if( $config['mail_metod'] == "smtp" ) {
			$this->mail->isSMTP();
			$this->mail->Timeout = 10;
			$this->mail->Host = $config['smtp_host'];
			$this->mail->Port = intval( $config['smtp_port'] );
			$this->mail->SMTPSecure = $config['smtp_secure'];

			if( $config['smtp_user'] ) {
				$this->mail->SMTPAuth = true;
				$this->mail->Username = $config['smtp_user'];
				$this->mail->Password = $config['smtp_pass'];

			}

			if( $config['smtp_mail'] ) {
				$this->mail->From = $config['smtp_mail'];
				$this->mail->Sender = $config['smtp_mail'];

			}

		}
		$this->mail->XMailer = "CMS";

		if ( $is_html ) {
			$this->mail->isHTML();
			$this->html_mail = true;

		}

	}

	function send( $to, $subject, $message ) {
		if( $this->from ) {
			$this->mail->addReplyTo($this->from  );

		}
		$this->mail->addAddress( $to );
		$this->mail->Subject = $subject;

		if($this->mail->Mailer == 'smtp' AND $this->keepalive ) {
			$this->mail->SMTPKeepAlive = true;

		}

		if( $this->html_mail ) {
			$this->mail->msgHTML( $message );

		} else {
			$this->mail->Body = $message;

		}

		if( count( $this->bcc ) ) {
			foreach( $this->bcc AS $bcc ) {
				$this->mail->addBCC( $bcc );

			}

		}

		if ( ! $this->mail->send() ) {
			$this->smtp_msg = $this->mail->ErrorInfo;
			$this->send_error = true;

		}
		$this->mail->clearAllRecipients();
		$this->mail->clearAttachments();

	}

	function addAttachment( $path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment' ) {
		$this->mail->addAttachment( $path, $name, $encoding, $type, $disposition );

	}

}
