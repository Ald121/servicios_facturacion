<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Example 2</title>
    {!! Html::style('css/factura/style.css') !!}
  </head>
  <body>
    <header>
    </header>
    <main>
<table>
        <tbody>
        <tr>
        <td style=" width: 380px;">
        <div style=" float: left;">
        <div id="client">
        <!-- <img src="../public/img/logo_fac.png" style="width: 290px;"> -->
        <p>{{$data['empresa']['nombre']}}</p>
        <p>Dir Matriz: {{$data['empresa']['direccion']}}</p>

        </div> 
    </div></td>
        <td class="unit">
    <div style="float: left;">
        <div id="client">
        <p>RUC/CI: {{$data['empresa']['ruc_ci']}}</p>
        <p>FACTURA No. {{$data['factura']->serie}}</p>
        <div id="clave_acceso">
        <!-- <img src="../public/temp.gif" > -->
        </div>
        </div> 
    </div>
      </td>
       </tr>
      <tr>
      <td>
      <p>Razón Social / Nombres y Apellidos: {{$data['cliente']['nombres']}}</p>
      </td>
      <td>
      <p>RUC / CI: {{$data['cliente']['ruc_ci']}}</p>
      <p>Fecha de Emisión: {{$data['factura']->fecha_creacion}}</p>
      <!-- <p>Guía de Remisión:</p> -->
      </td>
      </tr>
          </tbody>
  </table>
<h1>Detalles</h1>
      <table border="0" cellspacing="0" cellpadding="0" id="tabla_detalles">
        <thead>
          <tr>
            <th>Cod. Princial</th>
            <th >Cant.</th>
            <th >Descripción</th>
            <th >Precio Unitario</th>
            <th >Precio Total</th>
          </tr>
        </thead>
        <tbody>

        @foreach ($data['detalles'] as $item) 
          <tr>
            <td >{{ $item['codigo_prod'] }}</td>
            <td >{{ $item['cantidad_fac'] }}</td>
            <td >{{ $item['descripcion_corta'] }}</td>
            <td ></td>
            <td >{{ $item['precio'] }}</td>
            <td >{{ $item['total_fac'] }}</td>
          </tr>
        @endforeach

        </tbody>
        <tfoot>

        @foreach ($data['totales'] as $total) 
          <tr>
            <td colspan="4"></td>
            <td colspan="3">{{ $total['label'] }}</td>
            <td>{{ $total['valor'] }}</td>
          </tr>
        @endforeach
          
          
        </tfoot>
      </table>
      <div id="thanks">Thank you!</div>
      <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>