<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Registry\TSession;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Datagrid\TDataGrid;
use Adianti\Widget\Datagrid\TDataGridAction;
use Adianti\Widget\Datagrid\TDataGridColumn;
use Adianti\Widget\Datagrid\TPageNavigation;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Util\TDropDown;
use Adianti\Wrapper\BootstrapDatagridWrapper;

class FiliadoList extends TPage
{
  private $datagrid;
  private $pageNavigation;

  use Adianti\Base\AdiantiStandardListTrait;

  public function __construct()
  {
    parent::__construct();

    $this->setDatabase('communication');
    $this->setActiveRecord('FiliadosPartido');
    $this->setDefaultOrder('id', 'asc');
    $this->addFilterField('id', '=', 'id');
    $this->addFilterField('numero_da_inscricao', '=', 'numero_da_inscricao');
    $this->addFilterField('nome_do_afiliado', 'like', 'nome_do_afiliado');
    $this->addFilterField('nome_do_partido', 'like', 'nome_do_partido');
    $this->addFilterField('situacao_do_registro', '=', 'situacao_do_registro');

    $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
    $this->datagrid->width = '100%;';
    $this->datagrid->style = 'min-width: 1500px;';

    $col_id               = new TDataGridColumn('id', 'ID do Filiado', 'center', '10%');
    $col_numero_inscricao = new TDataGridColumn('numero_da_inscricao', 'Num. Inscrição', 'center', '20%');
    $col_nome             = new TDataGridColumn('nome_do_afiliado', 'Nome', 'center', '20%');
    $col_partido          = new TDataGridColumn('nome_do_partido', 'Partido', 'center', '20%');
    $col_situacao          = new TDataGridColumn('situacao_do_registro', 'Situação', 'center', '20%');

    $col_id->setAction(new TAction([$this, 'onReload']), ['order' => 'id']);
    $col_numero_inscricao->setAction(new TAction([$this, 'onReload']), ['order' => 'numero_da_inscricao']);
    $col_nome->setAction(new TAction([$this, 'onReload']), ['order' => 'nome_do_afiliado']);
    $col_partido->setAction(new TAction([$this, 'onReload']), ['order' => 'nome_do_partido']);
    $col_situacao->setAction(new TAction([$this, 'onReload']), ['order' => 'situacao_do_registro']);

    $this->datagrid->addColumn($col_id);
    $this->datagrid->addColumn($col_numero_inscricao);
    $this->datagrid->addColumn($col_nome);
    $this->datagrid->addColumn($col_partido);
    $this->datagrid->addColumn($col_situacao);

    $action1 = new TDataGridAction(['FiliadoForm', 'onEdit'], ['key' => '{id}', 'register_state' => 'false']);
    $action2 = new TDataGridAction([$this, 'onDelete'], ['key' => '{id}']);

    
    $this->datagrid->addAction($action1, 'Editar', 'fa:edit blue');
    $this->datagrid->addAction($action2, 'Excluir', 'fa:trash-alt red');
    
    $action1->setUseButton(true);
    $action2->setUseButton(true);

    $this->datagrid->createModel();

    $this->form = new TForm;
    $this->form->add($this->datagrid);

    $id               = new TEntry('id');
    $numero_inscricao = new TEntry('numero_da_inscricao');
    $nome             = new TEntry('nome_do_afiliado');
    $partido          = new TEntry('partido');
    $situacao          = new TEntry('situacao');

    $id->exitOnEnter();
    $numero_inscricao->exitOnEnter();
    $nome->exitOnEnter();
    $partido->exitOnEnter();
    $situacao->exitOnEnter();

    $id->setSize('100%');
    $numero_inscricao->setSize('100%');
    $nome->setSize('100%');
    $partido->setSize('100%');
    $situacao->setSize('100%');

    $id->tabindex = -1;
    $numero_inscricao->tabindex = -1;
    $nome->tabindex = -1;
    $partido->tabindex = -1;
    $situacao->tabindex = -1;

    $id->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
    $numero_inscricao->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
    $nome->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
    $partido->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));
    // $situacao->setExitAction(new TAction([$this, 'onSearch'], ['static' => '1']));

    $tr = new TElement('tr');
    $this->datagrid->prependRow($tr);

    $tr->add(TElement::tag('td', ''));
    $tr->add(TElement::tag('td', ''));
    $tr->add(TElement::tag('td', $id));
    $tr->add(TElement::tag('td', $numero_inscricao));
    $tr->add(TElement::tag('td', $nome));
    $tr->add(TElement::tag('td', $partido));
    $tr->add(TElement::tag('td', ''));

    $this->form->addField($id);
    $this->form->addField($numero_inscricao);
    $this->form->addField($nome);
    $this->form->addField($partido);
    $this->form->addField($situacao);

    $this->form->setData(TSession::getValue(__CLASS__ . '_filter_data'));

    $this->pageNavigation = new TPageNavigation;
    $this->pageNavigation->setAction(new TAction([$this, 'onReload']));
    $this->pageNavigation->enableCounters();

    $panel = new TPanelGroup('Filiados');
    $panel->add($this->form);
    $panel->addFooter($this->pageNavigation);

    $panel->getBody()->style = 'overflow-x: auto';

    $panel->addHeaderActionLink('Novo', new TAction(['FiliadoForm', 'onClear'], ['register_state' => 'false']), 'fa:plus green');

    parent::add($panel);
  }
}
