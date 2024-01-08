<?php
require_once('inc.connect.php');

// definição da variáveis
$acao = 'insert';
$id_especialidade = isset($_REQUEST['id_update']) && !empty($_REQUEST['id_update']) ? $_REQUEST['id_update'] : 0;

$ds_especialidade = '';
$tipo_especialidade = '';
$ds_cid = '';
$ie_ativo = '';
$custo_medio = '';

// relatório do update
echo $id_especialidade;
if ($id_especialidade != '') {
    $acao = 'update';
    $query = 'MATCH (e:Especialidade)
	WHERE e.ds_especialidade = "'.$id_especialidade.'" 
	RETURN e.ds_especialidade AS ds_especialidade, 
		   e.tipo_especialidade AS tipo_especialidade, 
		   e.ie_ativo AS ie_ativo, 
		   e.custo_medio AS custo_medio';
    $response = executeCypherQuery($query);

	if ($response) {
		$decodedResponse = json_decode($response, true);

        if (isset($decodedResponse['results'][0]['data'][0]['row'])) {
            $rowData = $decodedResponse['results'][0]['data'][0]['row'];
            $ds_especialidade = $rowData[0] ?? '';
            $tipo_especialidade = $rowData[1] ?? '';
            $ie_ativo = $rowData[2] ?? '';
            $custo_medio = $rowData[3] ?? '';
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
<form action="acao.especialidade.php" method="POST" enctype="multipart/form-data">

    <table class="table table-condensed table-striped table-bordered table-hover" border="0">
        <input type="hidden" name="acao" value="'.$acao.'">
        <input type="hidden" name="id_especialidade" value="'.$id_especialidade.'">    
        <tr>
            <td colspan="2" align="center"><h4>'.$valor_botao.' Especialidade</h4></td>
        </tr>
        <tr>
            <td>Nome da Especialidade:</td>
            <td><input type="text" name="ds_especialidade" value="'.$ds_especialidade.'" size="30"></td>
        </tr>
        <tr>
            <td>Tipo da Especialidade</td>
            <td><input type="text" name="tipo_especialidade" value="'.$tipo_especialidade.'" size="30"></td>
        </tr>
        <tr>
            <td>Ativo</td>
            <td><input type="text" name="ie_ativo" value="'.$ie_ativo.'" size="30"></td>
        </tr>
        <tr>
            <td>Custo Médio</td>
            <td><input type="text" name="custo_medio" value="'.$custo_medio.'" size="30"></td>
        </tr>
        <tr>
            <td colspan="2" align="left">
                <input type="submit" name="cadastrar" value="'.$valor_botao.' Especialidade" class="btn btn-success">
            </td>
        </tr>
    </table>
</form>';

// exibe os dados na tabela
echo '<hr>';
echo '<table class="table table-condensed table-striped table-bordered table-hover" border="1px">';
echo '<tr>';
echo '<td colspan="8" align="center"><h4>Especialidades Cadastradas</h4></td>';
echo '</tr>';
echo '<tr align="center">';
echo '<td>Nome da Especialidade</td>';
echo '<td>Tipo da Especialidade</td>';
echo '<td>CID</td>';
echo '<td>Ativo</td>';
echo '<td>Custo Médio</td>';
echo '<td>Ações</td>';
echo '</tr>';
echo '<tr>';

// criação e exibição do relatório principal da tela
$query = "MATCH (e:Especialidade)-[:TEM_CID]->(c:CID)
RETURN e.ds_especialidade AS ds_especialidade, e.tipo_especialidade AS tipo_especialidade, c.ds_cid AS ds_cid, e.ie_ativo AS ie_ativo, e.custo_medio AS custo_medio
UNION
MATCH (e:Especialidade)
WHERE NOT ((e)-[:TEM_CID]->(:CID))
RETURN e.ds_especialidade AS ds_especialidade, e.tipo_especialidade AS tipo_especialidade, null AS ds_cid, e.ie_ativo AS ie_ativo, e.custo_medio AS custo_medio";

$response = executeCypherQuery($query);

if ($response) {
    $decodedResponse = json_decode($response, true);

    if (isset($decodedResponse['results'][0]['data'])) {
        $data = $decodedResponse['results'][0]['data'];

        if (!empty($data)) {
            foreach ($data as $record) {
                $rowData = $record['row'];

				list($ds_especialidade, $tipo_especialidade, $ds_cid, $ie_ativo, $custo_medio) = $rowData;

				echo '<tr>';
                    echo '<td>' . $ds_especialidade . '</td>';
                    echo '<td>' . $tipo_especialidade . '</td>';
                    echo '<td>' . $ds_cid . '</td>';
                    echo '<td>' . $ie_ativo . '</td>';
                    echo '<td>' . $custo_medio . '</td>';
                    echo '<td><a href="index.php?pg=especialidade&id_update=' . $ds_especialidade . '" class="btn btn-primary"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                         <a href="acao.especialidade.php?acao=delete&id_especialidade=' . $ds_especialidade . '" class="btn btn-danger" onclick="return confirm(\'Tem certeza que deseja excluir este paciente?\');"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>';
                    echo '</tr>';
				}
            } else {
                echo '<tr align="center"><td colspan="8">Nenhum registro para listar</td></tr>';
            }
        } else {
            echo '<tr align="center"><td colspan="8">Nenhum resultado encontrado</td></tr>';
        }
            
    } else {
            echo '<tr align="center"><td colspan="8">Erro na execução da consulta</td></tr>';
    }
echo '</tr>';
echo '</table>';
?>
