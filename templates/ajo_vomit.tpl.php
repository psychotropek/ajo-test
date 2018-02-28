<div class="content" id="ajo-vomit">
<h1 class="title"><?php echo $title;?></h1>
<?php
//$handle = fopen(drupal_get_path('module', 'ajo_test').'/dummy-article-for-import.html', "r");

//h6 as well. will be at end of document
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
var_dump($doi_match);

//n.b. how will excel files get parsed? Just as HTML, see if you can do that.

?>


?>

</div>