<?php
require_once('inc.connect.php');

(isset($_POST['ds_especialidade']) and !empty($_POST['ds_especialidade'])) ? $ds_especialidade = $_POST['ds_especialidade'] : $erro = TRUE;
(isset($_POST['tipo_especialidade']) and !empty($_POST['tipo_especialidade'])) ? $tipo_especialidade = $_POST['tipo_especialidade'] : $erro = TRUE;
(isset($_POST['ie_ativo']) and !empty($_POST['ie_ativo'])) ? $ie_ativo = $_POST['ie_ativo'] : $erro = TRUE;
(isset($_POST['custo_medio']) and !empty($_POST['custo_medio'])) ? $custo_medio = $_POST['custo_medio'] : $erro = TRUE;
(isset($_REQUEST['acao']) and !empty($_REQUEST['acao'])) ? $acao = $_REQUEST['acao'] : $erro = TRUE;
(isset($_REQUEST['id_especialidade']) and !empty($_REQUEST['id_especialidade'])) ? $id_especialidade = $_REQUEST['id_especialidade'] : $erro = TRUE;
(isset($_POST['id_especialidade']) and !empty($_POST['id_especialidade'])) ? $id_especialidade = $_POST['id_especialidade'] : $erro = TRUE;

$acao = $_POST['acao'] ?? '';

switch ($acao) {
    case 'insert':
        $query = 'CREATE (e:Especialidade { 
                   ds_especialidade: "'.$ds_especialidade.'", 
                   tipo_especialidade: "'.$tipo_especialidade.'", 
                   ie_ativo: "'.$ie_ativo.'", 
                   custo_medio: "'.$custo_medio.'",
                   imagem: "'.$nome_imagem.'" 
                }) 
            RETURN e';

        $parameters = [
            'ds_especialidade' => $ds_especialidade,
            'tipo_especialidade' => $tipo_especialidade,
            'ie_ativo' => $ie_ativo,
            'custo_medio' => $custo_medio,
            'nome_imagem' => $nome_imagem
        ];

        $result = executeCypherQuery($query, $parameters);

        if ($result) {
            $msg = 'cadastrado';
        } else {
            $msg = 'erro';
        }
        break;

        case 'update':
            echo "PASSANDO POR UPDATE";
            echo $id_especialidade;
            $query = 'MATCH (e:Especialidade) WHERE e.ds_especialidade = "' . $id_especialidade . '"
                      SET e.ds_especialidade = "'.$ds_especialidade.'", 
                          e.tipo_especialidade = "'.$tipo_especialidade.'", 
                          e.ie_ativo = "'.$ie_ativo.'", 
                          e.custo_medio = "'.$custo_medio.'"';
            echo $query;
            $result = executeCypherQuery($query, [
                'ds_especialidade' => $ds_especialidade,
                'tipo_especialidade' => $tipo_especialidade,
                'ie_ativo' => $ie_ativo,
                'custo_medio' => $custo_medio,
                'logo' => $logo
            ]);
        
            if ($query) {
                $msg = 'alterado';
            } else {
                $msg = 'erro';
            }
            break;

    case 'delete':
        if ($_GET['acao'] == 'delete' && isset($_GET['ds_especialidade'])) {
            $ds_especialidade = $_GET['ds_especialidade'];
        $query = 'MATCH (e:Especialidade {ds_especialidade: $id_especialidade}) DETACH DELETE e';
        $result = executeCypherQuery($query);

        if ($result) {
            $msg = 'deletado';
        } else {
            $msg = 'erro';
        }
    }
        break;
}

header("location:index.php?pg=especialidade&msg=" . $msg);
?>