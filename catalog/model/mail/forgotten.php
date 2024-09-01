<?php
namespace Opencart\Catalog\Model\Mail;
class Forgotten extends \Opencart\System\Engine\Model {
  
  public function sendCustomerMail($email, $code) {
    $this->load->language('mail/forgotten');
    
    $subject = sprintf($this->language->get('text_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
    
    $message  = sprintf($this->language->get('text_greeting'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')) . "\n\n";
    $message .= $this->language->get('text_change') . "\n\n";
    $message .= $this->url->link('account/reset', 'code=' . $code, true) . "\n\n";
    $message .= sprintf($this->language->get('text_ip'), $this->request->server['REMOTE_ADDR']) . "\n\n";
    
    $mail = new \Opencart\System\Library\Mail();
    $mail->protocol = $this->config->get('config_mail_protocol');
    $mail->parameter = $this->config->get('config_mail_parameter');
    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
    
    $mail->setTo($email);
    $mail->setFrom($this->config->get('config_email'));
    $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
    $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
    $mail->send();
  }
  
  public function sendAdminMail($email) {
    $this->load->language('mail/forgotten');
    
    $subject = sprintf($this->language->get('text_subject_admin'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
    
    $message .= sprintf($this->language->get('text_message_admin'), $email) . "\n\n";
    $message .= sprintf($this->language->get('text_ip_admin'), $this->request->server['REMOTE_ADDR']) . "\n\n";
    
    $mail = new \Opencart\System\Library\Mail();
    $mail->protocol = $this->config->get('config_mail_protocol');
    $mail->parameter = $this->config->get('config_mail_parameter');
    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
    
    $mail->setTo($this->config->get('config_email'));
    $mail->setFrom($this->config->get('config_email'));
    $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
    $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
    $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
    $mail->send();
  }
  
}