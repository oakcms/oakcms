<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\text\models\Text;
 */

?>
<section class="<?= $model->settings['cssClass']['value'] ?>">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <p><?= $model->settings['text_top']['value'] ?></p>
            </div>
            <div class="col-sm-4 col-sm-offset-4">
                <div>
                    <h4>Форма обратной связи</h4>
                    <p class="ok hidden">Ваш запрос получен!<br>Мы свяжемся с вами в ближайшее время!</p>
                    <form class="formQuest2" method='post' autocomplete="off">
                        <label for="name">Имя</label>
                        <input type="text" name="name" id="name" pattern="[А-Яа-яA-Za-z]{3,30}" required>
                        <label for="tel">Телефон</label>
                        <input type="tel" name="tel" id="tel" required>
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                        <label for="comment">Комментарий</label>
                        <textarea name="comment" id="comment" required></textarea>
                        <input type="hidden" name="form" value="Замовлення зворотнього звяку!">
                        <input type="submit" name="send" value="Отправить">
                    </form>
                </div>
            </div>
            <div class="col-xs-12">
                <p><?= $model->settings['text_bottom']['value'] ?></p>
            </div>
        </div>
    </div>
</section>
