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
