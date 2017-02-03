<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{$data['factura']->serie}}</title>
    {!! Html::style('css/factura/style.css') !!}
  </head>
  <body>
    <header>
    </header>
    <main>

  <div id="container">
        <div class="left">
        <p>PROFORMA No. {{$data['factura']->serie}}</p>
        <p>{{$data['empresa']['nombre']}}</p>
        <p>RUC: {{$data['empresa']['ruc_ci']}}</p>
        <p>Dir Matriz: {{$data['empresa']['direccion']}}</p>
        </div> 
        <div style="    width: 50%;   float: right;">
        <p>RUC / CI: {{$data['cliente']['ruc_ci']}}</p>
        <p>Razón Social / Nombres y Apellidos: {{$data['cliente']['nombres']}}</p>
        <p>FECHA Y HORA DE EMISION: {{$data['factura']->fecha_creacion}}</p>
      </div>
  </div>
  <br><br><br><br><br><br><br><br><br><br>
<h1>Detalles</h1>
      <table border="0" cellspacing="0" cellpadding="0" id="tabla_detalles">
        <thead>
          <tr>
            <th>Cod. Princial</th>
            <th >Cant.</th>
            <th colspan="2">Descripción</th>
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
            <td colspan="2"></td>
            <td colspan="3">{{ $total['label'] }}</td>
            <td>{{ $total['valor'] }}</td>
          </tr>
        @endforeach
          
          
        </tfoot>
      </table>
    </main>
  </body>
</html>