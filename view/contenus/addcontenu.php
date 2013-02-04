<?php
$ns = $this->getModel('fonctions');
if (isset($data['content'])) {
    $contenu = $data['content'];
}
?>
<div class="form_contenu_edit">
<?php
if (count($contenu) > 1) {
    foreach ($contenu as $type) {
        $option = substr($type, 1 + strlen($this->getModel('cms')->table_cms_contenu));
?>
    <p>
        <a class="<?php echo $option; ?>" href="<?php echo __WWW__; ?>/contenus/addcontenu?id_zone=<?php 
    if ($ns->ifGet('int', 'id')) { 
        echo $ns->ifGet('int', 'id'); 
    } else { 
        echo '0'; 
    } 
?>&id_page=<?php
    if ($ns->ifGet('id', 'page')) { 
        echo $ns->ifGet('id', 'page'); 
    } else { 
        echo '0'; 
    } 
?>&type_content=<?php echo $type; ?>"><?php
    if (isset(Clementine::$config['module_contenus'][$option . '_name'])) {
        echo Clementine::$config['module_contenus'][$option . '_name'];
    } else {
        echo $option;
    }
?></a>
    </p>
<?php 
    }
} else {
    $url = __WWW__ . '/contenus/addcontenu?id_zone=';
    if ($ns->ifGet('int', 'id')) { 
        $url .= $ns->ifGet('int', 'id'); 
    } else { 
        $url .= '0'; 
    } 
    $url .= '&id_page=';
    if ($ns->ifGet('id', 'page')) { 
        $url .= $ns->ifGet('id', 'page'); 
    } else { 
        $url .= '0'; 
    } 
    $url .= '&type_content=' . $contenu[0];
?>
<script type="text/javascript">
    window.document.location.href = '<?php echo $url; ?>';
</script>
<?php
}
?>
</div>
