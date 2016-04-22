<ul>
<?php
foreach($topics as $entry){
?>
    <li><a href="/ci/index.php/topic/get/<?=$entry->id?>"><?=$entry->title?></a></li>
<?php
}
?>
</ul>