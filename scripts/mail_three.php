<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 28.09.2016
 * Project: kotsyubynsk
 * File name: mail_three.php
 */

@error_reporting(0);
header('Content-type: application/json');
session_start();

if (!isset($_SESSION['post'])) {
    $_SESSION['post'] = $_POST;
    $double = false;
} else {
    if (count(array_diff($_POST, $_SESSION['post'])) != 0) {
        $double = false;
        $_SESSION['post'] = $_POST;
    } else $double = true;
}
if (!$double) {

    if ($_POST) {

        $to_email = require 'email_to.php'; //Recipient email, Replace with own email here

        //Sanitize input data using PHP filter_var().
        $user_tel = filter_var($_POST["tel"], FILTER_SANITIZE_STRING);
        $user_popup = filter_var($_POST["popup"], FILTER_SANITIZE_STRING);

        // subject
        $subject = "Коцюбинський";

        //email body
        $message_body = "Клиент очень ждет Вашего звонка:" . "\n<br>\n\n" .
            "Телефон: \n " . $user_tel . "\n<br>\n\n" .
            "<br><br> URL откуда пришла заявка: " . $_POST["location"];

        //proceed with PHP email.
        $headers = 'From: kotsyubynsk' . "\r\n" .
            'Content-type: text/html; charset=utf-8' . "\r\n" .
            'Reply-To: ' . $user_name . '' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $send_mail = mail($to_email, $subject, $message_body, $headers);

        if (!$send_mail) {
            //If mail couldn't be sent output error. Check your PHP email configuration (if it ever happens)
            $output = json_encode([
                'type' => 'error',
                'text' => 'Could not send mail! Please check your PHP mail configuration.'
            ]);
            exit($output);
        } else {

            $url = 'https://docs.google.com/forms/d/e/1FAIpQLSfPLeeQvq8DP3V8Dcm94KNlUFOQ9ITEOwvT1JNdr7bdVWdsrQ/formResponse'; // куда слать, это атрибут action у гугл формы
            $data = array(); // массив для отправки в гугл форм
            $data['entry.1268529292'] = getNextNumber();
            $data['entry.557716529'] = $user_tel;
            $data['entry.1641626580'] = $user_popup;

            $data = http_build_query($data);

            $options = array( // задаем параметры запроса
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => $data,
                ),
            );
            $context  = stream_context_create($options); // создаем контекст отправки
            $result = file_get_contents($url, false, $context);

            $output = json_encode([
                'type' => 'message',
                'text' => 'Hi ' . $user_name . ' Thank you for your email'
            ]);
            exit($output);
        }
    } else {
        $output = json_encode([
            'type' => 'error',
            'text' => 'You have already submitted the form with the data'
        ]);
        exit($output);
    }
} else {
    $output = json_encode([
        'type' => 'error',
        'text' => 'You have already submitted the form with the data'
    ]);
    exit($output);
}

function getNextNumber() {
    $count = (int)file_get_contents('id_three.txt');
    $count+=1;
    file_put_contents('id_two.txt',$count);
    return $count;
}