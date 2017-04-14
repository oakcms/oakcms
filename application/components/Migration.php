<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\components;

use app\modules\language\models\LanguageSource;
use app\modules\language\models\LanguageTranslate;
use Yii;
use yii\helpers\Console;

class Migration extends \yii\db\Migration
{
    /**
     * @var array
     */
    public $translations = [];

    public function upDbTranslate()
    {
        $db = Yii::$app->db;

        if (!$db instanceof \yii\db\Connection) {
            throw new \Exception('The "db" option must refer to a valid database application component.');
        }

        foreach ($this->translations as $language => $categories) {
            Console::output("Language: $language");
            foreach ($categories as $category => $msgs) {
                $messagesCount = count($msgs, COUNT_RECURSIVE);
                $i = 0;
                Console::output("Category: $category");
                Console::startProgress(0, $messagesCount);
                $messagesOutputs = '';
                foreach ($msgs as $source_msgs => $translation) {
                    Console::updateProgress(++$i, $messagesCount);

                    if (
                        ($languageSource = LanguageSource::find()
                            ->where(['category' => $category, 'message' => $source_msgs])
                            ->one()) === null
                    ) {
                        $languageSource = new LanguageSource();
                        $languageSource->category = $category;
                        $languageSource->message = $source_msgs;
                        if ($languageSource->save()) {

                            $messagesOutputs .= $i . ' Complete create Language Source' . PHP_EOL;

                            $languageTranslate = new LanguageTranslate();

                            $languageTranslate->id = $languageSource->id;
                            $languageTranslate->language = $language;
                            $languageTranslate->translation = $translation;
                            if ($languageTranslate->save()) {
                                $messagesOutputs .= $i . ' Complete create Language Translate' . PHP_EOL;
                            } else {
                                $messagesOutputs .= $i . ' Error create Language Translate' . PHP_EOL;
                            }

                        } else {
                            $messagesOutputs .= $i . ' Error create Language Source' . PHP_EOL;
                        }
                    } else {
                        if (
                            ($languageTranslate = LanguageTranslate::find()
                                ->where(['id' => $languageSource->id, 'language' => $language])
                                ->one()) === null
                        ) {
                            $languageTranslate = new LanguageTranslate();

                            $languageTranslate->id = $languageSource->id;
                            $languageTranslate->language = $language;
                            $languageTranslate->translation = $translation;
                            if ($languageTranslate->save()) {
                                $messagesOutputs .= $i . ' Complete create Language Translate' . PHP_EOL;
                            } else {
                                $messagesOutputs .= $i . ' Error create Language Translate' . PHP_EOL;
                            }
                        } else {
                            $languageTranslate->translation = $translation;
                            if ($languageTranslate->save()) {
                                $messagesOutputs .= $i . ' Complete update Language Translate' . PHP_EOL;
                            } else {
                                $messagesOutputs .= $i . ' Error update Language Translate' . PHP_EOL;
                            }
                        }
                    }
                }

                Console::endProgress();
                echo PHP_EOL . $messagesOutputs . PHP_EOL;
            }
        }
    }

    public function downDbTranslate()
    {
        $db = Yii::$app->db;

        if (!$db instanceof \yii\db\Connection) {
            throw new \Exception('The "db" option must refer to a valid database application component.');
        }

        foreach ($this->translations as $language => $categories) {
            Console::output("Language: $language");
            foreach ($categories as $category => $msgs) {
                $messagesCount = count($msgs, COUNT_RECURSIVE);
                $i = 0;
                Console::output("Category: $category");
                Console::startProgress(0, $messagesCount);
                $messagesOutputs = '';
                foreach ($msgs as $source_msgs => $translation) {
                    Console::updateProgress(++$i, $messagesCount);

                    if (
                        ($languageSource = LanguageSource::find()
                            ->where(['category' => $category, 'message' => $source_msgs])
                            ->one()) !== null
                    ) {
                        $languageSource->delete();
                    }
                }

                Console::endProgress();
                echo PHP_EOL . $messagesOutputs . PHP_EOL;
            }
        }
    }
}
