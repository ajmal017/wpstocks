<?php


function yt_get_paragraphs($keywords, $number_responses_per_source, $number_paragraphs_per_source, $yt_api_key, $uploads){
  include("youtube.class.php");
  $youtube  = new YouTube();
  $results = $youtube->search($yt_api_key, $keywords, $uploads['path'], $uploads['url'], '75', $number_paragraphs_per_source*$number_paragraphs_per_source);
  shuffle($results['paragraphs']);
  return $results;
}

?>