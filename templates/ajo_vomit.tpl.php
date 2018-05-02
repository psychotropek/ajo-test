<div class="content" id="ajo-vomit">
<h1 class="title"><?php echo $title;?></h1>
<?php
require DRUPAL_ROOT.'/sites/all/vendor/autoload.php';

//$path = drupal_get_path('module', 'ajo_test')."/figure1.jpg";
//$my_xml = exif_custom_get_xmp($path);
//var_dump($my_xml['XMP:xmpmeta:rdf:description:description:alt:li']);
//field seems to be stored in
//XMP:xmpmeta:rdf:description:description:alt:li

$file = "fortier_2018_04_article_html.html";

/* $client = new GuzzleHttp\Client(['base_uri' => 'https://api.docconverter.pro','verify' => false]);
$res = $client->post('/Token', [ 'form_params' => [ 'grant_type' => 'password', 'username' => 'chris@quillandcode.com', 'password' => 'AJO_d3v070p'] ]);
$statusCode = $res->getStatusCode();
if ( $statusCode != 200 )
{
  echo 'Invalid request status code: ' . $statusCode;
  die();
}
$body = (string)$res->getBody();
$data = json_decode($body);
$token = $data->access_token;
// $token can be stored in site cache or database, please check expiry date ($data->expires_in) before using token again (2 weeks expiration time)

$table_match = 1;
$docconverterTemplate = "AJO test template";
if ($table_match > 0) 
{
  $docconverterTemplate = "AJO table template";
}
$formData = array();
$formData[] = [ 'name' => 'file_name', 'contents' => fopen($file, 'r')];
$formData[] = [ 'name' => 'template', 'contents' => $docconverterTemplate];
$formData[] = [ 'name' => 'returnHtml', 'contents' => 'true'];
$formData[] = [ 'name' => 'returnData', 'contents' => 'true'];

$response = $client->request('POST', '/api/converter/convertdoc', [
  'headers' => [ 'Authorization' => 'Bearer ' . $token ],
  'multipart' => $formData,
]);
          
$test = $response->getBody();
$handle = (string)$test; */

$handle = file_get_contents(drupal_get_path('module', 'ajo_test').'/'.$file);
if ($table_match > 0) 
{
  $table_array = array();
  preg_match_all("%(.*)<table>%",$handle,$above_table,PREG_PATTERN_ORDER);
  $table_array['above_table'] = $above_table;
  preg_match_all("%(<table>\n(.*)\n</table>)%",$handle,$table_data,PREG_PATTERN_ORDER);
  $table_array['table_data'] = $table_data;
  preg_match_all("%</table>\n(.*)>%",$handle,$table_caption,PREG_PATTERN_ORDER);
  $table_array['table_caption'] == $table_caption;
  preg_match_all("%(table)|(Table)(.*)_%",$path_parts['filename'],$table_index,PREG_PATTERN_ORDER);
  $item['tables'][$table_index] = $table_array;
}
else
{
  $handle = preg_replace("%\t%","",$handle);
  $handle = preg_replace("%<p>(\s)*&#xa0;(\s)*</p>%","",$handle);

  preg_match_all("%<h1>\s*(.*)\s*</h1>%",$handle,$title,PREG_PATTERN_ORDER);
  $item['title'] = trim($title[1][0]);

  preg_match_all("%<h6>\s*Authors\s*</h6>\s*<p>\s*(.*)\s*</p>%",$handle,$authors_match,PREG_PATTERN_ORDER);
  $item['authors'] = trim($authors_match[1][0]);

  //todo: don't have yet
  preg_match_all("%<h2>\s*Summary\s*</h2>(((?!<h)(.|\n))*)%",$handle,$summary_match,PREG_PATTERN_ORDER);
  $item['summary'] = $summary_match;

  //todo: don't have yet
  preg_match_all("%<h2>\s*Teaser\s*</h2>(((?!<h)(.|\n))*)%",$handle,$teaser_match,PREG_PATTERN_ORDER);
  $item['teaser'] = $teaser_match;

  preg_match_all("%<h2>\s*Take-Home Points\s*</h2>\s*(((?!<h)(.|\n))*)\s*%",$handle,$points_match,PREG_PATTERN_ORDER);
  $item['take_home_points'] = trim($points_match[1][0]);
  
//todo: reformat, not working
  //preg_match_all("%</ul>(\s|n)*<h2>(\s|.)*</h2>((?!<h)(.|\n))*%",$handle,$body_match,PREG_PATTERN_ORDER);
  preg_match_all("%*<h2>\s*Body(\s|.)*</h2>\s*((.|\n))*<h2>\s*References\s*</h2>%",$handle,$body_match,PREG_PATTERN_ORDER);
  $item['body'] = $body_match;
  
  //todo: not working, find marker for end of file
  preg_match_all("%<h2>\s*References\s*</h2>\s*(((?!<h)(.|\n))*)%",$handle,$ref_match,PREG_PATTERN_ORDER);
  $item['references'] = $ref_match;

  preg_match_all("%<h6>\s*Topics\s*</h6>\s*<p>\s*(((?!<h)(.|\n))*)\s*</p>%",$handle,$topics_match,PREG_PATTERN_ORDER);
  $item['topics'] = $topics_match[1][0];
    //needs element [0][1][0]

  preg_match_all("%<h6>\s*Sections\s*</h6>\s*<p>\s*(((?!<h)(.|\n))*)\s*</p>%",$handle,$sections_match,PREG_PATTERN_ORDER);
  $item['sections'] = trim($sections_match[1][0]);


  preg_match_all("%<h6>\s*Digital Object ID\s*</h6>\s*<p>\s*(((?!<h)(.|\n))*)\s*</p>%",$handle,$doi_match,PREG_PATTERN_ORDER);
  $item['digital_object_id'] = trim($doi_match[1][0]);
    //needs element [0] [0] [1]

var_dump($item);
}

