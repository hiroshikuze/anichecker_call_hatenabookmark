<?php
/**
 * hatenabookmark
 */

if(! (getenv("REMOTE_ADDR") != "" && array_shift(get_included_files()) === __FILE__)) exit;

if(isset($_GET["q"])) {
    $q = urlencode(htmlspecialchars($_GET["q"], ENT_QUOTES, "UTF-8"));
} else exit;

$agent = stream_context_create(
    array(
        'http'=>array(
            'user_agent'=>'anichecker(kuze.tsukaeru.jp/tools/iphone/anichecker/)'
        )
    )
);

$json = array();
$xml = file_get_contents("http://b.hatena.ne.jp/search/title?mode=rss&sort=popular&q=".$q, false, $agent);
$xml = simplexml_load_string($xml);
for($i = 0; $i < count($xml->item); $i++) {
    $title = (array) $xml->item->$i->title;
    $description = (array) $xml->item->$i->description;
    $link = (array) $xml->item->$i->link;
    $add = array(
      "title" => $title[0],
      "description" => $description[0],
      "link" => $link[0]);
    array_push($json, $add);
}

header("Content-type: application/json; charset=UTF-8");
echo json_encode($json);

exit;

?>