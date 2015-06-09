<?php
/**
 * contenusContenusModel : gestion de contenus
 *
 * @package 
 * @version $id$
 * @copyright 
 * @author Pierre-Alexis <pa@quai13.com> 
 * @license 
 */
class contenusContenusModel extends contenusContenusModel_Parent
{

    public $table_cms_page                      = 'clementine_cms_page';
    public $table_cms_template                  = 'clementine_cms_template';
    public $table_cms_zone                      = 'clementine_cms_zone';
    public $table_cms_instance_zone             = 'clementine_cms_instance_zone';
    public $table_cms_instance_zone_has_contenu = 'clementine_cms_instance_zone_has_contenu';
    public $table_cms_parametres_zone           = 'clementine_cms_parametres_zone';
    public $table_cms_contenu                   = 'clementine_cms_contenu';
    public $table_cms_parametres_contenu        = 'clementine_cms_parametres_contenu';

    /**
     * getTypeContent : renvoie le type de contenu du contenu $id_content
     * 
     * @access public
     * @return void
     */
    public function getTypeContent ($id_content) 
    {
        $id_content = (int) $id_content; 
        $db = $this->getModel('db');
        $sql = "SELECT table_contenu FROM `$this->table_cms_contenu` WHERE id_contenu = '$id_content' LIMIT 1 ";
        $stmt = $db->query($sql);
        $type_content = $db->fetch_assoc($stmt);
        return $type_content['table_contenu'];
    }

    /**
     * getListeTypeContent : renvoie la liste des types de contenu disponibles
     * 
     * @access public
     * @return void
     */
    public function getListeTypeContent () 
    {
        $db = $this->getModel('db');
        $sql = "SHOW TABLES LIKE '" . $this->table_cms_contenu . "_%'";
        $stmt = $db->query($sql);
        for (true; $res = $db->fetch_assoc($stmt); true) {
            $liste_type_content[] = $res['Tables_in_' . Clementine::$config['clementine_db']['name'] . ' (' . $this->table_cms_contenu . '_%)']; 
        }
        return $liste_type_content;
    }

    /**
     * getContentDefault : renvoie les données du contenu $id_content qui est de type $type_content
     * 
     * @param mixed $id_content 
     * @param mixed $type_content 
     * @access public
     * @return void
     */
    public function getContentDefault ($id_content, $type_content) 
    {
        $id_content = (int) $id_content; 
        $db = $this->getModel('db');
        $sql = "SELECT * FROM " . $this->table_cms_contenu . " WHERE id_contenu = '$id_content' AND table_contenu = '$type_content' ";
        $sql .= "LIMIT 1 ";
        $stmt = $db->query($sql);
        $contenu = array ('default' => $db->fetch_assoc($stmt)); 
        // stripslashes car les contenus sont echappes avant d'etre enregistres en BD
        if ($contenu['default']) {
            foreach ($contenu['default'] as $key => $val) {
                $contenu['default'][$key] = stripslashes($val);
            }
        }
        return $contenu;
    }

    /**
     * getContent : renvoie les données du contenu $id_content qui est de type $type_content
     * 
     * @param mixed $id_content 
     * @param mixed $type_content 
     * @access public
     * @return void
     */
    public function getContent ($id_content, $type_content, $lang) 
    {
        $id_content = (int) $id_content; 
        $db = $this->getModel('db');
        $sql = "SELECT * FROM $type_content WHERE id = '$id_content' AND lang = '$lang' LIMIT 1 ";
        $stmt = $db->query($sql);
        $contenu = array ('contenu' => $db->fetch_assoc($stmt)); 
        // stripslashes car les contenus sont echappes avant d'etre enregistres en BD
        if ($contenu['contenu']) {
            foreach ($contenu['contenu'] as $key => $val) {
                $contenu['contenu'][$key] = stripslashes($val);
            }
        }
        if ($cms = $this->getModel('cms')) {
            $contenu['contenu'] = $cms->unescape_content($contenu['contenu']);
        }
        return $contenu;
    }

    /**
     * publishContent : publie ou dépublie le contenu $id_content de type $type_content
     * 
     * @param mixed $id_content 
     * @param mixed $type_content 
     * @param mixed $publish 
     * @access public
     * @return void
     */
    public function publishContent ($id_content, $type_content, $publish) 
    {
        $id_content = (int) $id_content; 
        $db = $this->getModel('db');
        $sql = "UPDATE " . $this->table_cms_contenu . " SET valide = '$publish' WHERE id_contenu = '$id_content' AND table_contenu = '$type_content' LIMIT 1 ";
        $stmt = $db->query($sql);
    }

    /**
     * deleteContent : supprime le contenu $id_content qui est de type $type_content
     * 
     * @param mixed $id_content 
     * @param mixed $type_content 
     * @access public
     * @return void
     */
    public function deleteContent ($id_content, $type_content) 
    {
        $id_content = (int) $id_content; 
        $db = $this->getModel('db');
        $sql = "DELETE FROM $type_content WHERE id = '$id_content' LIMIT 1 ";
        $stmt = $db->query($sql);
        $sql = "DELETE FROM " . $this->table_cms_parametres_contenu . " WHERE contenu_id_contenu = '$id_content' AND contenu_table_contenu = '" . $type_content . "' ";
        $stmt = $db->query($sql);
        $sql = "DELETE FROM " . $this->table_cms_instance_zone_has_contenu . " WHERE id_contenu = '$id_content' AND table_contenu = '$type_content' ";
        $stmt = $db->query($sql);
        $sql = "DELETE FROM " . $this->table_cms_contenu . " WHERE id_contenu = '$id_content' AND table_contenu = '$type_content' LIMIT 1 ";
        $stmt = $db->query($sql);
    }

