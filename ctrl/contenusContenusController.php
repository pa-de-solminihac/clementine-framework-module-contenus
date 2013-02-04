<?php
/**
 * contenusContenusController : gestion de contenus
 *
 * @package 
 * @version $id$
 * @copyright 
 * @author Pierre-Alexis <pa@quai13.com> 
 * @license 
 */
class contenusContenusController extends contenusContenusController_Parent
{
    /**
     * set_contenu_defaut : enregistre les données par défaut des contenus
     * 
     * @param mixed $donnees
     * @access public
     * @return void
     */
    function set_contenu_defaut ($id_contenu) 
    {
        $ns = $this->getModel('fonctions');
        $contenus = $this->getModel('contenus');
        if (!$id_contenu) {
            $donnees['id_contenu'] = $ns->strip_tags($ns->ifPost('int', 'id'));
        } else {
            $donnees['id_contenu'] = $id_contenu;
        }
        $donnees['type_contenu'] = $ns->strip_tags($ns->ifGet('string', 'type'));
        $donnees['nom'] = $ns->ifPost('html', 'nom');
        $donnees['date_lancement'] = $ns->ifPost('int', 'date_lancement_timestamp') / 1000;
        $donnees['date_arret'] = $ns->ifPost('int', 'date_arret_timestamp') / 1000;
        return $contenus->setContentDefault($donnees);
    }

