<?php
require_once 'Parser.php';
$arHtml = Parser::getPage([
    "url" => "http://httpbin.org/ip" // string Ссылка на страницу
]);

?><h3><?php echo json_encode($arHtml); ?></h3>
