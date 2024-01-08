<?php
require_once('inc.connect.php');

(isset($_REQUEST['acao']) and !empty($_REQUEST['acao'])) ? $acao = $_REQUEST['acao'] : $erro = TRUE;
(isset($_POST['id_consulta']) and !empty($_POST['id_consulta'])) ? $id_consulta = $_POST['id_consulta'] : $erro = TRUE;
(isset($_POST['id_clinica']) and !empty($_POST['id_clinica'])) ? $id_clinica = $_POST['id_clinica'] : $erro = TRUE;
(isset($_POST['paciente']) and !empty($_POST['paciente'])) ? $paciente = $_POST['paciente'] : $erro = TRUE;
(isset($_POST['medico']) and !empty($_POST['medico'])) ? $medico = $_POST['medico'] : $erro = TRUE;
(isset($_POST['datasolicitacao']) and !empty($_POST['datasolicitacao'])) ? $datasolicitacao = $_POST['datasolicitacao'] : $erro = TRUE;
(isset($_POST['dataconsulta']) and !empty($_POST['dataconsulta'])) ? $dataconsulta = $_POST['dataconsulta'] : $erro = TRUE;
(isset($_POST['horarioconsulta']) and !empty($_POST['horarioconsulta'])) ? $horarioconsulta = $_POST['horarioconsulta'] : $erro = TRUE;
(isset($_POST['dataconfirmacao']) and !empty($_POST['dataconfirmacao'])) ? $dataconfirmacao = $_POST['dataconfirmacao'] : $erro = TRUE;
(isset($_POST['horarioconfirmacao']) and !empty($_POST['horarioconfirmacao'])) ? $horarioconfirmacao = $_POST['horarioconfirmacao'] : $erro = TRUE;
(isset($_POST['contato']) and !empty($_POST['contato'])) ? $contato = $_POST['contato'] : $erro = TRUE;
(isset($_POST['pagamento']) and !empty($_POST['pagamento'])) ? $pagamento = $_POST['pagamento'] : $erro = TRUE;
(isset($_POST['troco']) and !empty($_POST['troco'])) ? $troco = $_POST['troco'] : $erro = TRUE;
(isset($_POST['observacoes']) and !empty($_POST['observacoes'])) ? $observacoes = $_POST['observacoes'] : $erro = TRUE;

$valor = 1;
switch ($acao) {
    case 'insert':
        $query = 'CREATE (c:Consulta { id_paciente: $paciente, 
                                        id_medico: $medico,
                                        data_solicitacao: $datasolicitacao,
                                        data_consulta: $dataconsulta,
                                        horario_consulta: $horarioconsulta,
                                        data_confirmacao: $dataconfirmacao,
                                        horario_confirmacao: $horarioconfirmacao,
                                        forma_contato: $contato,
                                        observacao: $observacoes,
                                        forma_pagamento: $pagamento,
                                        valor: $valor,
                                        id_clinica: $id_clinica })';

        $parameters = [
            'paciente' => $paciente,
            'medico' => $medico,
            'datasolicitacao' => $datasolicitacao,
            'dataconsulta' => $dataconsulta,
            'horarioconsulta' => $horarioconsulta,
            'dataconfirmacao' => $dataconfirmacao,
            'horarioconfirmacao' => $horarioconfirmacao,
            'contato' => $contato,
            'observacoes' => $observacoes,
            'pagamento' => $pagamento,
            'valor' => $valor,
            'id_clinica' => $id_clinica,
        ];

        $result = executeCypherQuery($query, $parameters);

        if ($result) {
            $msg = 'cadastrado';
        } else {
            $msg = 'erro';
        }
        break;

    case 'update':
        $query = 'MATCH (c:Consulta {id_consulta: $id_consulta}) SET c.id_paciente = $paciente, 
                                                                c.id_medico = $medico, 
                                                                c.data_solicitacao = $datasolicitacao, 
                                                                c.data_consulta = $dataconsulta,
                                                                c.horario_consulta = $horarioconsulta, 
                                                                c.data_confirmacao = $dataconfirmacao, 
                                                                c.horario_confirmacao = $horarioconfirmacao,
                                                                c.forma_contato = $contato,
                                                                c.observacao = $observacoes,
                                                                c.forma_pagamento = $pagamento,
                                                                c.valor = $valor,
                                                                c.id_clinica = $id_clinica';

        $parameters = [
            'id_consulta' => $id_consulta,
            'paciente' => $paciente,
            'medico' => $medico,
            'datasolicitacao' => $datasolicitacao,
            'dataconsulta' => $dataconsulta,
            'horarioconsulta' => $horarioconsulta,
            'dataconfirmacao' => $dataconfirmacao,
            'horarioconfirmacao' => $horarioconfirmacao,
            'contato' => $contato,
            'observacoes' => $observacoes,
            'pagamento' => $pagamento,
            'valor' => $valor,
            'id_clinica' => $id_clinica,
        ];

        $result = executeCypherQuery($query, $parameters);

        if ($result) {
            $msg = 'alterado';
        } else {
            $msg = 'erro';
        }
        break;

    case 'delete':
        (isset($_GET['id_consulta']) and !empty($_GET['id_consulta'])) ? $id_consulta = $_GET['id_consulta'] : $erro = TRUE;

        $query = 'MATCH (c:Consulta {id_consulta: $id_consulta}) DELETE c';

        $parameters = [
            'id_consulta' => $id_consulta,
        ];

        $result = executeCypherQuery($query, $parameters);

        if ($result) {
            $msg = 'deletado';
        } else {
            $msg = 'erro';
        }
        break;
}

header("location:index.php?pg=consulta&msg=" . $msg);