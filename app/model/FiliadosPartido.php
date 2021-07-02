<?php

use Adianti\Database\TRecord;

/**
 * Cliente Active Record
 * @author  <your-name-here>
 */
class FiliadosPartido extends TRecord
{
  const TABLENAME = 'filia_lista_pt';
  const PRIMARYKEY = 'id';
  const IDPOLICY =  'serial'; // {max, serial}

  /**
   * Constructor method
   */
  public function __construct($id = NULL, $callObjectLoad = TRUE)
  {
    parent::__construct($id, $callObjectLoad);
    parent::addAttribute('data_da_extracao');
    parent::addAttribute('hora_da_extracao');
    parent::addAttribute('numero_da_inscricao');
    parent::addAttribute('nome_do_afiliado');
    parent::addAttribute('sigla_do_partido');
    parent::addAttribute('nome_do_partido');
    parent::addAttribute('uf');
    parent::addAttribute('codigo_do_municipio');
    parent::addAttribute('nome_do_municipio');
    parent::addAttribute('zona_eleitoral');
    parent::addAttribute('secao_eleitoral');
    parent::addAttribute('data_da_filiacao');
    parent::addAttribute('situacao_do_registro');
    parent::addAttribute('tipo_do_registro');
    parent::addAttribute('data_do_processo');
    parent::addAttribute('data_da_desfiliacao');
    parent::addAttribute('data_do_cancelamento');
    parent::addAttribute('data_da_regularizacao');
    parent::addAttribute('motivo_do_cancelamento');
  }

  public function onBeforeLoad($id)
  {
    // echo "Antes de carregar o regitro $id <br>";
  }

  public function onAfterLoad($object)
  {
    // print_r($object);
  }

  public function onBeforeStore($object)
  {
    // echo "<b>Antes de gravar o objeto</b> <br>";
    // print_r($object);
    // echo "<br>";
  }

  public function onAfterStore($object)
  {
    // echo "<b>Depois de gravar o objeto</b> <br>";
    // print_r($object);
    // echo "<br>";
  }

  public function onBeforeDelete($object)
  {
    // echo "<b>Antes de excluir o objeto</b> <br>";
    // print_r($object);
    // echo "<br>";
  }

  public function onAfterDelete($object)
  {
    // echo "<b>Depois de excluir o objeto</b> <br>";
    // print_r($object);
    // echo "<br>";
  }
}
