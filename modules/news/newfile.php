<?php
function test($input)
{
    $pic = '../inc/images/uploads/newskat/hp.jpg';
    $title = 'TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST';
    $date = '03.03.2012 : 12:22';
    $text = 'TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p>
            TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p> TEST TEXT |
            TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p>
            TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p>
            TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | TEST TEXT | <p>';

    $news = array();
    $news[] = array('title' => $title, 'date' => $date, 'text' => $text, 'newsimage' => $pic);


    return $news;
}