    /**
    * Function : addcontenuAction() 
    * 
    */
    function addcontenuAction($request) 
    {
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            $ns = $this->getModel('fonctions');
            $contenus = $this->getModel('contenus');
            if ($ns->ifGet('string', 'type_content')) {
                $id_zone      = $ns->strip_tags($ns->ifGet('int', 'id_zone'));
                $id_page      = $ns->strip_tags($ns->ifGet('int', 'id_page'));
                $type_content = $ns->strip_tags($ns->ifGet('string', 'type_content'));
                // $this->data['content'] = $contenus->addContenu($donnees);
                $this->getModel('fonctions')->redirect(__WWW__ . '/contenus/editcontenu?id_zone=' . $id_zone . '&id_page=' . $id_page . '&type=' . $type_content);
            } else {
                $this->data['id_page'] = $ns->ifGet('int', 'page');
                $this->data['content'] = $contenus->getListeTypeContent();
            }
        } else {
            $this->getModel('fonctions')->redirect(__WWW__);
        }
    }

    /**
     * Function : editcontenuAction() 
     * 
     */
    function editcontenuAction($request) 
    {
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            // recupere le contenu du script a injecter dans le footer
            $script = $this->getBlockHtml('contenus/jquery_ui_datepicker');
            // charge les js et css necessaires
            if (Clementine::$config['module_jstools']['use_google_cdn']) {
                $this->getModel('cssjs')->register_js('jquery', array('src' => 'https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js'));
                $this->getModel('cssjs')->register_js('jquery.ui', array('src' => 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js'));
            } else {
                $this->getModel('cssjs')->register_js('jquery', array('src' => __WWW_ROOT_JSTOOLS__ . '/skin/jquery/jquery.min.js'));
                $this->getModel('cssjs')->register_js('jquery.ui', array('src' => __WWW_ROOT_JSTOOLS__ . '/skin/jquery-ui/js/jquery-ui-1.8.16.custom.min.js'));
            }
            $this->getModel('cssjs')->register_css('ui.datepicker', array('src' => __WWW_ROOT_JSTOOLS__ . '/skin/jquery-ui/css/ui-lightness/jquery-ui-1.8.16.custom.css'));
            $this->getModel('cssjs')->register_foot('ui.datepicker', $script);
            // traitements...
            $ns = $this->getModel('fonctions');
            $id_content = $ns->ifGet("int", "id"); 
            $type_content = $ns->ifGet("string", "type"); 
            $contenus = $this->getModel('contenus');
            $this->data['type_content'] = $type_content;
            $request = $this->getRequest();
            $lang = $request->LANG;
            $this->data['content'] = $contenus->getContent($id_content, $this->data['type_content'], $lang);
            $this->data['content_default'] = $contenus->getContentDefault($id_content, $this->data['type_content']);
            $this->data['page'] = $ns->ifGet('int', 'id_page');
        } else {
            $this->getModel('fonctions')->redirect(__WWW__);
        }
    } 

    /**
    * Function : publishcontenuAction() 
    * 
    */
    function publishcontenuAction($request) 
    {
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            $ns = $this->getModel('fonctions');
            $id_content = $ns->ifGet("int", "id"); 
            $id_page = $ns->ifGet("int", "id_page"); 
            $type_content = $ns->ifGet("string", "type"); 
            $publish = $ns->ifGet("int", "publish"); 
            $contenus = $this->getModel('contenus');
            $contenus->publishContent($id_content, $type_content, $publish);

            if (isset($_SERVER['HTTP_REFERER'])) {
                $ns->redirect($_SERVER['HTTP_REFERER']);
            } else {
                $ns->redirect(__WWW__ . '/cms/editpage?id=' . $id_page);
            }
        } else {
            $this->getModel('fonctions')->redirect(__WWW__);
        }
    } 

    /**
    * Function : deletecontenuAction() 
    * 
    */
    function deletecontenuAction($request) 
    {
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            $ns = $this->getModel('fonctions');
            $id_content = $ns->ifGet("int", "id"); 
            $type_content = $ns->ifGet("string", "type"); 
            $contenus = $this->getModel('contenus');
            $this->data['content'] = $contenus->deleteContent($id_content, $type_content);
            if (isset($_SERVER['HTTP_REFERER'])) {
                $ns->redirect($_SERVER['HTTP_REFERER']);
            } else {
                $id_page = $ns->ifGet("int", "id_page"); 
                $ns->redirect(__WWW__ . '/cms/editpage?id=' . $id_page);
            }
        } else {
            $this->getModel('fonctions')->redirect(__WWW__);
        }
    }

    /**
    * Function : valid_clementine_cms_contenu_htmlAction() 
    * 
    */
    function valid_clementine_cms_contenu_htmlAction($request) 
    {
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            $ns = $this->getModel('fonctions');
            if (!empty($_POST)) {
                $type_content = 'clementine_cms_contenu_html';
                $id           = $ns->ifPost('int', 'id');
                $id_page      = $ns->ifPost('int', 'id_page');
                $id_zone      = $ns->ifPost('int', 'id_zone');
                $nom          = $ns->ifPost('html', 'nom');
                $contenu_html = $ns->ifPost('html', 'contenu_html');
                $contenus = $this->getModel('contenus');
                // ajoute le contenu s'il n'existe pas deja
                $request = $this->getRequest();
                $lang = $request->LANG;
                if (!$id) {
                    $id = $contenus->addContenu($nom, $type_content, $id_zone, $id_page, $lang);
                }
                if ($this->set_contenu_defaut($id)) {
                    $contenus->updateContenuHtml($id, $contenu_html, $lang);
                }
            }
            if ($id_page) {
                $ns->redirect(__WWW__ . '/cms/editpage?id=' . $id_page);
            } else {
                $ns->redirect(__WWW__ . '/cms');
            }
        } else {
            $this->getModel('fonctions')->redirect(__WWW__);
        }
    }

    /**
    * Function : valid_clementine_cms_contenu_html_niceditAction() 
    * 
    */
    function valid_clementine_cms_contenu_html_niceditAction($request) 
    {
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            $ns = $this->getModel('fonctions');
            if (!empty($_POST)) {
                $type_content = 'clementine_cms_contenu_html_nicedit';
                $id           = $ns->ifPost('int', 'id');
                $id_zone      = $ns->ifPost('int', 'id_zone');
                $id_page      = $ns->ifPost('int', 'id_page');
                $nom          = $ns->ifPost('html', 'nom');
                $contenu_html = $ns->ifPost('html', 'contenu_html_nicedit');
                $contenus = $this->getModel('contenus');
                // ajoute le contenu s'il n'existe pas deja
                $request = $this->getRequest();
                $lang = $request->LANG;
                if (!$id) {
                    $id = $contenus->addContenu($nom, $type_content, $id_zone, $id_page, $lang);
                }
                if ($this->set_contenu_defaut($id)) {
                    $contenus->updateContenuHtmlNicedit($id, $contenu_html, $lang);
                }
            }
            if ($id_page) {
                $ns->redirect(__WWW__ . '/cms/editpage?id=' . $id_page);
            } else {
                $ns->redirect(__WWW__ . '/cms');
            }
        } else {
            $this->getModel('fonctions')->redirect(__WWW__);
        }
    }
}
?>
