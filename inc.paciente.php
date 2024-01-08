<?php
require_once('inc.connect.php');

// definição da variáveis
$acao = 'insert';
if (isset($_REQUEST['id_update']) and !empty($_REQUEST['id_update'])) {
    $id_paciente = $_REQUEST['id_update'] ;
}else{
    $id_paciente = 0;
}

$nome = '';
$id_consulta = '';
$celular = '';
$telefone = '';
$email = '';
$uf = '';

if ($id_paciente != '') {
    $acao = 'update';
    $query = 'MATCH (paciente:Paciente)
    WHERE paciente.nome = "' . $id_paciente . '"
    RETURN paciente.nome AS nome,
           paciente.id_consulta AS id_consulta,
           paciente.celular AS celular,
           paciente.telefone AS telefone,
           paciente.email AS email,
           paciente.uf AS uf';
    $response = executeCypherQuery($query);
    echo $id_consulta;
    if ($response) {
        $decodedResponse = json_decode($response, true);

        if (isset($decodedResponse['results'][0]['data'][0]['row'])) {
            $rowData = $decodedResponse['results'][0]['data'][0]['row'];
            $nome = $rowData[0] ?? '';
            $id_consulta = $rowData[1] ?? '';
            $celular = $rowData[2] ?? '';
            $telefone = $rowData[3] ?? '';
            $email = $rowData[4] ?? '';
            $uf = $rowData[5] ?? '';

            echo $id_consulta;
        }
    }
}

if ($acao == 'insert') {
    $valor_botao = 'Cadastrar';
} elseif ($acao == 'update') {
    $valor_botao = 'Alterar';
}

echo '<form action="acao.paciente.php" method="POST" enctype="multipart/form-data">
	<table class="table table-condensed table-striped table-bordered table-hover">
	<input type="hidden" name="acao" value="'.$acao.'">
	<input type="hidden" name="id_paciente" value="'.$id_paciente.'">	
	<tr>
		<td colspan="2" align="center"><h4>'.$valor_botao.' Paciente</h4></td>
	</tr>
	<tr>
		<td>Nome:</td>
		<td><input type="text" name="nome" value="'.$nome.'" size="30"></td>
	</tr>
		<tr>
			<td>Celular:</td>
			<td><input type="text" name="celular" value="'.$celular.'" size="30"></td>
		</tr>
		
		<tr>
			<td>Telefone:</td>
			<td><input type="text" name="telefone" value="'.$telefone.'"  size="30"></td>
		</tr>

		<tr>
			<td>Email:</td>
			<td><input type="text" name="email" value="'.$email.'" size="30"></td>
		</tr>

		<tr>
			<td>UF:</td>
			<td><input type="text" name="uf" value="'.$uf.'" size="30"></td>
		</tr>
	</table>

	<input type="submit" name="cadastrar" class="btn btn-success" value="'.$valor_botao.' Paciente">

</form>';

?>

<hr>
<table class=" table table-condensed table striped table-bordered table-hover" border="1px">
    <tr>
        <td colspan="8" align="center"><h4>Pacientes Cadastrados</h4></td>
    </tr>
    <tr align="center">
        <td>Nome</td>
        <td>Última Consulta</td>
        <td>Celular</td>
        <td>Telefone</td>
        <td>Email</td>
        <td>UF</td>
        <td>Ações</td>
    </tr>
    <?php
    $query = "MATCH (p:Paciente)-[:REALIZOU]->(c:Consulta)
    RETURN p.nome, c.data_consulta as id_consulta, p.celular, p.telefone, p.email, p.uf
    UNION
    MATCH (p:Paciente)
    WHERE NOT ((p)-[:REALIZOU]->(:Consulta))
    RETURN p.nome, null as id_consulta, p.celular, p.telefone, p.email, p.uf";
    $response = executeCypherQuery($query);
    
    if ($response) {
        $decodedResponse = json_decode($response, true);
        if (isset($decodedResponse['results'][0]['data'])) {
            $data = $decodedResponse['results'][0]['data'];
        
            if (count($data) > 0) {
                foreach ($data as $record) {
                    $rowData = $record['row'];
        
                    list($nome, $id_consulta, $celular, $telefone, $email, $uf) = $rowData;
        
                    echo '<tr>';
                    echo '<td>' . $nome . '</td>';
                    echo '<td>' . $id_consulta . '</td>';
                    echo '<td>' . $celular . '</td>';
                    echo '<td>' . $telefone . '</td>';
                    echo '<td>' . $email . '</td>';
                    echo '<td>' . $uf . '</td>';
                    echo '<td><a href="index.php?pg=paciente&id_update=' . $nome . '" class="btn btn-primary"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                         <a href="acao.paciente.php?acao=delete&id_paciente=' . $nome . '" class="btn btn-danger" onclick="return confirm(\'Tem certeza que deseja excluir este paciente?\');"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>';
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
    ?>
</table>
