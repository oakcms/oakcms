<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 28.09.2016
 * Project: kotsyubynsk
 * File name: mail_two.php
 */

header('Content-type: application/json');
session_start();

if (!isset($_SESSION['post'])) {// Если данные присланы впервые
    $_SESSION['post'] = $_POST;
    $double = false;
} else {    //Если данные уже присылались то
    if (count(array_diff($_POST, $_SESSION['post'])) != 0) {   //сравниваем с предыдущими
        $double = false;    //Если отличаются
        $_SESSION['post'] = $_POST;
    } else $double = true;   //если те же самые, что и в прошлый раз
}
if (!$double) {

    //обработчик формы
    if ($_POST) {

        $to_email = require 'email_to.php'; //Recipient email, Replace with own email here

        //Sanitize input data using PHP filter_var().
        $user_name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
        $user_tel = filter_var($_POST["tel"], FILTER_SANITIZE_STRING);
        $user_email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
        $user_comment = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);

        // subject
        $subject = "Коцюбинський";

        //email body
        $message_body = "Клиент очень ждет Вашего звонка:" . "\n<br>\n\n" . "Имя: \n " . $user_name . "\n<br>\n\n" . "Телефон: \n " . $user_tel . "\n<br>\n\n" . "Email: \n " . $user_email . "\n<br>\n\n" . "Комментарий/Вопрос: \n " . $user_comment . "<br><br> URL откуда пришла заявка: " . $_POST["location"];

        //proceed with PHP email.
        $headers = 'From: kotsyubynsk' . "\r\n" .
            'Content-type: text/html; charset=utf-8' . "\r\n" .
            'Reply-To: ' . $user_email . '' . "\r\n" .
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

            $url = 'https://docs.google.com/forms/d/e/1FAIpQLScQ3EbazmFje0cY4GmLT3Zvsu_aWOeBX97p44C53MO3qfWdtw/formResponse'; // куда слать, это атрибут action у гугл формы
            $data = array(); // массив для отправки в гугл форм
            $data['entry.1898095542'] = getNextNumber();
            $data['entry.441979965'] = $user_name;
            $data['entry.1437418522'] = $user_tel;
            $data['entry.1863557833'] = $user_email;
            $data['entry.1058019864'] = $user_comment;

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
    }
} else {
    $output = json_encode([
        'type' => 'error',
        'text' => 'You have already submitted the form with the data'
    ]);
    exit($output);
}

function getNextNumber() {
    $count = (int)file_get_contents('id.txt');
    $count+=1;
    file_put_contents('id_two.txt',$count);
    return $count;
}

?>