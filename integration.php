<?php
//Генерация токена
//https://habr.com/ru/sandbox/141670/

$accessToken = '';
/*
 * Тут указываем либо ID пользователя, либо "self" для вывода фото владельца токена
 * Как получить ID? Да в том же инструменте, в котором вы получали токен
 */
$url = "https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=" . $accessToken;

$instagramCnct = curl_init(); // инициализация cURL подключения
curl_setopt($instagramCnct, CURLOPT_URL, $url); // адрес запроса
curl_setopt($instagramCnct, CURLOPT_RETURNTRANSFER, 1); // просим вернуть результат
$response = json_decode(curl_exec($instagramCnct)); // получаем и декодируем данные из JSON
curl_close($instagramCnct); // закрываем соединение

// обновляем токен и дату его создания в базе

$accessToken = $response->access_token; // обновленный токен

$url = "https://graph.instagram.com/me/media?fields=id,media_type,media_url,caption,timestamp,thumbnail_url,permalink&access_token=" . $accessToken;
$instagramCnct = curl_init(); // инициализация cURL подключения
curl_setopt($instagramCnct, CURLOPT_URL, $url); // адрес запроса
curl_setopt($instagramCnct, CURLOPT_RETURNTRANSFER, 1); // просим вернуть результат
$media = json_decode(curl_exec($instagramCnct)); // получаем и декодируем данные из JSON
curl_close($instagramCnct); // закрываем соединение
 
//количество фотографий для вывода
$limit = 10;

//размер изображений (высота и ширина одинаковые)
$size = 200;
/*
 * функция array_slice() задает количество элементов, которые нам нужно получить из массива
 * если нам нужно вывести все фото, тогда: foreach($media->data as $data) {
 */
 
$out = '';

$i = 0;

$instaFeed = array();
foreach ($media->data as $mediaObj) {
    if (!empty($mediaObj->children->data)) {
      foreach ($mediaObj->children->data as $children) {
        $instaFeed[$children->id]['img'] = $children->thumbnail_url ?: $children->media_url;
        $instaFeed[$children->id]['link'] = $children->permalink;
        $instaFeed[$mediaObj->id]['caption'] = $mediaObj->caption;
      }
    } else {
      $instaFeed[$mediaObj->id]['img'] = $mediaObj->thumbnail_url ?: $mediaObj->media_url;
      $instaFeed[$mediaObj->id]['link'] = $mediaObj->permalink;
      $instaFeed[$mediaObj->id]['caption'] = $mediaObj->caption;
    }
}


foreach(array_slice($instaFeed, 0, $limit) as $data) {
  var_dump($data);
}

return $out;
