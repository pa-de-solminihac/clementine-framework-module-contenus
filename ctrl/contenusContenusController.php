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
    function set_contenu_defaut ($request, $id_contenu) 
    {
        $ns = $this->getModel('fonctions');
        $contenus = $this->getModel('contenus');
        if (!$id_contenu) {
            $donnees['id_contenu'] = $ns->strip_tags($request->post('int', 'id'));
        } else {
            $donnees['id_contenu'] = $id_contenu;
        }
        $donnees['type_contenu'] = $ns->strip_tags($request->get('string', 'type'));
        $donnees['nom'] = $request->post('html', 'nom');
        $donnees['date_lancement'] = $request->post('string', 'clementine_cms_contenu_date_lancement');
        $donnees['date_arret'] = $request->post('string', 'clementine_cms_contenu_date_arret');
        return $contenus->setContentDefault($donnees);
    }

    /**
    * Function : addcontenuAction() 
    * 
    */
    function addcontenuAction($request, $params = null) 
    {
        $ns = $this->getModel('fonctions');
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            $contenus = $this->getModel('contenus');
            if ($request->get('string', 'type_content')) {
                $id_zone      = $ns->strip_tags($request->get('int', 'id_zone'));
                $id_page      = $ns->strip_tags($request->get('int', 'id_page'));
                $type_content = $ns->strip_tags($request->get('string', 'type_content'));
                // $this->data['content'] = $contenus->addContenu($donnees);
                $ns->redirect(__WWW__ . '/contenus/editcontenu?id_zone=' . $id_zone . '&id_page=' . $id_page . '&type=' . $type_content);
            } else {
                $this->data['id_page'] = $request->get('int', 'page');
                $this->data['content'] = $contenus->getListeTypeContent();
            }
        } else {
            $ns->redirect(__WWW__);
        }
    }

    /**
     * Function : editcontenuAction() 
     * 
     */
    function editcontenuAction($request, $params = null) 
    {
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            // recupere le contenu du script a injecter dans le footer
            $script = $this->getBlockHtml('contenus/jquery_ui_datepicker');
            // charge les js et css necessaires
            $cssjs = $this->getModel('cssjs');
            // jQuery
            $cssjs->register_foot('jquery', array(
                'src' => $this->getHelper('jquery')->getUrl()
            ));
            // traitements...
            $id_content = $request->get("int", "id"); 
            $type_content = $request->get("string", "type"); 
            $contenus = $this->getModel('contenus');
            $this->data['type_content'] = $type_content;
            $request = $this->getRequest();
            $lang = $request->LANG;
            $this->data['content'] = $contenus->getContent($id_content, $this->data['type_content'], $lang);
            $this->data['content_default'] = $contenus->getContentDefault($id_content, $this->data['type_content']);
            $this->data['page'] = $request->get('int', 'id_page');
        } else {
            $this->getModel('fonctions')->redirect(__WWW__);
        }
    } 

    /**
    * Function : publishcontenuAction() 
    * 
    */
    function publishcontenuAction($request, $params = null) 
    {
        $ns = $this->getModel('fonctions');
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            $id_content = $request->get("int", "id"); 
            $id_page = $request->get("int", "id_page"); 
            $type_content = $request->get("string", "type"); 
            $publish = $request->get("int", "publish"); 
            $contenus = $this->getModel('contenus');
            $contenus->publishContent($id_content, $type_content, $publish);
            if (isset($_SERVER['HTTP_REFERER'])) {
                $ns->redirect($_SERVER['HTTP_REFERER']);
            } else {
                $ns->redirect(__WWW__ . '/cms/editpage?id=' . $id_page);
            }
        } else {
            $ns->redirect(__WWW__);
        }
    } 

    /**
    * Function : deletecontenuAction() 
    * 
    */
    function deletecontenuAction($request, $params = null) 
    {
        $ns = $this->getModel('fonctions');
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            $id_content = $request->get("int", "id"); 
            $type_content = $request->get("string", "type"); 
            $contenus = $this->getModel('contenus');
            $this->data['content'] = $contenus->deleteContent($id_content, $type_content);
            if (isset($_SERVER['HTTP_REFERER'])) {
                $ns->redirect($_SERVER['HTTP_REFERER']);
            } else {
                $id_page = $request->get("int", "id_page"); 
                $ns->redirect(__WWW__ . '/cms/editpage?id=' . $id_page);
            }
        } else {
            $ns->redirect(__WWW__);
        }
    }

    /**
    * Function : valid_clementine_cms_contenu_htmlAction() 
    * 
    */
    function valid_clementine_cms_contenu_htmlAction($request, $params = null) 
    {
        $ns = $this->getModel('fonctions');
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            if (!empty($request->POST)) {
                $type_content = 'clementine_cms_contenu_html';
                $id           = $request->post('int', 'id');
                $id_page      = $request->post('int', 'id_page');
                $id_zone      = $request->post('int', 'id_zone');
                $nom          = $request->post('html', 'nom');
                $contenu_html = $request->post('html', 'contenu_html');
                $contenus = $this->getModel('contenus');
                // ajoute le contenu s'il n'existe pas deja
                $request = $this->getRequest();
                $lang = $request->LANG;
                if (!$id) {
                    $id = $contenus->addContenu($nom, $type_content, $id_zone, $id_page, $lang);
                }
                if ($this->set_contenu_defaut($request, $id)) {
                    $contenus->updateContenuHtml($id, $contenu_html, $lang);
                }
            }
            if ($id_page) {
                $ns->redirect(__WWW__ . '/cms/editpage?id=' . $id_page);
            } else {
                $ns->redirect(__WWW__ . '/cms');
            }
        } else {
            $ns->redirect(__WWW__);
        }
    }

    /**
    * Function : valid_clementine_cms_contenu_html_niceditAction() 
    * 
    */
    function valid_clementine_cms_contenu_html_niceditAction($request, $params = null) 
    {
        $ns = $this->getModel('fonctions');
        if ($this->getModel('users')->needPrivilege('manage_contents')) {
            if (!empty($request->POST)) {
                $type_content = 'clementine_cms_contenu_html_nicedit';
                $id           = $request->post('int', 'id');
                $id_zone      = $request->post('int', 'id_zone');
                $id_page      = $request->post('int', 'id_page');
                $nom          = $request->post('html', 'nom');
                $contenu_html = $request->post('html', 'contenu_html_nicedit');
                $contenus = $this->getModel('contenus');
                // ajoute le contenu s'il n'existe pas deja
                $request = $this->getRequest();
                $lang = $request->LANG;
                if (!$id) {
                    $id = $contenus->addContenu($nom, $type_content, $id_zone, $id_page, $lang);
                }
                if ($this->set_contenu_defaut($request, $id)) {
                    $contenus->updateContenuHtmlNicedit($id, $contenu_html, $lang);
                }
            }
            if ($id_page) {
                $ns->redirect(__WWW__ . '/cms/editpage?id=' . $id_page);
            } else {
                $ns->redirect(__WWW__ . '/cms');
            }
        } else {
            $ns->redirect(__WWW__);
        }
    }
}
?>
