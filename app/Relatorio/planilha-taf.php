<?php

    require_once '../../lib/Database/Connection.php';
    require_once '../Model/Turma.php';
    require_once '../Model/Discente.php';
 

    $id    = $_GET['id'];

    $turma     = Turma::buscaTurma($id);
    $discentes = Discente::listarDiscentes($id);

    $primeiro_dia  = date('d/m/Y', strtotime($turma[0]['inicio']));
    $segundo_dia   = date('d/m/Y', strtotime($turma[0]['inicio'] . ' +1 day'));
    $terceiro_dia  = date('d/m/Y', strtotime($turma[0]['termino']));
   
    $eap = $turma[0]['turma'] . ' - ' .  $primeiro_dia . ' a ' . $terceiro_dia;


    function calculaIdade($dn)
    {
        $dataNascimento = new DateTime($dn);
        $hoje = new DateTime();
        $idade = $hoje->diff($dataNascimento)->y;
        return $idade;
    }

    function tabelaTaf($idade)
    {
        //verificar essa fórmula
        $resultado = ($idade / 5) - 2;
        return (int) $resultado; // trunca a parte decimal
    }
    

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Assinatura</title>

    <!-- CSS dos ícones Bootstrap -->
    <link rel="stylesheet" href="../../public/assets/bootstrap/bootstrap-icons-1.11.3/font/bootstrap-icons.css">


    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../../public/assets/bootstrap/css/bootstrap.min.css">


    <style>

        * {
            font-size: 10px;
        }
        
        body {
            background-color: gray;
            padding: 10px 20px;
            display: flex;
            justify-content: center;

        }

        .container__relatorio {
            box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;
            background-color: #fff;
            padding: 10px;
            width: 1122px;
            min-height: 500px;
            /*min-height: 1122px;*/
            padding: 20px 10px;
        }

        .container_relatorio__cabecalho {
            text-align: center;
            margin-top: 5px;
        }

        .container_relatorio__cabecalho p{
            font-weight: 700;
            line-height: 10px;
        }

        .container__relatorio td,
        .container__relatorio th {
            border: 1px solid gray;
            vertical-align: middle;
            padding: 1px 3px;
        }

        .container__relatorio table th {
            background-color: lightgrey;
        }

        #btn-imprimir {
            border-radius: 4px;
            width: 50px;
            height: 50px;
            position: fixed;
            margin-top: 10px;
            margin-left: 10px;
            display: grid;
            place-items: center;
            background-color: #0459a9;
            color: #fff;
            border: 2px solid #1a3697;
        }

        #btn-imprimir:hover {
            background-color: #1a3697;
        }

        .th__vertical {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
        }


        .pontos {
            width: 35px;
        }

        .col__conceito {
            background-color:rgb(228, 239, 250) !important;
        }


        .assinatura {
            text-align: right;
            margin-top: 70px;
        }


        @media print {

            body {
                /*imprimir cor*/
                -webkit-print-color-adjust: exact; /* Chrome, Safari */
                print-color-adjust: exact;         /* Firefox */
            }

            .container__relatorio {
                box-shadow: none;
                padding: 5px;
            }

            .container__no_print {
                display: none;
            }
        }

        @page {
            size: auto;
            margin: 0mm;
            size: A4 landscape;
        }
        

    </style>

</head>
<body>

        <div class="container__no_print">

            <button id="btn-imprimir"  onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" fill="currentColor"><path d="M640-640v-120H320v120h-80v-200h480v200h-80Zm-480 80h640-640Zm560 100q17 0 28.5-11.5T760-500q0-17-11.5-28.5T720-540q-17 0-28.5 11.5T680-500q0 17 11.5 28.5T720-460Zm-80 260v-160H320v160h320Zm80 80H240v-160H80v-240q0-51 35-85.5t85-34.5h560q51 0 85.5 34.5T880-520v240H720v160Zm80-240v-160q0-17-11.5-28.5T760-560H200q-17 0-28.5 11.5T160-520v160h80v-80h480v80h80Z"/></svg>
            </button>
        
        </div>

    <div class="container__relatorio">
        
        <div class="container_relatorio__cabecalho">
            <p>SECRETARIA DA SEGURANÇA PÚBLICA</p>
            <p>POLÍCIA MILITAR DO ESTADO DE SÃO PAULO</p>

            <p>CPI-7 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; GT</p>
            <br>
            <p><u><?php echo $eap ?></u></p>
            <br>
            <p>TESTE DE APTIDÃO FÍSICA</p>
            <br>
            <p>Data do teste: <?php echo $segundo_dia; ?></p>
        </div>


        <table class="table table-borded">
            <tr>
                <th>Nº</th>
                <th>PT/GRAD</th>
                <th>RE</th>
                <th>NOME</th>
                <th>OPM</th>

                <th>NASC</th>
                <th class="th__vertical" style="width: 10px;">Idade</th>
                <th class="th__vertical" style="width: 10px;">Tabela</th>
                <th class="th__vertical" style="width: 10px;">Sexo</th>
                <th class="th__vertical" style="width: 10px;">Sexo</th>
                <th class="th__vertical pontos">Barra</th>
                <th class="th__vertical pontos">Pontos</th>
                <th class="th__vertical pontos">Apoio</th>
                <th class="th__vertical pontos">Pontos</th>
                <th class="th__vertical pontos">Abdm</th>
                <th class="th__vertical pontos">Pontos</th>
                <th class="th__vertical pontos">50 m</th>
                <th class="th__vertical pontos">Pontos</th>
                <th class="th__vertical pontos">2400</th>
                <th class="th__vertical pontos">Pontos</th>
                <th class="th__vertical pontos">Total</th>
                <th class="th__vertical" style="width: 40px;">Conc</th>
                <th class="th__vertical" style="width: 45px;">Peso</th>
                <th class="th__vertical" style="width: 45px;">Circunf</th>
            </tr>


        <?php

            foreach($discentes as $i => $discente) {
        ?>
            <tr>
                <td><?php echo  $i+1 ?></td>
                <td><?php if($discente['pt_gr']==='SD PM - 1C' or $discente['pt_gr']==='SD PM - 2C'){ echo 'SD PM'; }else{ echo  $discente['pt_gr'];}?></td>
                <td><?php echo  $discente['re'] . '-' . $discente['dg_re'];?></td>
                <td><?php echo  $discente['nome'];?></td>
                <td><?php echo  $discente['opm'];?></td>

                <td><?php echo date('d/m/Y', strtotime($discente['data_nascimento']));?></td>
                <td><?php $idade = calculaIdade($discente['data_nascimento']);  echo $idade; ?></td>
                <td><?php echo tabelaTaf($idade); ?></td>

                <td><?php echo  $discente['sexo'];?></td>
                <td><?php if ($discente['sexo'] == 'M') { echo '0'; } else { echo '1';};?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="col__conceito"></td>
                <td></td>
                <td></td>

            </tr>           

        <?php
            }
        ?>

        </table>


        <div class="assinatura">
            <p><strong>PROFESSOR ED. FÍSICA</strong></p>
        </div>

    </div>   
    
</body>

</html>