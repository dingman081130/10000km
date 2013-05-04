<?php

$parents = $threaded_comments->getParents();
$parents_id = array_keys($parents);
rsort($parents_id);
$s = 0;
foreach ($parents_id as $parent_id) {
    if ($s == $comments_limit)
        break;

    if ($comments_prev != 0 && $parent_id >= $comments_prev)
        continue;

    $this->widget('TripCommentWidget', array(
        'comment' => $parents[$parent_id],
        'threaded_comments' => $threaded_comments,
    ));
    $s++;
}
/*
  foreach ($parents as $comment) {
  $this->widget('TripCommentWidget', array(
  'comment' => $comment,
  'threaded_comments' => $threaded_comments,
  ));
  }
 * 
 */
?>