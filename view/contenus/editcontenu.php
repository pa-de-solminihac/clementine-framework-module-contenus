<div class="form_contenu_edit">
<?php
if (isset($data['content'])) {
    $contenu = $data['content']['contenu'];
    $type_contenu = $data['type_content'];
    $default = $data['content_default']['default'];
    $nom = $default['nom_contenu'];
    $date_lancement = $default['date_lancement'] ? strftime('%d/%m/%Y', strtotime($default['date_lancement'])) : '';
    $date_arret = $default['date_arret'] ? strftime('%d/%m/%Y', strtotime($default['date_arret'])) : '';
?>
    <form name="add_content" method="post" action="<?php echo __WWW__; ?>/contenus/valid_<?php echo $type_contenu; ?>?id=<?php echo $request->get('int', 'id'); ?>&amp;type=<?php echo $type_contenu; ?>" enctype="multipart/form-data">
        <div class="content-box">
            <div class="content-box-header">
                <h3>Informations de base</h3>
            </div>
            <div class="content-box-content">
<?php
    $this->getBlock('contenus/editcontenu_baseparams', $data);
?>
            </div>
        </div>
        <div class="content-box">
            <div class="content-box-header">
                <h3>Contenu</h3>
            </div>
            <div class="content-box-content">
<?php
    //récuppère le formulaire correspondant au type de contenu
    $this->getBlock('contenus/form_' . $type_contenu, $contenu);
?>
            </div>
        </div>
        <p id="form_content_edit_submit"><input type="submit" name="valider" value="Valider" /></p>
    </form>
<?php
} else {
?>
Le contenu que vous avez demandé n'existe pas
<?php
}
?>
</div>
