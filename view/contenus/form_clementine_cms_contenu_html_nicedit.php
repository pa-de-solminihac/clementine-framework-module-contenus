        <script type="text/javascript" src="<?php echo __WWW_ROOT_CONTENUS__; ?>/skin/js/nicedit/nicEdit.js"></script>
        <script type="text/javascript">
            bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
        </script>
        <p class="form_content_edit_content_html_nicedit">
            <textarea name="contenu_html_nicedit"><?php 
    if (isset($data['contenu_html_nicedit'])) {
        echo $this->getModel('fonctions')->htmlentities($data['contenu_html_nicedit']); 
    } else {
        if ($this->canGetBlock('contenus/default_contenu_html_nicedit')) {
            $this->getBlock('contenus/default_contenu_html_nicedit', $data);
        }
    }
?></textarea>
        </p>
