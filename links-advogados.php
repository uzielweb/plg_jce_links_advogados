<?php
/**
 * @version     1.0.0
 * @package     Advogados Links for JCE
 * @author      Ponto Mega
 * @copyright   Copyright (c) 2025 Ponto Mega. All rights reserved.
 * @license     GNU/GPL license: https://www.gnu.org/licenses/gpl.html
 */

defined('_JEXEC') or defined('_WF_EXT') or die('ERROR_403');

use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Language\Text;

class WFLinkBrowser_advogados extends CMSObject
{
    public $plugin = null;

    public function __construct($config = array())
    {
        parent::__construct();

        $this->setProperties($config);

        // Load language files
        $language = Factory::getLanguage();
        // load the admin language file for plgugins/jce/links-advogados
        $language->load('plg_jce_links_advogados', JPATH_ADMINISTRATOR);
    }

    public function getOption()
    {
        return array('com_advogados');
    }

    public function getInstance()
    {
        static $instance;

        if (!is_object($instance)) {
            $instance = new WFLinkBrowser_advogados();
        }
        return $instance;
    }

    public function getList()
    {
        $html = '<li id="index.php?option=com_advogados&view=advogado"><div class="uk-tree-row"><a href="#"><span class="uk-tree-icon folder content nolink"></span><span class="uk-tree-text">' . Text::_('PLG_JCE_LINKS_ADVOGADOS_ADVOGADOS') . '</span></a></div></li>';

        return $html;
    }

    public function display()
    {
        // Load css if needed
        $document = WFDocument::getInstance();
        // $document->addStyleSheet(array('links-advogados'), 'plugins');
    }

    public function isEnabled()
    {
        // Para compatibilidade com Joomla 6, simplificar verificação
        // O parâmetro padrão no XML é 1 (habilitado)
        return true;
    }

    public function getLinks($args)
    {
        $app = Factory::getApplication();
        $db = Factory::getContainer()->get('DatabaseDriver');

        $items = array();
        $view = isset($args->view) ? $args->view : '';

        switch ($view) {
            case 'advogado':
            case 'search':
                // Busca por advogados
                $search = isset($args->search) ? $args->search : '';
                if (!empty($search)) {
                    $query = $db->getQuery(true);
                    $query->select('a.id, a.nome, a.alias, a.linguagem')
                        ->from('#__advogados AS a')
                        ->where('a.state = 1')
                        ->where('(a.nome LIKE ' . $db->quote('%' . $search . '%') . 
                               ' OR a.alias LIKE ' . $db->quote('%' . $search . '%') . ')')
                        ->order('a.nome ASC');

                    $db->setQuery($query);
                    $advogados = $db->loadObjectList();

                    foreach ($advogados as $advogado) {
                        // URL simples sem SEF
                        $advogado->href = 'index.php?option=com_advogados&view=advogado&id=' . $advogado->id;
                        // Nome com idioma para exibição na lista
                        $nomeExibicao = $advogado->nome;
                        if (!empty($advogado->linguagem)) {
                            $nomeExibicao .= ' (' . strtoupper($advogado->linguagem) . ')';
                        }
                        $items[] = array(
                            'id' => $advogado->href,
                            'name' => $nomeExibicao, // Nome com idioma para exibição na lista
                            'class' => 'file'
                        );
                    }
                } else {
                    // Quando view=advogado mas sem busca, retorna todos
                    $query = $db->getQuery(true);
                    $query->select('a.id, a.nome, a.alias, a.linguagem')
                        ->from('#__advogados AS a')
                        ->where('a.state = 1')
                        ->order('a.nome ASC');

                    $db->setQuery($query);
                    $advogados = $db->loadObjectList();

                    foreach ($advogados as $advogado) {
                        // URL simples sem SEF
                        $advogado->href = 'index.php?option=com_advogados&view=advogado&id=' . $advogado->id;
                        // Nome com idioma para exibição na lista
                        $nomeExibicao = $advogado->nome;
                        if (!empty($advogado->linguagem)) {
                            $nomeExibicao .= ' (' . strtoupper($advogado->linguagem) . ')';
                        }
                        $items[] = array(
                            'id' => $advogado->href,
                            'name' => $nomeExibicao, // Nome com idioma para exibição na lista
                            'class' => 'file'
                        );
                    }
                }
                break;

            default:
                // Buscar todos os advogados
                $query = $db->getQuery(true);
                $query->select('a.id, a.nome, a.alias, a.linguagem')
                    ->from('#__advogados AS a')
                    ->where('a.state = 1')
                    ->order('a.nome ASC');

                $db->setQuery($query);
                $advogados = $db->loadObjectList();

                foreach ($advogados as $advogado) {
                    // URL simples sem SEF
                    $advogado->href = 'index.php?option=com_advogados&view=advogado&id=' . $advogado->id;
                    // Nome com idioma para exibição na lista
                    $nomeExibicao = $advogado->nome;
                    if (!empty($advogado->linguagem)) {
                        $nomeExibicao .= ' (' . strtoupper($advogado->linguagem) . ')';
                    }
                    $items[] = array(
                        'id' => $advogado->href,
                        'name' => $nomeExibicao, // Nome com idioma para exibição na lista
                        'class' => 'file'
                    );
                }
                break;
        }

        return $items;
    }
}