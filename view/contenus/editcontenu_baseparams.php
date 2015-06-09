<?php
$input_types = array(
    'text'      => '%Y-%m-%d %H:%M',
    'date'      => '%Y-%m-%d',
    'time'      => '%H:%M',
    'datetime'  => '%Y-%m-%d %H:%M',
);
if (isset($data['content'])) {
    $contenu = $data['content']['contenu'];
    $type_contenu = $data['type_content'];
    $default = $data['content_default']['default'];
    $nom = $default['nom_contenu'];
    $date_lancement = $default['date_lancement'] ? strftime($input_types['date'], strtotime($default['date_lancement'])) : '';
    $date_arret = $default['date_arret'] ? strftime($input_types['date'], strtotime($default['date_arret'])) : '';
?>
        <input type="hidden" name="id" value="<?php echo $request->get('int', 'id'); ?>" />
        <input type="hidden" name="id_zone" value="<?php echo $request->get('int', 'id_zone'); ?>" />
        <input type="hidden" name="id_page" value="<?php echo $request->get('int', 'id_page'); ?>" />
        <div class="contenus_params_index_list" id="clementine_cms_contenus_params">
            <div class="form-group">
                <label for="clementine_cms_contenu_nom">Titre</label>
                <input class="form-control" type="text" name="nom" id="clementine_cms_contenu_nom" value="<?php
                    if (isset($nom)) {
                        echo $nom;
                    }
                ?>" />
            </div>
            <div class="form-group">
                <label for="clementine_cms_contenu_date_lancement">Date de lancement</label>
                <input class="form-control bootstrap3datetimepicker" type="datetime" id="clementine_cms_contenu_date_lancement" name="clementine_cms_contenu_date_lancement" value="<?php echo $date_lancement; ?>" />
            </div>
            <div class="form-group">
                <label for="clementine_cms_contenu_date_arret">Date d'arrêt</label>
                <input class="form-control bootstrap3datetimepicker" type="datetime" id="clementine_cms_contenu_date_arret" name="clementine_cms_contenu_date_arret" value="<?php echo $date_arret; ?>" />
            </div>
        </div>
<?php
}
?>
