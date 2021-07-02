<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TFieldList;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Wrapper\TDBCheckGroup;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;

class FiliadoForm extends TPage
{
  private $form;

  public function __construct()
  {
    parent::__construct();

    parent::setTargetContainer('adianti_right_panel');

    $this->form = new BootstrapFormBuilder('form_filiado');
    $this->form->setFormTitle('Filiado');

    $id   = new TEntry('id');
    $inscricao = new TEntry('numero_da_inscricao');
    $nome = new TEntry('nome_do_afiliado');
    $sigla_partido = new TEntry('sigla_do_partido');
    $nome_partido = new TEntry('nome_do_partido');
    $uf = new TCombo('uf');
    $codigo_municipio = new TEntry('codigo_do_municipio');
    $municipio = new TCombo('nome_do_municipio');
    $zona_eleitoral  = new TEntry('zona_eleitoral');
    $secao_eleitoral = new TEntry('secao_eleitoral');
    $data_filiacao = new TDate('data_da_filiacao');
    $situacao_registro = new TCombo('situacao_do_registro');
    $tipo_registro = new TCombo('tipo_do_registro');
    $data_processamento = new TDate('data_do_processo');
    $data_desfiliacao = new TDate('data_da_desfiliacao');
    $data_cancelamento = new TDate('data_do_cancelamento');
    $data_regularizacao = new TDate('data_da_regularizacao');
    $motivo_cancelamento = new TCombo('motivo_do_cancelamento');

    $situacao_registro->addItems([
      'REGULAR' => 'REGULAR',
      'EXCLUIDO' => 'EXCLUIDO',
      'SUB_JUDICE' => 'SUB_JUDICE',
      'CANCELADO' => 'CANCELADO',
      'DESFILIADO' => 'DESFILIADO',
      'TRANSFERIDO' => 'TRANSFERIDO',
      'RECEPCIONADO' => 'RECEPCIONADO',
      'ERRO' => 'ERRO',
      'AGUARDANDO ACEITE DO PARTIDO' => 'AGUARDANDO ACEITE DO PARTIDO',
    ]);

    $tipo_registro->addItems(['OFICIAL' => 'OFICIAL', 'INTERNA' => 'INTERNA']);

    $motivo_cancelamento->addItems([
      'JUDICIAL' => 'JUDICIAL',
      'A PEDIDO DO ELEITOR' => 'A PEDIDO DO ELEITOR',
      'A PEDIDO DO PARTIDO' => 'A PEDIDO DO PARTIDO',
      'CANCELAMENTO AUTOMÁTICO' => 'CANCELAMENTO AUTOMÁTICO',
      'CANCELAMENTO AUTOMÁTICO POR INSCRIÇÃO INEXISTENTE' => 'CANCELAMENTO AUTOMÁTICO POR INSCRIÇÃO INEXISTENTE',
      'CANCELAMENTO AUTOMÁTICO A PEDIDO DO PARTIDO' => 'CANCELAMENTO AUTOMÁTICO A PEDIDO DO PARTIDO',
      'CANCELAMENTO AUTOMÁTICO POR FALECIMENTO' => 'CANCELAMENTO AUTOMÁTICO POR FALECIMENTO',
      'CANCELAMENTO AUTOMÁTICO POR SUSPENSÃO DE DIREITOS POLÍTICOS' => 'CANCELAMENTO AUTOMÁTICO POR SUSPENSÃO DE DIREITOS POLÍTICOS',
    ]);

    $id->setEditable(false);

    $uf->setId('estado');

    $codigo_municipio->setEditable(false);
    $codigo_municipio->setId('codMunicipio');
    $municipio->setId('cidade');
    $municipio->{'onchange'} = 'changeCity()';

    $data_filiacao->setSize('100%');
    $data_filiacao->setMask('dd/mm/yyyy');
    $data_filiacao->setDatabaseMask('yyyy-mm-dd');
    $data_desfiliacao->setSize('100%');
    $data_desfiliacao->setMask('dd/mm/yyyy');
    $data_desfiliacao->setDatabaseMask('yyyy-mm-dd');
    $data_cancelamento->setSize('100%');
    $data_cancelamento->setMask('dd/mm/yyyy');
    $data_cancelamento->setDatabaseMask('yyyy-mm-dd');
    $data_regularizacao->setSize('100%');
    $data_regularizacao->setMask('dd/mm/yyyy');
    $data_regularizacao->setDatabaseMask('yyyy-mm-dd');
    $data_processamento->setSize('100%');
    $data_processamento->setMask('dd/mm/yyyy');
    $data_processamento->setDatabaseMask('yyyy-mm-dd');

    $this->form->appendPage('Dados básicos');
    $this->form->addFields([new TLabel('Id')], [$id], [new TLabel('Inscrição')], [$inscricao]);
    $this->form->addFields([new TLabel('Nome')], [$nome]);
    $this->form->addFields([new TLabel('Sigla do Partido')], [$sigla_partido], [new TLabel('Nome do Partido')], [$nome_partido],);
    $this->form->addFields([new TLabel('UF')], [$uf]);
    $this->form->addFields([new TLabel('Município')], [$municipio], [new TLabel('Cód. Municipio')], [$codigo_municipio]);

    $this->form->appendPage('Outras informações');
    $this->form->addFields([new TLabel('Zona Eleitoral')], [$zona_eleitoral], [new TLabel('Seção Eleitoral')], [$secao_eleitoral]);
    $this->form->addFields([new TLabel('Data filiação')], [$data_filiacao]);
    $this->form->addFields([new TLabel('Situação do registro')], [$situacao_registro]);
    $this->form->addFields([new TLabel('Tipo do registro')], [$tipo_registro]);
    $this->form->addFields([new TLabel('Data do processamento')], [$data_processamento], [new TLabel('Data de desfiliação')], [$data_desfiliacao]);
    $this->form->addFields([new TLabel('Data de cancelamento')], [$data_cancelamento], [new TLabel('Data de regularização')], [$data_regularizacao]);
    $this->form->addFields([new TLabel('Motivo do Cancelamento')], [$motivo_cancelamento]);

    $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
    $this->form->addHeaderActionLink('Fechar', new TAction([$this, 'onClose']), 'fa:times red');

    TScript::create("
      function getState() {
        $.ajax({
          url:\"https://servicodados.ibge.gov.br/api/v1/localidades/estados\",
          type: \"GET\",
          dataType: \"JSON\",
          success:function(res) {
            
            let states = res;
            
            $('#estado').empty();
            
            for ( const state of states ) {
    
              var name = state.nome;
              var initial = state.sigla;
              var id = state.id;

              $('#estado').append('<option value='+name+' data-id='+id+'>'+name+'</option>');

            }

            getInitials();

          },
          error:function() {
            console.warn(\"erro no servidor do IBGE.\");
          }
        });
      } 

      getState();

      function changeState() {
  
        var selectInitials = document.getElementById(\"estado\");
        
        selectInitials.onchange = function() {
            getInitials();
        }
    
      }

      changeState();

      function getInitials () {

        var selectInitials = $(\"#estado > option:selected\")[0];
    
        var selectCities = $(\"#cidade\")[0];
    
        $.ajax({
          url:\"https://servicodados.ibge.gov.br/api/v1/localidades/estados/\"+selectInitials.getAttribute('data-id')+\"/municipios\",
          type: \"GET\",
          dataType: \"JSON\",
          success:function(res) {
            
            let cities = res;
    
            selectCities.innerHTML = \"\";

            for ( const city of cities ) {
  
              var name = city.nome;
              var idCity = city.id;
              
              var nameCity = document.createElement(\"option\");
              nameCity.setAttribute(\"value\", idCity);
              nameCity.innerText = name;

              selectCities.appendChild(nameCity);
              
            }

            $('#codMunicipio').val( $('#cidade').val() );

          },
          error:function() {
            console.warn(\"erro no servidor do IBGE.\");
          }
        });
      
      }

      function changeCity() {
        $('#codMunicipio').val( $('#cidade').val() );
      }

    ");

    parent::add($this->form);
  }

  public static function onSave($param)
  {
    try {
      TTransaction::open('communication');
      $filiado = new FiliadosPartido;
      $filiado->fromArray($param);
      $filiado->store();

      $data = new stdClass;
      $data->id = $filiado->id;
      TForm::sendData('form_filiado', $data);

      TScript::create('Template.closeRightPanel()');


      $pos_action = new TAction(['FiliadoList', 'onReload']);

      new TMessage('info', 'Registro gravado com sucesso', $pos_action);

      TTransaction::close();
    } catch (Exception $e) {
      new TMessage('error', $e->getMessage());
    }
  }

  public function onClear($param)
  {
    $this->form->clear();
  }

  public function onEdit($param)
  {
    try {
      if (isset($param['key'])) {
        TTransaction::open('communication');

        $filiado = new FiliadosPartido($param['key']);

        $this->form->setData($filiado);

        TTransaction::close();
      } else {
        $this->onClear($param);
      }
    } catch (Exception $e) {
      new TMessage('error', $e->getMessage());
    }
  }

  public static function onClose($param)
  {
    TScript::create('Template.closeRightPanel()');
  }
}
