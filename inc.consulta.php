<?php 
require_once('inc.connect.php'); 
	
// definição da variáveis
$acao='insert';
	
if (isset($_REQUEST['id_update']) and !empty($_REQUEST['id_update'])) {
	$id_consulta = $_REQUEST['id_update'] ;
}else{
	$id_consulta = 0;
}

$id_paciente = '';
$id_medico = '';
$data_contato = '';
$data_solicitacao = '';
$data_consulta = '';
$horario_consulta = '';
$data_confirmacao = '';
$horario_confirmacao='';
$forma_contato = '';
$observacao = '';
$forma_pagamento = '';
$valor = '';
$id_clinica = '';

if ($id_consulta > 0) {
	$query = 'MATCH (consulta:Consulta {id_consulta: {idConsulta}})
				RETURN consulta.id_paciente AS id_paciente,
						consulta.id_medico AS id_medico,
						consulta.data_solicitacao AS data_solicitacao,
						consulta.data_consulta AS data_consulta,
						consulta.horario_consulta AS horario_consulta,
						consulta.data_confirmacao AS data_confirmacao,
						consulta.horario_confirmacao AS horario_confirmacao,
						consulta.forma_contato AS forma_contato,
						consulta.observacao AS observacao,
						consulta.forma_pagamento AS forma_pagamento,
						consulta.valor AS valor,
						consulta.id_clinica AS id_clinica';

	$result = $session->run($query, ['idConsulta' => $id_consulta]);
	foreach ($result->getRecords() as $record) {
		$consultaData = [
			'id_paciente' => $record->value('id_paciente'),
			'id_medico' => $record->value('id_medico'),
			'data_solicitacao' => $record->value('data_solicitacao'),
			'data_consulta' => $record->value('data_consulta'),
			'horario_consulta' => $record->value('horario_consulta'),
			'data_confirmacao' => $record->value('data_confirmacao'),
			'horario_confirmacao' => $record->value('horario_confirmacao'),
			'forma_contato' => $record->value('forma_contato'),
			'observacao' => $record->value('observacao'),
			'forma_pagamento' => $record->value('forma_pagamento'),
			'valor' => $record->value('valor'),
			'id_clinica' => $record->value('id_clinica')
		];
	}

	$queryClinica = "MATCH (clinica:Clinica) WHERE ID(clinica) = $id_clinica RETURN clinica";
	$responseClinica = executeCypherQuery($queryClinica);

	if ($responseClinica) {
		$decodedResponseClinica = json_decode($responseClinica, true);

		if (isset($decodedResponseClinica['results'][0]['data'][0])) {
			$recordClinica = $decodedResponseClinica['results'][0]['data'][0];
			$clinica = $recordClinica['row']['clinica'];
			$nomeClinica = $clinica['nome'];
			$rua = $clinica['rua'];
			$numero = $clinica['numero'];
			$cep = $clinica['cep'];
			$bairro = $clinica['bairro'];
			$cidade = $clinica['cidade'];
			$estado = $clinica['estado'];
		}
	}
}

if($acao=='insert'){
	$valor_botao = 'Cadastrar';
}else if($acao=='update'){
	$valor_botao = 'Alterar';
}

