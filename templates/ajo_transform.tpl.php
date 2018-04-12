<div class="content" id="ajo-transform">
<h1 class="title">TransFOOORRRRRM</h1>
<?php


//all of these will be slurped up as strings
$item = array();
$item['title'] = "Title of Article";
$item['summary'] = "This is a summary";
$item['body'] = "Here's the body of the article.<br>What to do about superscripts? To test.";
$item['teaser'] = "Teaser stuff here.";
$item['take_home_points'] = "Take-home points. Where do they go?";
$item['references'] = "References. Get samples.";
$item['digital_object_id'] = "45ADg";
$item['author_disclosure'] = null;
//todo: check key info

//todo: figures_tables will need to be one long text of code. how to set
//foreach item['figures']
//each figures should have a ordinal (title) such as figure 1
//a body which is html, and/or a filename which is something like something_figure1.png
//a caption, which should be rendered as html

//TODO: get end of figure title and such in parser- file name with contains_substr

//then munge them all together later...add title figure 1? see other formatting? check escape values
//For each figure, make a div...
//within that, add a heading of figure title class, blat out the code as div figure class with img src imagename.jpg, 
//add caption as div class caption
//with excel files we're just going to output things as they are in the document
$figures_html = "";


//todo: check format- will it be comma delimited?
$item['authors'] = "Brian E. Ward, MD; Joshua S. Dines, MD";
//check how to add new subcategories-where should they fall under? that's their own business
$item['topics'] = "Knee, Imaging";
$item['sections'] = "Surgery, Research, Studies";

$values = array(
    'type' => 'article',
    'uid' => 1,
    'status' => 1,
    'comment' => 1,
    'promote' => 0,
  );
$entity = entity_create('node', $values);
// Now create an entity_metadata_wrapper around the new node entity
// to make getting and setting values easier
$ewrapper = entity_metadata_wrapper('node', $entity);
$ewrapper->title->set($item['title']);

// Setting the body is a bit different from other properties or fields
$ewrapper->body->set(array('value' => $item['body']));
//n.b. body isn't set with summary, it's a different field
//$ewrapper->body->summary->set($item['summary']);
//the abstract is contained in the summary field
$ewrapper->field_article_summary->set(array('value' => $item['summary']) );

//Other fields that are text
//TODO: install feature on local installation
$ewrapper->field_article_teaser->set(array('value' => $item['teaser']));
$ewrapper->field_article_doi->set($item['digital_object_id']);
//n.b.Legacy authors is a rich text that contains author disclosure
$ewrapper->field_article_legacy_authors->set($item['author_disclosure']);

//Image fields: field type is image and

  //todo: check if we need path correct (not just filename). If possible filename collisions, prefix file names in fetcher
  //also check if entity type is file or image

  $file_efq = new EntityFieldQuery();
  $filename = "figure1.jpg";
  $file_efq->entityCondition('entity_type', 'file')
    ->propertyCondition('filename', $filename);
  $file_efq_result = $file_efq->execute();
  $fid = 0;
  if (count($file_efq_result)) {
    // This will only add the first match from EFQ for this filename
    $fid = array_pop($file_efq_result['file'])->fid;
  }
  // Set both file and title separately. TODO: need actual file
  $image_obj = file_load($fid);
  //$ewrapper->field_article_medium_image->file = $image_obj;
  //todo: get title from caption- ['big_image']['title'] 
  //$ewrapper->field_article_medium_image->title = $title;

//$ewrapper->field_article_medium_image->set();
//$ewrapper->field_article_big_image->set();


//Inside the article- all long text fields. Key Info = Take Home Points
$ewrapper->field_article_key_info->set(array('value' => $item['take_home_points']));

//TODO: Figures and tables requires several fields. We're going to have to add something in the $items
$ewrapper->field_article_figures_tables->set(array('value' => "Code that deals with figures and tables. Will be munged from several things"));
//TODO: What is the multimedia going to be? How do they want to insert it?
//$ewrapper->field_article_multimedia->set("some code here that inserts multimedia");
$ewrapper->field_references->set(array('value' => $item['references']));


//Term reference and taxonomy fields
$authors_vocab = taxonomy_vocabulary_machine_name_load('authors');
$vid = $authors_vocab->vid;

$authors_string = $item['authors'];
$authors_array = explode('; ', $authors_string);

$tid_array = array();
foreach($authors_array as $author) {
    $tid = custom_create_taxonomy_term($author, $vid);
    $tid_array[] = $tid;
}
$ewrapper->field_article_authors->set($tid_array);

//n.b. we don't need to worry about taxonomic subcategories. They should add automagically
//Insertion is another story, really
$topics_vocab = taxonomy_vocabulary_machine_name_load('topics');
$vid = $topics_vocab->vid;
$topics_string = $item['topics'];
$topics_array = explode(', ', $topics_string);
$tid_array = array();
foreach($topics_array as $topic) {
    $tid = custom_create_taxonomy_term($topic, $vid);
    $tid_array[] = $tid;
}


$ewrapper->field_article_topics->set($tid_array);

$sections_vocab = taxonomy_vocabulary_machine_name_load('sections');
$vid = $sections_vocab->vid;
$sections_string = $item['sections'];
$sections_array = explode(', ', $sections_string);
$tid_array = array();
foreach($sections_array as $section) {
    $tid = custom_create_taxonomy_term($section, $vid);
    $tid_array[] = $tid;
}
var_dump($tid_array);
$ewrapper->field_article_sections->set($tid_array);





// Now just save the wrapper and the entity
// There is some suggestion that the 'true' argument is necessary to
// the entity save method to circumvent a bug in Entity API. If there is
// such a bug, it almost certainly will get fixed, so make sure to check.
$ewrapper->save();



/*
   - check?
  field_display_mode

*/


 ?>


Author Bio	field_author_bio	
Article Source	field_article_source

field_article_issue	Entity Reference	
field_article_page_number	Text- is this even used?	
field_article_citation_override  Text

<?php 
function custom_create_taxonomy_term($name, $vid) {

    $term_name = taxonomy_get_term_by_name($name);
    //var_dump($term_name);
    if (is_array($term_name)) {
      $term_name = array_values($term_name)[0];
      if (isset($term_name->name)) {
          //if there is a term, return the id of the term
        return $term_name->tid;
      }else{
          //if there is no term it creates the term and returns the id
        $term = new stdClass();
        $term->name = $name;
        $term->vid = $vid;
        taxonomy_term_save($term);
        return $term->tid;
      }
    }
    else {
      //if there is no term it creates the term and returns the id
      $term = new stdClass();
      $term->name = $name;
      $term->vid = $vid;
      taxonomy_term_save($term);
      return $term->tid;
    }
  }


//todo: move to processor or parser
  function contains_substr($mainStr, $str, $loc = false) {
    if ($loc === false) return (strpos($mainStr, $str) !== false);
    if (strlen($mainStr) < strlen($str)) return false;
    if (($loc + strlen($str)) > strlen($mainStr)) return false;
    return (strcmp(substr($mainStr, $loc, strlen($str)), $str) == 0);
}

?>