function exif_custom_get_xmp($image) {
    $content = file_get_contents($image);
    $xmp_data_start = strpos($content, '<x:xmpmeta');
    $xmp_data_end = strpos($content, '</x:xmpmeta>');
    if ($xmp_data_start === FALSE || $xmp_data_end === FALSE) {
      return array();
    }
    $xmp_length = $xmp_data_end - $xmp_data_start;
    $xmp_data = substr($content, $xmp_data_start, $xmp_length + 12);
    unset($content);
    $xmp = simplexml_load_string($xmp_data);
    if ($xmp === FALSE) {
      return array();
    }
    // $namespaces = $xmp->getDocNamespaces(true);
    // $fields = array();
    // foreach ($namespaces as $namespace) {
    // $fields[] = exif_custom_xml_recursion($xmp->children($namespace));
    $field_data = array();
    exif_custom_xml_recursion($xmp, $field_data, 'XMP');
   
    return $field_data;
  }
  
  function exif_custom_xml_recursion($obj, array &$fields, $name) {
    $namespace = $obj->getDocNamespaces(TRUE);
    $namespace[NULL] = NULL;
  
    $children = array();
    $attributes = array();
  
    $text = trim((string) $obj);
    if (strlen($text) === 0) {
      $text = NULL;
    }
  
    if (strtolower((string) $obj->getName()) == "bag") {
      // @todo Add support for bags of objects other than just text?
      $childValues = array();
      $objChildren = $obj->children("rdf", TRUE);
      foreach ($objChildren as $child) {
        $childValues[] = trim((string) $child);
      }
      if (count($childValues) > 0) {
        $fields[$name] = $childValues;
      }
    }
    else {
      $name = $name . ':' . strtolower((string) $obj->getName());
  
      // Get info for all namespaces.
      if (is_object($obj)) {
        foreach ($namespace as $ns => $nsUrl) {
          // Attributes.
          $objAttributes = $obj->attributes($ns, TRUE);
          foreach ($objAttributes as $attributeName => $attributeValue) {
            $attribName = strtolower(trim((string) $attributeName));
            $attribVal = trim((string) $attributeValue);
            if (!empty($ns)) {
              $attribName = $ns . ':' . $attribName;
            }
            $attributes[$attribName] = $attribVal;
          }
  
          // Children.
          $objChildren = $obj->children($ns, TRUE);
          foreach ($objChildren as $childName => $child) {
            $childName = strtolower((string) $childName);
            if (!empty($ns)) {
              $childName = $ns . ':' . $childName;
            }
            $children[$childName][] = exif_custom_xml_recursion($child, $fields, $name);
          }
        }
      }
      if (!is_null($text)) {
        $fields[$name] = $text;
      }
    }
  
    return array(
      'name' => $name,
      'text' => html_entity_decode($text),
      'attributes' => $attributes,
      'children' => $children,
    );
  }





