<?php
// redirection si un seul type de contenu possible
if (isset($data['content'])) {
    $contenu = $data['content'];
    $nb_types_contenus = 0;
}
if (count($contenu) > 1) {
    foreach ($contenu as $type) {
        $option = substr($type, 1 + strlen($this->getModel('cms')->table_cms_contenu));
        // si le type de contenu n'a pas de nom on ne le propose pas
        if (!empty(Clementine::$config['module_contenus'][$option . '_name'])) {
            ++$nb_types_contenus;
        }
    }
}
if (!$nb_types_contenus) {
    $url = __WWW__ . '/contenus/addcontenu?id_zone=';
    if ($request->get('int', 'id')) { 
        $url .= $request->get('int', 'id'); 
    } else { 
        $url .= '0'; 
    } 
    $url .= '&id_page=';
    if ($request->get('int', 'page')) { 
        $url .= $request->get('int', 'page'); 
    } else { 
        $url .= '0'; 
    } 
    $url .= '&type_content=' . $contenu[0];
?>
<script type="text/javascript">
    window.document.location.href = '<?php echo $url; ?>';
</script>
<?php
    return;
}
?>
<div class="form_contenu_edit">
<?php
if (count($contenu) > 1) {
    foreach ($contenu as $type) {
        $option = substr($type, 1 + strlen($this->getModel('cms')->table_cms_contenu));
        // si le type de contenu n'a pas de nom on ne le propose pas
        if (!empty(Clementine::$config['module_contenus'][$option . '_name'])) {
?>
        <p>
            <a class="<?php echo $option; ?>" href="<?php echo __WWW__; ?>/contenus/addcontenu?id_zone=<?php 
        if ($request->get('int', 'id')) { 
            echo $request->get('int', 'id'); 
        } else { 
            echo '0'; 
        } 
?>&id_page=<?php
        if ($request->get('int', 'page')) { 
            echo $request->get('int', 'page'); 
        } else { 
            echo '0'; 
        } 
?>&type_content=<?php echo $type; ?>"><?php
        echo Clementine::$config['module_contenus'][$option . '_name'];
?></a>
        </p>
<?php 
        }
    }
}
?>
</div>
