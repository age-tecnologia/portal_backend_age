<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-mail b2b</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
        *, :before, :after {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
            overflow-x: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

    </style>

</head>
<body>
<div id="container" style="width: 100vw; background-color: #e0e1e7">
    <div id="card" style="width: 40%; height: 100%; background-color: #fff; margin: 0 auto">
        <div id="header" style="width: 100%; height: 23vh; background-image: url('https://i.ibb.co/1009k2N/Prancheta-1-copiar-5.jpg'); background-size: cover; background-repeat: no-repeat">

        </div>
        <div id="main" style="padding: 3vh 2vw 3vh 4vw; margin: 0 auto">
            <div id="section">
                <h1 style="font-size: 1.4rem; color: #104176; font-weight: 600">Prezado {{ $client }}, como vai?</h1>
            </div>
            <br>
            <div id="section-2">
                <p style="color: #82b4ff; font-weight: 500; font-size: .9rem">
                    Primeira, gostariámos de lhe dar as boas-vindas pela <br>
                    sua inclusão no nosso portfólio de clientes <b style="color: #104176">Age Empresas.</b>
                </p>
                <br>
                <p style="color: #82b4ff; font-weight: 500; font-size: .9rem">
                    A partir de agora, você terá acesso a uma experiência incrível <br>
                    na utilização da nossa <b style="color: #104176">solução de link dedicado</b> ou <b style="color: #104176">banda larga</b> <br>
                    <b style="color: #104176">de alta capacidade.</b>
                </p>
            </div>
            <div id="section-3" style="margin: 4vh 0">
                <div id="card-info" style="margin: 0 auto; width: 80%; background-color: #B8D1F6; padding: 2vh 1vw">
                    <p style="color: #104176; font-weight: 700; font-size: .8rem">Estes são seus dados: </p>
                    <br>
                    <p style="color: #104176; font-weight: 700; font-size: .8rem">Número do Contrato do cliente: {{ $contract }}</p>
                    <br>
                    <p style="color: #104176; font-weight: 700; font-size: .8rem">Vigência do Contrato: {{ $vigence  }} </p>
                    <br>
                    <p style="color: #104176; font-weight: 700; font-size: .8rem">Descritivo do Serviços (Link ou Banda Larga): {{ $lorem }}</p>
                </div>
            </div>
            <div id="section-4" style="display: flex; flex-direction: row; gap: 1vw">
                <img src="https://i.ibb.co/WVTWB4b/Prancheta-1-copiar.jpg" alt="seta" style="width: 2.5vw; height: auto">
                <p style="color: #104176; font-weight: 500; font-size: .8rem">Caso necessite de atendimento, estaremos <br> sempre prontos para atendê-lo através de nossos canais:</p>
            </div>
            <div id="section-5" style="width: 100%; margin: 3vh 0">
                <div id="card-2" style="width: 80%; margin: 0 auto; border: 2px solid #B8D1F6; padding: 2vh 1vw">
                    <div id="item" style="margin-bottom: 2vh; display: flex; flex-direction: row; gap: 1vw; align-items: center">
                        <img src="https://i.ibb.co/R9CHJhn/Prancheta-1-copiar-4.jpg" alt="suporte" style="width: 2vw; height: auto">
                        <span style="color: #B8D1F6; font-size: .8rem"><b>suporte.empresas@agetelecom.com.br</b></span>
                    </div>
                    <div id="item" style="margin-bottom: 2vh; display: flex; flex-direction: row; gap: 1vw; align-items: center">
                        <img src="https://i.ibb.co/Ttx80VH/Prancheta-1-copiar-3.jpg" alt="email" style="width: 2vw; height: auto">
                        <span style="color: #B8D1F6; font-size: .8rem"><b>noc@agetelecom.com.br</b></span>
                    </div>
                    <div id="item" style="display: flex; flex-direction: row; gap: 1vw; align-items: center">
                        <img src="https://i.ibb.co/6B8KvMs/Prancheta-1-copiar-2.jpg" alt="whatsapp" style="width: 2vw; height: auto">
                        <span style="color: #B8D1F6; font-size: .8rem"><b>Telefone: 4040-4498</b></span>
                    </div>
                </div>
            </div>
            <div id="section-6" style="margin: 8vh 0 12vh 0">
                <p style="color: #B8D1F6; text-align: center">Atenciosamente,</p>
                <p style="color: #B8D1F6; text-align: center"><b>Age Empresas</b></p>
            </div>

        </div>
        <div id="footer"
             style="background-image: url('https://i.ibb.co/WgDLGcc/rodap.jpg');
                 background-repeat: no-repeat; background-size: cover; width: 100%; height: 6vh ">

        </div>
    </div>

</div>
</body>
</html>