    /**
     * addContenu : ajoute un contenu
     * 
     * @param mixed $id_zone 
     * @param mixed $nom 
     * @param mixed $type_content 
     * @access public
     * @return void
     */
    public function addContenu ($nom, $type_content, $id_zone, $id_page, $lang) 
    {
        $id_zone      = (int) $id_zone;
        $id_page      = (int) $id_page;
        $lang         = preg_replace('/[^a-z]/', '', $lang);

        $db = $this->getModel('db');
        $sql = "SELECT id_instance_zone FROM " . $this->table_cms_instance_zone . " WHERE page_id_page = '$id_page' AND zone_id_zone = '$id_zone'";
        $stmt = $db->query($sql);
        $id_instance_zone = $db->fetch_array($stmt); 
        $id_instance_zone = $id_instance_zone[0]; 

        $sql  = "START TRANSACTION"; 
        $stmt = $db->query($sql); 
        $sql  = "SELECT max(id_contenu) FROM " . $this->table_cms_contenu; 
        $stmt = $db->query($sql); 
        $last_insert_id_array = $db->fetch_array($stmt); 
        $last_insert_id = $last_insert_id_array[0]; 
        $new_insert_id = $last_insert_id + 1;

        $sql = "INSERT INTO " . $type_content . " (id, lang) VALUES ('" . $new_insert_id . "', '" . $lang . "')";
        $req = $db->query($sql);

        $sql = "INSERT INTO " . $this->table_cms_contenu . " (id_contenu, lang, table_contenu, nom_contenu) 
                    VALUES ('" . $new_insert_id . "', '" . $lang . "', '" . $type_content . "', '" . $nom . "')";
        $req = $db->query($sql); 

        $sql = "INSERT INTO " . $this->table_cms_instance_zone_has_contenu . " (id_contenu, table_contenu, id_instance_zone, poids) 
                    VALUES ('" . $new_insert_id . "', '" . $type_content . "', '" . $id_instance_zone . "', '0')";
        $req = $db->query($sql); 

        $sql  = "COMMIT"; 
        $stmt = $db->query($sql); 

        // $ns = $this->getModel('fonctions');
        // $ns->redirect(__WWW__ . '/contenus/editcontenu?id=' . ($last_insert_id + 1) . '&id_page=' . $id_page . '&type=' . $type_content);
        return $new_insert_id;
    }

    /**
     * setContentDefault : update les infos de base du contenu $donnees['id_contenu']
     * 
     * @param mixed $donnees 
     * @access public
     * @return void
     */
    public function setContentDefault ($donnees) 
    {
        $db = $this->getModel('db');
        $sql  = "UPDATE `" . $this->table_cms_contenu . "` SET ";
        if ($donnees['nom']) {
            $sql .= "`nom_contenu` = '" . $db->escape_string($donnees['nom']) . "', ";
        }
        if ($donnees['date_lancement']) {
            $sql .= "`date_lancement` = '" . $db->escape_string($donnees['date_lancement']) . "' ";
        } else {
            $sql .= "`date_lancement` = NULL ";
        }
        $sql .= ", ";
        if ($donnees['date_arret']) {
            $sql .= "`date_arret`     = '" . $db->escape_string($donnees['date_arret']) . "' ";
        } else {
            $sql .= "`date_arret`     = NULL ";
        }
        $sql .= "WHERE `id_contenu` =  '" . (int) $donnees['id_contenu'] . "' 
                    AND table_contenu = '" . $db->escape_string($donnees['type_contenu']) . "'"; 
        return $db->query($sql);
    }

    /**
     * updateContenuHtml : update le contenu $id_content de type "contenu_html"
     * 
     * @param mixed $id_content 
     * @param mixed $contenu_html 
     * @access public
     * @return void
     */
    public function updateContenuHtml ($id_content, $contenu_html, $lang) 
    {
        $id_content = (int) $id_content; 
        if ($cms = $this->getModel('cms')) {
            $contenu_html = $cms->escape_content($contenu_html);
        }
        $db = $this->getModel('db');
        $sql  = "INSERT INTO " . $this->table_cms_contenu . "_html (`id`, `lang`, `contenu_html`) 
                 VALUES ('$id_content', '$lang', '" . $db->escape_string($contenu_html) . "') 
                 ON DUPLICATE KEY UPDATE `contenu_html` = '" . $db->escape_string($contenu_html) . "' "; 
        $stmt = $db->query($sql);
    }

    /**
     * updateContenuHtmlNicedit : update le contenu $id_content de type "contenu_html_nicedit"
     * 
     * @param mixed $id_content 
     * @param mixed $contenu_html 
     * @access public
     * @return void
     */
    public function updateContenuHtmlNicedit ($id_content, $contenu_html, $lang) 
    {
        $id_content = (int) $id_content; 
        if ($cms = $this->getModel('cms')) {
            $contenu_html = $cms->escape_content($contenu_html);
        }
        $db = $this->getModel('db');
        $sql  = "INSERT INTO " . $this->table_cms_contenu . "_html_nicedit (`id`, `lang`, `contenu_html_nicedit`) 
                 VALUES ('$id_content', '$lang', '" . $db->escape_string($contenu_html) . "') 
                 ON DUPLICATE KEY UPDATE `contenu_html_nicedit` = '" . $db->escape_string($contenu_html) . "' "; 
        $stmt = $db->query($sql);
    }

}
?>