//$handle = fopen(drupal_get_path('module', 'ajo_test').'/dummy-article-for-import.html', "r");

//h6 as well. will be at end of document
/*
$handle = file_get_contents(drupal_get_path('module', 'ajo_test').'/dummy-article-for-import.html');
$handle = preg_replace("%\t%","",$handle);
$handle = preg_replace("%<p>(\s)*&#xa0;(\s)*</p>%","",$handle);


preg_match_all("%<h1>(.*)</h1>%",$handle,$title,PREG_PATTERN_ORDER);

//authors will be a list of terms in the taxonomy. So get it as string, and then array
//with explode or implode. Like Topics, we'll do the taxonomy processing later
preg_match_all("%<h6>.*Author.*</h6>\n(.*)\n%",$handle,$authors_match,PREG_PATTERN_ORDER);
var_dump($authors_match);

//summary starts with <h2>Summary </h2>, ends with <h
preg_match_all("%<h2>\s*Summary\s*</h2>(((?!<h)(.|\n))*)%",$handle,$summary_match,PREG_PATTERN_ORDER);
var_dump($summary_match);

//author's disclosure statemen t will need to be h6
//todo: Ask what an author disclosure statement will look like?

//teaser is <h2></h2>, can I say no bold? or always bold?
preg_match_all("%<h2>\s*Teaser\s*</h2>(((?!<h)(.|\n))*)%",$handle,$teaser_match,PREG_PATTERN_ORDER);
var_dump($teaser_match);

//take home points will be h2 and have bullet points
preg_match_all("%<h2>\s*Take-Home Points\s*</h2>(((?!<h)(.|\n))*)%",$handle,$points_match,PREG_PATTERN_ORDER);
var_dump($points_match);

//for body, we'll have to take everything starting after bullet points. 
//Unfortunately, this may not even be a heading! We'll want to make references a different heading type
//It may be abstract h2, or it might be something else. Find out what. We go until we 
//get to references
//This is a little different in that the opening heading will also go into the text
//todo: allow for second paragraphs, change negative lookahead
preg_match_all("%</ul>(\s|n)*<h2>(\s|.)*</h2>((?!<h)(.|\n))*%",$handle,$body_match,PREG_PATTERN_ORDER);
//var_dump($body_match);

//references are also h2
//big block o'text, ends with <h6> ? 
//so the regex should not be inclusive
preg_match_all("%<h2>\s*References\s*</h2>(((?!<h)(.|\n))*)%",$handle,$ref_match,PREG_PATTERN_ORDER);
var_dump($ref_match);

//get topics
//in topics, strip out p tags- use strip_tags();
//then make an array by exploding/imploding
//assign to array element (items)   
//we'll process them into the taxonomy later, there are some mappings for that
preg_match_all("%<h6>\s*Topics\s*</h6>(((?!<h)(.|\n))*)%",$handle,$topics_match,PREG_PATTERN_ORDER);
var_dump($topics_match);

//sections same as topics
preg_match_all("%<h6>\s*Sections\s*</h6>(((?!<h)(.|\n))*)%",$handle,$sections_match,PREG_PATTERN_ORDER);
var_dump($sections_match);

//digital object id
preg_match_all("%<h6>\s*Digital Object ID\s*</h6>(((?!<h)(.|\n))*)%",$handle,$doi_match,PREG_PATTERN_ORDER);
var_dump($doi_match); */

//n.b. how will excel files get parsed? Just as HTML, see if you can do that.

?>



</div>