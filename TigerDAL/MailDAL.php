<?php

namespace TigerDAL;

/*
 * 基本数据类包
 * 类
 * 发送邮件用
 *
 */

use config\code;
use http\Exception;
use PHPMailer;
use phpmailerException;

class MailDAL {

    //默认方法
    function __construct() {
    }

    /**
     * 执行邮件发送的方法
     * @param $fromInfo
     * @param $maildetail 
     * @return bool
     */
    function mailTo($fromInfo, $maildetail) {
        try{
            if (!empty($fromInfo["out_put_email"])) {
                $mail = new PHPMailer();
                $mail->CharSet = "utf-8";
                $mail->IsSMTP();
                $mail->SMTPSecure = "ssl";
                $mail->Host = $fromInfo['out_put_server']; // SMTP servers
                $mail->Port = $fromInfo['out_put_ssl'];
                $mail->SMTPAuth = true; // turn on SMTP authentication
                $mail->IsHTML(true); //开启html格式

                $mail->Username = $fromInfo['out_put_email']; // SMTP username
                $mail->Password = $fromInfo['out_put_password']; // SMTP password
                //			$mail->SMTPDebug  = 2; 

                $mail->From = $fromInfo['out_put_email']; //从哪里发来
                $mail->FromName = $fromInfo['company_name']; //从哪里发来

                $mail->AddAddress($maildetail['user_email'], $maildetail['user_name']); //收件人地址
                $mail->AddCC($fromInfo['company_email'], $fromInfo['company_name']); //抄送
                $mail->AddReplyTo($fromInfo['company_email'], $fromInfo['company_name']); //对方可回复对象.

                $mail->Subject = $maildetail['subject'];
                $mail->Body = $maildetail['body']; //邮件正文
                //$data['con']=$mail;
                //return $mail;
                try {
                    return $mail->Send();
                } catch (phpmailerException $e) {
                    CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($e));
                    exit;
                }
            } else {
                //return mail();
                $to = $maildetail['user_email'];
                $subject = $maildetail['subject'];
                $body = $maildetail['body'];
                $headers = "From: " . $fromInfo['company_email'] . "\n";
                $headers .= "Cc: " . $fromInfo['company_email'] . "\n";
                $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
                return mail($to, $subject, $body, $headers);
            }
        }catch(Exception $ex) {
            CatchDAL::markError(code::$code[code::HOME_INDEX], code::HOME_INDEX, json_encode($ex));
            exit;
        }
    }

}