?>
<form action="acao.consulta.php" method="POST">
    <table class="table table-condensed table-striped table-bordered table-hover">
        <?php
        echo '<input type="hidden" name="acao" value="'.$acao.'">
        <input type="hidden" name="id_consulta" value="'.$id_consulta.'">
        <input type="hidden" name="id_clinica" value="'.$id_clinica.'">
        <tr><td colspan="2" align="center"><h4>'.$valor_botao.' Consulta</h4></td></tr>';
        ?>
        <tr>
		<td>Paciente</td>
		<td>
			<select name="paciente">
				<option value="Selecione">Selecione</option>
				<?php
				$query = 'MATCH (p:Paciente) RETURN ID(p) AS id, p.nome AS nome';
				$response = executeCypherQuery($query);

				if ($response) {
					$decodedResponse = json_decode($response, true);

					if (isset($decodedResponse['results'][0]['data'])) {
						foreach ($decodedResponse['results'][0]['data'] as $record) {
							$paciente_id = $record['row']['id'];
							$paciente_nome = $record['row']['nome'];

							// Usando o índice como valor interno da opção
							echo '<option value="' . $paciente_id . '">' . $paciente_nome . '</option>';
						}
					}
				} else {
					echo '<option>Nenhum Paciente Cadastrado</option>';
				}
				?>
			</select>
		</td>

		</tr>
		<tr>
		<td>Médico</td>
		<td>
			<select name="medico">
				<option value="">Selecione</option>
				<?php
				$queryMedicos = 'MATCH (medico:Medico) RETURN ID(medico) AS node_id, medico.nome AS nome';
				$responseMedicos = executeCypherQuery($queryMedicos);

				if ($responseMedicos) {
					$decodedResponseMedicos = json_decode($responseMedicos, true);

					if (isset($decodedResponseMedicos['results'][0]['data'])) {
						foreach ($decodedResponseMedicos['results'][0]['data'] as $record) {
							$medico_node_id = $record['row']['node_id'];
							$medico_nome = $record['row']['nome'];

							echo '<option value="' . $medico_node_id . '">' . $medico_nome . '</option>';
						}
					}
				} else {
					echo '<option value="">Erro na consulta</option>';
				}
				?>
			</select>
		</td>
		</tr>
		<tr>
			<td>Data da Solicitação:</td>
			<td><input type="date" value="<?php echo $data_solicitacao?>" name="datasolicitacao"></td>
		</tr>				
        <tr>
			<td>Data da Consulta:</td>
			<td><input type="date" value="<?php echo $data_consulta?>" name="dataconsulta"></td>
		</tr>
		<tr>
			<td>Horário da Consulta</td>
			<td><input type="time" value="<?php echo $horario_consulta?>" name="horarioconsulta" size="30"></td>
		</tr>
        <tr>
			<td>Data de Confirmação:</td>
			<td><input type="date" value="<?php echo $data_confirmacao?>" name="dataconfirmacao"></td>
		</tr>
		<tr>
			<td>Horário de Confirmação</td>
			<td><input type="time" value="<?php echo $horario_confirmacao?>" name="horarioconfirmacao" size="30"></td>
		</tr>
		<tr>
			<td>Forma de contato</td>
			<td>
			<?php
				if($forma_contato=='telefone'){
				echo '<input type="radio" name="contato" value="telefone">Telefone
				<input type="radio" name="contato" value="whats">WhatsApp
				<input type="radio" name="contato" value="face">Facebook';
				}elseif($forma_contato=='whats'){
				echo '<input type="radio" name="contato" value="telefone">Telefone
				<input type="radio" name="contato" value="whats">WhatsApp
				<input type="radio" name="contato" value="face">Facebook';
				}else{
				echo '<input type="radio" name="contato" value="telefone">Telefone
				<input type="radio" name="contato" value="whats">WhatsApp
				<input type="radio" name="contato" value="face">Facebook';
				}
			?>
			</td>				
		</tr>
        <td>Forma de Pagamento</td>
            <td>
            <?php
            	if($forma_pagamento='dinheiro'){
            		echo '<input type="radio" name="pagamento" value="dinheiro">Dinheiro
            	<input type="radio" name="pagamento" value="debito">Débito
            	<input type="radio" name="pagamento" value="credito">Crédito';
            	}elseif($forma_pagamento='debito'){
            	echo '<input type="radio" name="pagamento" value="dinheiro" >Dinheiro
            	<input type="radio" name="pagamento" value="debito">Débito
            	<input type="radio" name="pagamento" value="credito">Crédito';
            	}else{
            		echo '<input type="radio" name="pagamento" value="dinheiro" >Dinheiro
            	<input type="radio" name="pagamento" value="debito" >Débito
            	<input type="radio" name="pagamento" value="credito">Crédito';
            	}
            ?>
            </td>
        <tr>
			<td>Observações</td>
			<td><textarea class="form-control" name="observacoes" rows="5" id="comment"><?php echo $observacao?></textarea></td>				
		</tr>
		<tr>
		<td>Clínica</td>
		<td>
			<select name="id_clinica">
				<option value="">Selecione</option>
				<?php
				$queryClinicas = 'MATCH (clinica:Clinica) RETURN clinica.id_clinica AS id, clinica.nome AS nome';
				$responseClinicas = executeCypherQuery($queryClinicas);
				
				if ($responseClinicas) {
					$decodedResponseClinicas = json_decode($responseClinicas, true);
				
					if (isset($decodedResponseClinicas['results'][0]['data'])) {
						foreach ($decodedResponseClinicas['results'][0]['data'] as $record) {
							$clinica_id = $record['row']['id'];
							$clinica_nome = $record['row']['nome'];
				
							if (!empty($clinica_id) && !empty($clinica_nome)) {
								echo '<option value="' . $clinica_id . '">' . $clinica_nome . '</option>';
							}
						}
					} else {
						echo '<option value="">Nenhuma Clínica Cadastrada</option>';
					}
				} else {
					echo '<option value="">Erro na consulta</option>';
				}
				?>
			</select>
		</td>
	</table>
	
	<input type="submit" name="cadastrar" value="<?php echo $valor_botao?> Consulta" class="btn btn-success">
