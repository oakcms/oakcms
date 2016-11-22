<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 12.10.2016
 * Project: kotsyubynsk
 * File name: view.php
 */
?>

<div class="flexslider">
    <div class="trees-top">
        <div class="container">
            <svg class="right" width="80px" height="102px" viewBox="0 0 50 62" version="1.1"
                 xmlns="http://www.w3.org/2000/svg">
                <g id="Symbols-2" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="header-2" transform="translate(0.000000, -28.000000)" fill="#00c564 ">
                        <g id="logo-2">
                            <g transform="translate(0.000000, 28.000000)">
                                <path class="tree"
                                      d="M12.1428571,37 L5,37 L17.7241379,19 L12,19 L25.5,0 L39,19 L33.2758621,19 L46,37 L37.8571429,37 L50,54 L28,54 L28,62 L22,62 L22,54 L0,54 L12.1428571,37 Z"
                                      id="Combined-Shape-2"></path>
                            </g>
                        </g>
                    </g>
                </g>
            </svg>

            <svg class="right" width="70px" height="92px" viewBox="0 0 50 62" version="1.1"
                 xmlns="http://www.w3.org/2000/svg">
                <g id="Symbols-3" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="header-3" transform="translate(0.000000, -28.000000)" fill="#00c564 ">
                        <g id="logo-3">
                            <g transform="translate(0.000000, 28.000000)">
                                <path class="tree"
                                      d="M12.1428571,37 L5,37 L17.7241379,19 L12,19 L25.5,0 L39,19 L33.2758621,19 L46,37 L37.8571429,37 L50,54 L28,54 L28,62 L22,62 L22,54 L0,54 L12.1428571,37 Z"
                                      id="Combined-Shape-3"></path>
                            </g>
                        </g>
                    </g>
                </g>
            </svg>
        </div>
    </div>

    <ul class="slides">
        <?php foreach ($items as $item) :?>
        <li style="background-image: url('<?= $item['media'] ?>')">
            <div class="f-descr">
                <div class="container">
                    <span><?= $item['status'] ?></span>
                    <h1><?= $item['title'] ?></h1>
                </div>
                <?if($item['content'] != ''):?>
                <div class="descr">
                    <?= $item['content'] ?>
                </div>
                <?endif;?>
            </div>
        </li>
        <?endforeach;?>
    </ul>

    <div class="trees-bottom">
        <div class="container">
            <svg width="144px" height="153px" viewBox="0 0 50 62" version="1.1" xmlns="http://www.w3.org/2000/svg">
                <!-- Generator: sketchtool 39.1 (31720) - http://www.bohemiancoding.com/sketch -->
                <title>93B0B57F-A985-4191-B6E1-A7FC7838E0E8</title>
                <desc>Created with sketchtool.</desc>
                <defs>
                    <linearGradient x1="50%" y1="0%" x2="82.9765625%" y2="100%" id="linearGradient-4">
                        <stop stop-color="#32DDC3" offset="0%"></stop>
                        <stop stop-color="#00C564" offset="100%"></stop>
                    </linearGradient>
                </defs>
                <g id="Symbols-4" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="header-4" transform="translate(0.000000, -28.000000)" fill="url(#linearGradient-4)">
                        <g id="logo-4">
                            <g transform="translate(0.000000, 28.000000)">
                                <path class="tree"
                                      d="M12.1428571,37 L5,37 L17.7241379,19 L12,19 L25.5,0 L39,19 L33.2758621,19 L46,37 L37.8571429,37 L50,54 L28,54 L28,62 L22,62 L22,54 L0,54 L12.1428571,37 Z"
                                      id="Combined-Shape-4"></path>
                            </g>
                        </g>
                    </g>
                </g>
            </svg>

            <svg width="115px" height="142px" viewBox="0 0 50 62" version="1.1" xmlns="http://www.w3.org/2000/svg">
                <!-- Generator: sketchtool 39.1 (31720) - http://www.bohemiancoding.com/sketch -->
                <title>93B0B57F-A985-4191-B6E1-A7FC7838E0E8</title>
                <desc>Created with sketchtool.</desc>
                <defs>
                    <linearGradient x1="50%" y1="0%" x2="82.9765625%" y2="100%" id="linearGradient-5">
                        <stop stop-color="#32DDC3" offset="0%"></stop>
                        <stop stop-color="#00C564" offset="100%"></stop>
                    </linearGradient>
                </defs>
                <g id="Symbols-5" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="header-5" transform="translate(0.000000, -28.000000)" fill="url(#linearGradient-5)">
                        <g id="logo-5">
                            <g transform="translate(0.000000, 28.000000)">
                                <path class="tree"
                                      d="M12.1428571,37 L5,37 L17.7241379,19 L12,19 L25.5,0 L39,19 L33.2758621,19 L46,37 L37.8571429,37 L50,54 L28,54 L28,62 L22,62 L22,54 L0,54 L12.1428571,37 Z"
                                      id="Combined-Shape-5"></path>
                            </g>
                        </g>
                    </g>
                </g>
            </svg>
        </div>
    </div>
</div>
