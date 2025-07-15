<?php

// la variable  SERVERURL contiene la URL del servidor donde se encuentra alojada la aplicación
// toca cambiar la url cuando se suba a un servidor, adicionalmente en el archivo config.js
// se debe cambiar la variable url a la misma URL del servidor, si no la aplicacion no funcionará correctamente

const SERVERURL = "http://localhost/proyectofinaljhon.shop/";

const COMPANY = "GESTION DE ANTEPROYECTO Y PROYECTOS DE GRADO AUTONONA DE NARIÑO";

const MONEDA = "$";

const STYLESCORREO = "<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 800px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .email-header {
            background:  #F8DC0B;
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        .email-header img {
            width: 180px;
            margin-bottom: 10px;
        }
        .email-header h2 {
            margin: 0;
            color: #034873;
            font-size: 20px;
        }
        .email-body {
            padding: 20px;
            background: #ffffff;
            color: #333;
            text-align: justify;
        }
        .email-body p{
            color:black !important;
        }

        .email-body h3{
            font-size: 17px;
        }
        .credentials {
            background: #034873;
            padding: 20px;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            margin: 20px 0;
        }
        .credentials ul {
            list-style: none;
            padding: 0;
        }
        .credentials li {
            margin: 8px 0;
        }
        .highlight {
            color: #d9534f;
            font-weight: bold;
        }
        .login-button {
            display: inline-block;
            background: #F8DC0B;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: 0.3s ease;
        }
        .login-button:hover {
            background: #034873;
            color: white;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: gray;
            padding: 15px;
            background: #f9f9f9;
            border-top: 1px solid #ddd;
        }
    </style>";

date_default_timezone_set("America/Bogota");