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
//todo: check key info

//todo: figures_tables will need to be one long text of code. how to set
//foreach item['figures']
//each figures should have a ordinal (title) such as figure 1
//a body which is html, and/or a filename which is something like something_figure1.png
//a caption, which should be rendered as html

//TODO: get end of figure title and such in parser- file name

//then munge them all together later...add title figure 1? see other formatting? check escape values
//For each figure, make a div...
//within that, add a heading of figure title class, blat out the code as div figure class with img src imagename.jpg, 
//add caption as div class caption
//with excel files we're just going to output things as they are in the document
$figures_html = "";


//todo: check format- will it be comma delimited?
$item['authors'] = "Edwin Lee, Leila Baydur, Arjen Anduve";
$item['topics'] = "Shoulder, Knee, Foot";
$item['sections'] = "Surgery, Research, Studies";

//TODO: check uid? or just use a given admin one?
$values = array(
    'type' => 'Article',
    'uid' => $user->uid,
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
// because the body can have both its complete value and its
// summary
$ewrapper->body->set(array('value' => $item['body']));
$ewrapper->body->summary->set($item['summary']);
//TODO: check on what the different summaries are.
$ewrapper->field_article_summary->set($item['summary']);

//Other fields that are text
//TODO: install feature on local installation
$ewrapper->field_article_teaser->set($item['teaser']);
$ewrapper->field_article_doi->set($item['digital_object_id']);
//TODO: Author and Disclosure Information	field_article_legacy_authors	is correct?
$ewrapper->field_article_legacy_authors->set($item['author_disclosure']);

//Image fields
//TODO: how to set an image? it should have subfields? or captions? how does that work? 
//also filename? just set filename? set caption? how does that even work
//$ewrapper->field_article_medium_image->set();
//$ewrapper->field_article_big_image->set();


//TODO: take home points field name? Is that part of key info?
$ewrapper->field_article_take_home_points->set($item['take_home_points']);

//Inside the article- all long text fields
$ewrapper->field_article_key_info->set("blee long text key info");

//TODO: Figures and tables requires several fields. We're going to have to add something in the $items
$ewrapper->field_article_figures_tables->set("Code that deals with figures and tables. Will be munged from several things");
//TODO: What is the multimedia going to be? How do they want to insert it?
$ewrapper->field_article_multimedia->set("some code here that inserts multimedia");
$ewrapper->field_references->set($item['references']);

//TODO: authors, topics, and sections- what are the machine name taxonomies?

$authors_vocab = taxonomy_vocabulary_machine_name_load('authors');
$vid = $authors_vocab->vid;
$authors_string = $item['authors'];
$authors_array = explode(', ', $authors_string);
$tid_array = array();
foreach($authors_array as $author) {
    $tid = custom_create_taxonomy_term($author, $vid);
    $tid_array[] = $tid;
}
$wrapper->field_article_authors->set($tid_array);

$topics_vocab = taxonomy_vocabulary_machine_name_load('topics');
$vid = $topics_vocab->vid;
$topics_string = $item['topics'];
$topics_array = explode(', ', $topics_string);
$tid_array = array();
foreach($topics_array as $topic) {
    $tid = custom_create_taxonomy_term($topic, $vid);
    $tid_array[] = $tid;
}
$wrapper->field_article_topics->set($tid_array);

$sections_vocab = taxonomy_vocabulary_machine_name_load('sections');
$vid = $sections_vocab->vid;
$sections_string = $item['sections'];
$sections_array = explode(', ', $sections_string);
$tid_array = array();
foreach($sections_array as $section) {
    $tid = custom_create_taxonomy_term($section, $vid);
    $tid_array[] = $tid;
}
$wrapper->field_article_sections->set($tid_array);





// Now just save the wrapper and the entity
// There is some suggestion that the 'true' argument is necessary to
// the entity save method to circumvent a bug in Entity API. If there is
// such a bug, it almost certainly will get fixed, so make sure to check.
//$ewrapper->save();



/*
   - check?
  field_display_mode

*/


 ?>

ASK ABOUT THESE:
field_article_subhead	Will there be a subhead?	
Author Bio	field_author_bio	
Article Source	field_article_source


//all the stuff from take home points?
Inside the Article	field_article_inside	
OR
field_article_key_info	Long text	
*take home points?

Check this
Author and Disclosure Information	field_article_legacy_authors	Long text

field_article_issue	Entity Reference	
field_article_page_number	Text- is this even used?	
field_article_citation_override  Text

<?php 
function custom_create_taxonomy_term($name, $vid) {
    $term_name = taxonomy_get_term_by_name($name);
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




?>