</form>
<hr>
<table class="table table-condensed table-striped table-bordered table-hover" border="1px">
<tr>
	<td colspan="13" align="center"><h4>Consultas</h4></td>
</tr>
<tr align="center">
	<td>Nome Paciente</td>
	<td>Médico</td>
	<td>Data de Solicitação</td>
	<td>Data da Consulta</td>
	<td>Horário da Consulta</td>
	<td>Forma de contato</td>
	<td>Forma de Pagamento</td>
	<td>Valor</td>
	<td>Observações</td>
	<td>Clínica</td>
	<td>Ações</td>
</tr>
<tr>

<?php
$queryConsultas = "MATCH (p:Paciente)-[:REALIZOU]->(c:Consulta)-[:REALIZADA_EM]->(cl:Clinica)
MATCH (m:Medico)-[:TRABALHA]->(cl)
RETURN p.nome AS `Nome Paciente`, 
	m.nome AS Medico, 
	c.data_solicitacao AS `Data de Solicitacao`, 
	c.data_consulta AS `Data da Consulta`, 
	c.horario_consulta AS `Horario da Consulta`, 
	c.forma_contato AS `Forma de Contato`, 
	c.forma_pagamento AS `Forma de Pagamento`, 
	c.valor AS Valor, 
	c.observacoes AS Observacoes, 
	cl.nome AS Clinica";
	$responseConsultas = executeCypherQuery($queryConsultas);

	if ($responseConsultas) {
		$decodedResponse = json_decode($responseConsultas, true);
	
		if (isset($decodedResponse['results'][0]['data'])) {
			$data = $decodedResponse['results'][0]['data'];
	
			if (!empty($data)) {
				foreach ($data as $record) {
					$rowData = $record['row'];
	
					list($nomePaciente, $nomeMedico, $dataSolicitacao, $dataConsulta, $horarioConsulta, $formaContato, $formaPagamento, $valor, $observacoes, $nomeClinica) = $rowData;
	
					echo '<tr>';
					echo '<td>' . $nomePaciente . '</td>';
					echo '<td>' . $nomeMedico . '</td>';
					echo '<td>' . $dataSolicitacao . '</td>';
					echo '<td>' . $dataConsulta . '</td>';
					echo '<td>' . $horarioConsulta . '</td>';
					echo '<td>' . $formaContato . '</td>';
					echo '<td>' . $formaPagamento . '</td>';
					echo '<td>' . $valor . '</td>';
					echo '<td>' . $observacoes . '</td>';
					echo '<td>' . $nomeClinica . '</td>';
					echo '<td><a href="index.php?pg=consulta&id_update=' . $nomePaciente . '" class="btn btn-primary"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
			  <a href="acao.consulta.php?acao=delete&id_consulta=' . $nomePaciente . '" class="btn btn-danger" onclick="return confirm(\'Tem certeza que deseja excluir esta consulta?\');"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>';
					echo '</tr>';
				}
			} else {
				echo '<tr align="center"><td colspan="10">Nenhum registro para listar</td></tr>';
			}
		} else {
			echo '<tr align="center"><td colspan="10">Nenhum resultado encontrado</td></tr>';
		}
	} else {
		echo '<tr align="center"><td colspan="10">Erro na execução da consulta</td></tr>';
	}	
?>
</tr>
	</table>