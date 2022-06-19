<?php

# Конвертация массива в строку
function convertTagsToString($tags) {
    if(empty($tags)){
        return '';
    }
    $stringTags = '';
    foreach ($tags as $tag) {
        $stringTags .= $tag . '*';
    }
    return $stringTags;
}

# Конвертация строки в массив
function convertTagsFromString($tags) {
    $array = explode('*', $tags);
    array_pop($array);
    return $array;
}

# Функция определения цвета отзыва по оценке
function defineReviewColor($rating) {
    if($rating < 4) {
        return 'red';
    }
    if($rating < 8) {
        return 'yellow';
    }
    return 'green';
}
