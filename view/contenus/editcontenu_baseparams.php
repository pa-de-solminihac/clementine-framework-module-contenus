<?php
if (isset($data['content'])) {
    $contenu = $data['content']['contenu'];
    $type_contenu = $data['type_content'];
    $default = $data['content_default']['default'];
    $nom = $default['nom_contenu'];
    $date_lancement = $default['date_lancement'] ? strftime('%d/%m/%Y', strtotime($default['date_lancement'])) : '';
    $date_arret = $default['date_arret'] ? strftime('%d/%m/%Y', strtotime($default['date_arret'])) : '';
?>
        <input type="hidden" name="id" value="<?php echo $request->get('int', 'id'); ?>" />
        <input type="hidden" name="id_zone" value="<?php echo $request->get('int', 'id_zone'); ?>" />
        <input type="hidden" name="id_page" value="<?php echo $request->get('int', 'id_page'); ?>" />
        <input type="hidden" name="date_lancement_timestamp" id="clementine_cms_contenu_date_lancement_timestamp" value="<?php echo (strtotime($default['date_lancement']) * 1000); ?>" />
        <input type="hidden" name="date_arret_timestamp" id="clementine_cms_contenu_date_arret_timestamp" value="<?php echo (strtotime($default['date_arret']) * 1000); ?>" />
        <table class="contenus_params_index_list" id="clementine_cms_contenus_params">
            <thead>
                <tr>
                    <th class="col1"> Nom </th>
                    <th class="col2"> Date lancement </th>
                    <th class="col3"> Date arrêt</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="col1">
                        <input type="text" name="nom" id="clementine_cms_contenu_nom" value="<?php
                            if (isset($nom)) {
                                echo $nom;
                            }
                        ?>" />
                    </td>
                    <td class="col2">
                        <input type="text" name="date_lancement" id="clementine_cms_contenu_date_lancement" value="<?php echo $date_lancement; ?>" />
                        <span class="reset_field" onclick="document.getElementById('clementine_cms_contenu_date_lancement').value=''; document.getElementById('clementine_cms_contenu_date_lancement_timestamp').value=''; " >vider</span>
                    </td>
                    <td class="col3">
                        <input type="text" name="date_arret" id="clementine_cms_contenu_date_arret" value="<?php echo $date_arret; ?>" />
                        <span class="reset_field" onclick="document.getElementById('clementine_cms_contenu_date_arret').value=''; document.getElementById('clementine_cms_contenu_date_arret_timestamp').value=''; " >vider</span>
                    </td>
                </tr>
            </tbody>
        </table>
<?php
}
?>
