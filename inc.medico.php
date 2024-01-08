<?php
require_once('inc.connect.php');

// definição da variáveis
$acao = 'insert';

if (isset($_REQUEST['id_update']) && !empty($_REQUEST['id_update'])) {
    $id_medico = $_REQUEST['id_update'];
} else {
    $id_medico = 0;
}

$nome = '';
$ie_ativo = '';
$ds_area_atuacao = '';

// relatório do update
if ($id_medico != '') {
    $acao = 'update';
    $query = 'MATCH (m:Medico) WHERE m.nome = "' . $id_medico . '" RETURN m.nome AS nome, m.ie_ativo AS ie_ativo, m.ds_area_atuacao AS ds_area_atuacao';
    $result = executeCypherQuery($query);
    if ($result) {
        $decodedResponse = json_decode($result, true);

        if (isset($decodedResponse['results'][0]['data'][0]['row'])) {
            $rowData = $decodedResponse['results'][0]['data'][0]['row'];
            $nome = $rowData[0] ?? '';
            $ie_ativo = $rowData[1] ?? '';
            $ds_area_atuacao = $rowData[2] ?? '';
        }
    }
}

if ($acao == 'insert') {
    $valor_botao = 'Cadastrar';
} elseif ($acao == 'update') {
    $valor_botao = 'Alterar';
}

// define os campos na tela
echo '
<form action="acao.medico.php" method="POST">
  
    <table class="table table-condensed table striped table-bordered table-hover" border="0">
        <input type="hidden" name="acao" value="'.$acao.'">
        <input type="hidden" name="id_medico" value="'.$id_medico.'">
        <tr>
            <td colspan="2" align="center"><h4>'.$valor_botao.' Médico</h4></td>
        </tr>
        <tr>
            <td>Nome:</td>
            <td><input type="text" name="nome" value="'.$nome.'" size="30"></td>
        </tr>
        <tr>
            <td>Ativo:</td>
            <td><input type="text" name="ie_ativo" value="'.$ie_ativo.'" size="30"></td>
        </tr>
        <tr>
            <td>Área de Atuação:</td>
            <td><input type="text" name="ds_area_atuacao" value="'.$ds_area_atuacao.'" size="30"></td>
        </tr>
    </table>
    <input type="submit" name="cadastrar" value="'.$valor_botao.' Médico" class="btn btn-success">
</form>';

// exibe os dados na tabela
echo '<hr>';
echo '<table class="table table-condensed table-striped table-bordered table-hover" border="1px">';
echo '<tr>';
echo '<td colspan="5" align="center"><h4>Médicos Cadastrados</h4></td>';
echo '</tr>';
echo '<tr align="center">';
echo '<td>Nome</td>';
echo '<td>Ativo</td>';
echo '<td>Área de Atuação</td>';
echo '<td>Especialidade</td>';
echo '<td>Ações</td>';
echo '</tr>';

    $query = "MATCH (m:Medico)-[:POSSUI]->(e:Especialidade)
    RETURN m.nome AS nome, m.ie_ativo AS ie_ativo, m.ds_area_atuacao AS ds_area_atuacao, e.ds_especialidade AS ds_especialidade
    UNION
    MATCH (m:Medico)
    WHERE NOT ((m)-[:POSSUI]->(:Especialidade))
    RETURN m.nome AS nome, m.ie_ativo AS ie_ativo, m.ds_area_atuacao AS ds_area_atuacao, null AS ds_especialidade
    ";
    $response = executeCypherQuery($query);
    
    if ($response) {
        $decodedResponse = json_decode($response, true);
    
        if (isset($decodedResponse['results'][0]['data'])) {
            $data = $decodedResponse['results'][0]['data'];
            if (count($data) > 0) {
                foreach ($data as $record) {
		        $rowData = $record['row'];
                   list($nome, $ie_ativo, $ds_area_atuacao, $ds_especialidade) = $rowData;
                    echo '<tr>';
                    echo '<td>' . $nome . '</td>';
                    echo '<td>' . $ie_ativo . '</td>';
                    echo '<td>' . $ds_area_atuacao . '</td>';
                    echo '<td>' . $ds_especialidade . '</td>';
                    echo '<td><a href="index.php?pg=medico&id_update=' . $nome . '" class="btn btn-primary"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
          <a href="acao.medico.php?acao=delete&id_medico=' . $nome . '" class="btn btn-danger" onclick="return confirm(\'Tem certeza que deseja excluir este paciente?\');"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr align="center"><td colspan="5">Nenhum registro para listar</td></tr>';
            }
        } else {
            echo '<tr align="center"><td colspan="5">Nenhum resultado encontrado para a consulta</td></tr>';
        }
    } else {
        echo 'Erro na execução da consulta';
    }
echo '</tr>';
echo '</table>';
?>