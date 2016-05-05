<?php
/**
 * hatenabookmark
 */
if(getenv("REMOTE_ADDR") === "" ||
  !(getenv("REMOTE_ADDR") != "" && current(get_included_files()) === __FILE__)) exit;
if(isset($_GET["q"])) {
    $q = urlencode(htmlspecialchars($_GET["q"], ENT_QUOTES, "UTF-8"));
} else exit;
$agent = stream_context_create(
    array(
        'http'=>array(
            'user_agent'=>'anichecker(//kuze.tsukaeru.jp/tools/iphone/anichecker/)'
        )
    )
);
$json = array();
$xml = file_get_contents("http://b.hatena.ne.jp/search/title?mode=rss&sort=popular&q=".$q, false, $agent);
$xml2 = $xml;
$xml2 = str_replace("content:encoded", "content", $xml2);
$xml2 = str_replace("dc:date", "date", $xml2);
$xml2 = str_replace("dc:subject", "subject", $xml2);
$xml2 = str_replace("hatena:bookmarkcount", "hatenabookmarkcount", $xml2);
$xml3 = simplexml_load_string($xml2);
$domDocument = new DOMDocument();
for($i = 0; $i < count($xml3->item); $i++) {
    $title = (array) $xml3->item->$i->title;
    $description = (array) $xml3->item->$i->description;
    $link = (array) $xml3->item->$i->link;
    $content = (array) $xml3->item->$i->content;
    $domDocument->loadHTML('<meta charset="UTF-8"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'.(is_array($content) ? current($content) : $content));
    $xmlString = $domDocument->saveXML();
    $xmlObject = simplexml_load_string($xmlString);
    $htmlObject = json_decode(json_encode($xmlObject), true);
    $htmlObject2 = current($htmlObject["body"]["blockquote"]["p"]);
    $image_src = NULL;
    $image_alt = NULL;
    if(isset($htmlObject2["a"])) {
        if(isset($htmlObject2["a"]["img"])) {
            if(isset($htmlObject2["a"]["img"]["@attributes"])) {
                if(isset($htmlObject2["a"]["img"]["@attributes"]["src"])) {
                    $url = $htmlObject2["a"]["img"]["@attributes"]["src"];
                    if(@file_get_contents($url, NULL, NULL, 0, 1) !== false) {
                        $image_src = $htmlObject2["a"]["img"]["@attributes"]["src"];
                        if(isset($htmlObject2["a"]["img"]["@attributes"]["alt"])) {
                            $image_alt = $htmlObject2["a"]["img"]["@attributes"]["alt"];
                        }
                    }
                }
            }
        }
    }
    $date = (array) $xml3->item->$i->date;
    $subject = (array) $xml3->item->$i->subject;
    $hatenabookmarkcount = (array) $xml3->item->$i->hatenabookmarkcount;
    $add = array(
      "title" => current($title),
      "description" => $description === NULL ? NULL : is_array($description) ? current($description) : $description,
      "link" => current($link),
      "date" => is_array($date) ? current($date) : $date,
      "subject" => $subject === NULL ? NULL : is_array($subject) ? current($subject) : $subject,
      "hatenabookmarkcount" => is_array($hatenabookmarkcount) ? current($hatenabookmarkcount) : $hatenabookmarkcount,
      "image_src" => $image_src,
      "image_alt" => $image_alt
      );
    array_push($json, $add);
}
header("Content-type: application/json; charset=UTF-8");
echo json_encode($json);
exit;
?>
