        <p class="form_content_edit_content_html">
            <textarea name="contenu_html"><?php 
    if (isset($data['contenu_html'])) {
        echo $this->getModel('fonctions')->htmlentities($data['contenu_html']); 
    } else {
        if ($this->canGetBlock('contenus/default_contenu_html')) {
            $this->getBlock('contenus/default_contenu_html', $data);
        }
    }
?></textarea>
        </p>
